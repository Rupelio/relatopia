<?php

namespace App\Http\Controllers;

use App\Models\Relacionamento;
use App\Models\RelacionamentoItem;
use App\Models\Sentimento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterfaceUsuarioController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $estatisticasRelacionamento = RelacionamentoItem::estatisticasPorUsuario(Auth::id());
        $estatisticasSentimentos = Sentimento::estatisticaPorUsuario(Auth::id());
        $sentimentoHoje = Sentimento::sentimentoHoje(Auth::id());
        $ultimoSentimento = Sentimento::ultimoSentimento(Auth::id());
        return view('interface.dashboard', [
            'estatisticas' => [
                'relacionamento' => $estatisticasRelacionamento,
                'sentimentos' => [
                    'total' => $estatisticasSentimentos['total'],
                    'hoje' => $sentimentoHoje,
                    'ultimo' => $ultimoSentimento
                ]
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

        return view('interface.dashboard', [
            'estatisticas' => [
                'relacionamento' => $estatisticasRelacionamento,
                'sentimentos' => [
                    'total' => $estatisticasSentimentos['total'],
                    'hoje' => $sentimentoHoje,
                    'ultimo' => $ultimoSentimento,
                ]
            ],
            'relacionamento' => $relacionamento,
            'somenteLeitura' => true
        ]);
    }
}
