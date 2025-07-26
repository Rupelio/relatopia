<?php

namespace App\Http\Controllers;

use App\Models\Relacionamento;
use App\Models\RelacionamentoItem;
use App\Models\Sentimento;
use App\Models\ListaDesejo;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterfaceUsuarioController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();
        $estatisticasRelacionamento = RelacionamentoItem::estatisticasPorUsuario($user->id);
        $estatisticasSentimentos = Sentimento::estatisticaPorUsuario($user->id);
        $sentimentoHoje = Sentimento::sentimentoHoje($user->id);
        $ultimoSentimento = Sentimento::ultimoSentimento($user->id);

        // Estatísticas da Lista de Desejos
        $listaDesejos = [
            'total' => $user->listaDesejos()->count(),
            'comprados' => $user->listaDesejos()->where('comprado', true)->count(),
            'pendentes' => $user->listaDesejos()->where('comprado', false)->count()
        ];

        // Estatísticas do Calendário
        $relacionamento = Relacionamento::where(function($query) use ($user) {
            $query->where('user_id_1', $user->id)
                  ->orWhere('user_id_2', $user->id);
        })->where('status', 'aceito')->first();

        $eventosPessoais = Evento::paraUsuario($user->id)->pessoais()->count();
        $eventosPessoaisFuturos = Evento::paraUsuario($user->id)->pessoais()->futuros()->count();

        $eventosCompartilhados = 0;
        $eventosCompartilhadosFuturos = 0;

        if ($relacionamento) {
            $eventosCompartilhados = Evento::compartilhadosDoRelacionamento($relacionamento->id)->count();
            $eventosCompartilhadosFuturos = Evento::compartilhadosDoRelacionamento($relacionamento->id)->futuros()->count();
        }

        $eventos = [
            'eventos_pessoais' => $eventosPessoais,
            'eventos_pessoais_futuros' => $eventosPessoaisFuturos,
            'eventos_compartilhados' => $eventosCompartilhados,
            'eventos_compartilhados_futuros' => $eventosCompartilhadosFuturos,
            'total_eventos' => $eventosPessoais + $eventosCompartilhados,
            'total_eventos_futuros' => $eventosPessoaisFuturos + $eventosCompartilhadosFuturos
        ];

        return view('interface.dashboard', [
            'estatisticas' => [
                'relacionamento' => $estatisticasRelacionamento,
                'sentimentos' => [
                    'total' => $estatisticasSentimentos['total'],
                    'hoje' => $sentimentoHoje,
                    'ultimo' => $ultimoSentimento
                ],
                'lista_desejos' => $listaDesejos,
                'eventos' => $eventos
            ]
        ]);
    }
    public function dashboardParceiro($relacionamentoId){
        $user = Auth::user();
        $relacionamento = Relacionamento::findOrFail($relacionamentoId);

        // Verifica se o usuário faz parte do relacionamento
        if ($relacionamento->user_id_1 !== $user->id && $relacionamento->user_id_2 !== $user->id) {
            abort(403);
        }

        // Descobre o parceiro
        $parceiroId = $relacionamento->user_id_1 === $user->id ? $relacionamento->user_id_2 : $relacionamento->user_id_1;

        $estatisticasRelacionamento = RelacionamentoItem::estatisticasPorUsuario($parceiroId);
        $estatisticasSentimentos = Sentimento::estatisticaPorUsuario($parceiroId);
        $sentimentoHoje = Sentimento::sentimentoHoje($parceiroId);
        $ultimoSentimento = Sentimento::ultimoSentimento($parceiroId);

        // Estatísticas da Lista de Desejos do parceiro
        $listaDesejos = [
            'total' => ListaDesejo::where('usuario_id', $parceiroId)->count(),
            'comprados' => ListaDesejo::where('usuario_id', $parceiroId)->where('comprado', true)->count(),
            'pendentes' => ListaDesejo::where('usuario_id', $parceiroId)->where('comprado', false)->count()
        ];

        return view('interface.dashboard', [
            'estatisticas' => [
                'relacionamento' => $estatisticasRelacionamento,
                'sentimentos' => [
                    'total' => $estatisticasSentimentos['total'],
                    'hoje' => $sentimentoHoje,
                    'ultimo' => $ultimoSentimento,
                ],
                'lista_desejos' => $listaDesejos
            ],
            'relacionamento' => $relacionamento,
            'somenteLeitura' => true
        ]);
    }
}
