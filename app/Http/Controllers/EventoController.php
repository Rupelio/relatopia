<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\Relacionamento;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EventoController extends Controller
{
    /**
     * Listar eventos do usuário (pessoais + compartilhados do relacionamento)
     */
    public function index(Request $request)
    {
        $usuario = Auth::user();

        // Buscar eventos pessoais
        $eventosPessoais = Evento::paraUsuario($usuario->id)
            ->pessoais()
            ->orderBy('data_evento', 'asc');

        // Buscar eventos compartilhados do relacionamento
        $eventosCompartilhados = collect();
        $relacionamento = Relacionamento::where(function($query) use ($usuario) {
            $query->where('user_id_1', $usuario->id)
                  ->orWhere('user_id_2', $usuario->id);
        })->where('status', 'ativo')->first();

        if ($relacionamento) {
            $eventosCompartilhados = Evento::compartilhadosDoRelacionamento($relacionamento->id)
                ->orderBy('data_evento', 'asc');
        }

        // Filtros opcionais
        if ($request->has('categoria') && $request->categoria !== 'todas') {
            $eventosPessoais->porCategoria($request->categoria);
            $eventosCompartilhados->porCategoria($request->categoria);
        }

        if ($request->has('mes') && $request->mes) {
            $dataInicio = Carbon::createFromFormat('Y-m', $request->mes)->startOfMonth();
            $dataFim = Carbon::createFromFormat('Y-m', $request->mes)->endOfMonth();

            $eventosPessoais->whereBetween('data_evento', [$dataInicio, $dataFim]);
            $eventosCompartilhados->whereBetween('data_evento', [$dataInicio, $dataFim]);
        }

        // Combinar e ordenar todos os eventos
        $eventos = $eventosPessoais->get()
            ->merge($eventosCompartilhados->get())
            ->sortBy('data_evento')
            ->values();

        return response()->json([
            'success' => true,
            'eventos' => $eventos,
            'total' => $eventos->count()
        ]);
    }

    /**
     * Listar apenas eventos pessoais do usuário
     */
    public function eventosPessoais(Request $request)
    {
        $usuario = Auth::user();

        $eventos = Evento::paraUsuario($usuario->id)
            ->pessoais()
            ->orderBy('data_evento', 'asc');

        // Filtros opcionais
        if ($request->has('categoria') && $request->categoria !== 'todas') {
            $eventos->porCategoria($request->categoria);
        }

        if ($request->has('mes') && $request->mes) {
            $dataInicio = Carbon::createFromFormat('Y-m', $request->mes)->startOfMonth();
            $dataFim = Carbon::createFromFormat('Y-m', $request->mes)->endOfMonth();
            $eventos->whereBetween('data_evento', [$dataInicio, $dataFim]);
        }

        $eventos = $eventos->get();

        return response()->json([
            'success' => true,
            'eventos' => $eventos,
            'total' => $eventos->count(),
            'tipo' => 'pessoais'
        ]);
    }

    /**
     * Listar apenas eventos compartilhados do relacionamento
     */
    public function eventosCompartilhados(Request $request)
    {
        $usuario = Auth::user();

        $relacionamento = Relacionamento::where(function($query) use ($usuario) {
            $query->where('user_id_1', $usuario->id)
                  ->orWhere('user_id_2', $usuario->id);
        })->where('status', 'ativo')->first();

        if (!$relacionamento) {
            return response()->json([
                'success' => true,
                'eventos' => [],
                'total' => 0,
                'tipo' => 'compartilhados',
                'message' => 'Você precisa estar em um relacionamento para ver eventos compartilhados.'
            ]);
        }

        $eventos = Evento::compartilhadosDoRelacionamento($relacionamento->id)
            ->orderBy('data_evento', 'asc');

        // Filtros opcionais
        if ($request->has('categoria') && $request->categoria !== 'todas') {
            $eventos->porCategoria($request->categoria);
        }

        if ($request->has('mes') && $request->mes) {
            $dataInicio = Carbon::createFromFormat('Y-m', $request->mes)->startOfMonth();
            $dataFim = Carbon::createFromFormat('Y-m', $request->mes)->endOfMonth();
            $eventos->whereBetween('data_evento', [$dataInicio, $dataFim]);
        }

        $eventos = $eventos->get();

        return response()->json([
            'success' => true,
            'eventos' => $eventos,
            'total' => $eventos->count(),
            'tipo' => 'compartilhados'
        ]);
    }

    /**
     * Criar novo evento
     */
    public function store(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data_evento' => 'required|date',
            'tipo' => 'required|in:pessoal,compartilhado',
            'categoria' => 'required|in:aniversario,encontro,viagem,comemoração,compromisso,outro',
            'notificar_email' => 'boolean',
            'notificar_minutos_antes' => 'integer|min:5|max:10080' // 5 min a 7 dias
        ]);

        $dadosEvento = $request->all();
        $dadosEvento['usuario_id'] = $usuario->id;
        $dadosEvento['relacionamento_id'] = null;

        // Se for evento compartilhado, verificar se tem relacionamento ativo
        if ($request->tipo === 'compartilhado') {
            $relacionamento = Relacionamento::where(function($query) use ($usuario) {
                $query->where('user_id_1', $usuario->id)
                      ->orWhere('user_id_2', $usuario->id);
            })->where('status', 'ativo')->first();

            if (!$relacionamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você precisa ter um relacionamento ativo para criar eventos compartilhados.'
                ], 400);
            }

            $dadosEvento['relacionamento_id'] = $relacionamento->id;
        }

        $evento = Evento::create($dadosEvento);

        return response()->json([
            'success' => true,
            'message' => 'Evento criado com sucesso!',
            'evento' => $evento
        ]);
    }

    /**
     * Exibir evento específico
     */
    public function show($id)
    {
        $usuario = Auth::user();

        $evento = Evento::findOrFail($id);

        // Verificar se o usuário tem permissão para ver este evento
        if ($evento->usuario_id !== $usuario->id &&
            (!$evento->relacionamento || !$this->usuarioTemAcessoAoRelacionamento($usuario, $evento->relacionamento))) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para ver este evento.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'evento' => $evento
        ]);
    }

    /**
     * Atualizar evento
     */
    public function update(Request $request, $id)
    {
        $usuario = Auth::user();
        $evento = Evento::findOrFail($id);

        // Verificar se o usuário pode editar este evento (apenas o criador)
        if ($evento->usuario_id !== $usuario->id) {
            return response()->json([
                'success' => false,
                'message' => 'Você só pode editar eventos que criou.'
            ], 403);
        }

        $request->validate([
            'titulo' => 'string|max:255',
            'descricao' => 'nullable|string',
            'data_evento' => 'date',
            'tipo' => 'in:pessoal,compartilhado',
            'categoria' => 'in:aniversario,encontro,viagem,comemoração,compromisso,outro',
            'notificar_email' => 'boolean',
            'notificar_minutos_antes' => 'integer|min:5|max:10080'
        ]);

        $dadosEvento = $request->only([
            'titulo', 'descricao', 'data_evento', 'tipo', 'categoria',
            'notificar_email', 'notificar_minutos_antes'
        ]);

        // Se mudou para compartilhado, verificar relacionamento
        if ($request->tipo === 'compartilhado' && $evento->tipo !== 'compartilhado') {
            $relacionamento = Relacionamento::where(function($query) use ($usuario) {
                $query->where('user_id_1', $usuario->id)
                      ->orWhere('user_id_2', $usuario->id);
            })->where('status', 'ativo')->first();

            if (!$relacionamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você precisa ter um relacionamento ativo para criar eventos compartilhados.'
                ], 400);
            }

            $dadosEvento['relacionamento_id'] = $relacionamento->id;
        } elseif ($request->tipo === 'pessoal') {
            $dadosEvento['relacionamento_id'] = null;
        }

        // Resetar notificação se mudou a data/hora
        if ($request->has('data_evento') && $request->data_evento !== $evento->data_evento->format('Y-m-d H:i:s')) {
            $dadosEvento['notificacao_enviada'] = false;
        }

        $evento->update($dadosEvento);

        return response()->json([
            'success' => true,
            'message' => 'Evento atualizado com sucesso!',
            'evento' => $evento
        ]);
    }

    /**
     * Deletar evento
     */
    public function destroy($id)
    {
        $usuario = Auth::user();
        $evento = Evento::findOrFail($id);

        // Verificar se o usuário pode deletar este evento (apenas o criador)
        if ($evento->usuario_id !== $usuario->id) {
            return response()->json([
                'success' => false,
                'message' => 'Você só pode deletar eventos que criou.'
            ], 403);
        }

        $evento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Evento deletado com sucesso!'
        ]);
    }

    /**
     * Estatísticas do calendário
     */
    public function estatisticas()
    {
        $usuario = Auth::user();

        // Eventos pessoais
        $eventosPessoais = Evento::paraUsuario($usuario->id)->pessoais()->count();
        $eventosPessoaisFuturos = Evento::paraUsuario($usuario->id)->pessoais()->futuros()->count();

        // Eventos compartilhados
        $eventosCompartilhados = 0;
        $eventosCompartilhadosFuturos = 0;

        $relacionamento = Relacionamento::where(function($query) use ($usuario) {
            $query->where('user_id_1', $usuario->id)
                  ->orWhere('user_id_2', $usuario->id);
        })->where('status', 'ativo')->first();

        if ($relacionamento) {
            $eventosCompartilhados = Evento::compartilhadosDoRelacionamento($relacionamento->id)->count();
            $eventosCompartilhadosFuturos = Evento::compartilhadosDoRelacionamento($relacionamento->id)->futuros()->count();
        }

        // Próximo evento
        $proximoEvento = Evento::where(function($query) use ($usuario, $relacionamento) {
            $query->where('usuario_id', $usuario->id);
            if ($relacionamento) {
                $query->orWhere('relacionamento_id', $relacionamento->id);
            }
        })->futuros()->orderBy('data_evento', 'asc')->first();

        return response()->json([
            'success' => true,
            'estatisticas' => [
                'eventos_pessoais' => $eventosPessoais,
                'eventos_pessoais_futuros' => $eventosPessoaisFuturos,
                'eventos_compartilhados' => $eventosCompartilhados,
                'eventos_compartilhados_futuros' => $eventosCompartilhadosFuturos,
                'total_eventos' => $eventosPessoais + $eventosCompartilhados,
                'total_eventos_futuros' => $eventosPessoaisFuturos + $eventosCompartilhadosFuturos,
                'proximo_evento' => $proximoEvento
            ]
        ]);
    }

    /**
     * Verificar se usuário tem acesso ao relacionamento
     */
    private function usuarioTemAcessoAoRelacionamento($usuario, $relacionamento)
    {
        return $relacionamento &&
               ($relacionamento->user_id_1 === $usuario->id || $relacionamento->user_id_2 === $usuario->id) &&
               $relacionamento->status === 'ativo';
    }
}
