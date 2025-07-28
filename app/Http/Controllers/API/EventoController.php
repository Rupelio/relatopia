<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\Relacionamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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
            ->values()
            ->map(function ($evento) {
                return [
                    'id' => $evento->id,
                    'title' => $evento->titulo,
                    'description' => $evento->descricao,
                    'date' => $evento->data_evento->format('Y-m-d'),
                    'time' => $evento->data_evento->format('H:i'),
                    'tipo' => $evento->tipo,
                    'categoria' => $evento->categoria,
                    'relacionamento_id' => $evento->relacionamento_id,
                    'created_at' => $evento->created_at->toISOString(),
                    'updated_at' => $evento->updated_at->toISOString(),
                ];
            });

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

        $eventos = $eventos->get()->map(function ($evento) {
            return [
                'id' => $evento->id,
                'title' => $evento->titulo,
                'description' => $evento->descricao,
                'date' => $evento->data_evento->format('Y-m-d'),
                'time' => $evento->data_evento->format('H:i'),
                'tipo' => $evento->categoria,
                'categoria' => $evento->tipo,
                'relacionamento_id' => $evento->relacionamento_id,
                'created_at' => $evento->created_at->toISOString(),
                'updated_at' => $evento->updated_at->toISOString(),
            ];
        });

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

        $eventos = $eventos->get()->map(function ($evento) {
            return [
                'id' => $evento->id,
                'title' => $evento->titulo,
                'description' => $evento->descricao,
                'date' => $evento->data_evento->format('Y-m-d'),
                'time' => $evento->data_evento->format('H:i'),
                'tipo' => $evento->categoria,
                'categoria' => $evento->tipo,
                'relacionamento_id' => $evento->relacionamento_id,
                'created_at' => $evento->created_at->toISOString(),
                'updated_at' => $evento->updated_at->toISOString(),
            ];
        });

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

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'nullable|date_format:H:i',
            'tipo' => 'required|in:aniversario,encontro,outro',
            'categoria' => 'required|in:pessoal,compartilhado',
        ], [
            'title.required' => 'O título é obrigatório.',
            'date.required' => 'A data é obrigatória.',
            'date.date_format' => 'A data deve estar no formato YYYY-MM-DD.',
            'time.date_format' => 'O horário deve estar no formato HH:MM.',
            'tipo.required' => 'O tipo é obrigatório.',
            'tipo.in' => 'Tipo inválido.',
            'categoria.required' => 'A categoria é obrigatória.',
            'categoria.in' => 'Categoria inválida.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        $dadosEvento = [
            'titulo' => $request->title,
            'descricao' => $request->description,
            'data_evento' => $request->date . ' ' . ($request->time ?? '00:00:00'),
            'tipo' => $request->tipo,
            'categoria' => $request->categoria,
            'usuario_id' => $usuario->id,
            'relacionamento_id' => null,
        ];

        // Se for evento compartilhado, verificar se tem relacionamento ativo
        if ($request->categoria === 'compartilhado') {
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
            'evento' => [
                'id' => $evento->id,
                'title' => $evento->titulo,
                'description' => $evento->descricao,
                'date' => $evento->data_evento->format('Y-m-d'),
                'time' => $evento->data_evento->format('H:i'),
                'tipo' => $evento->categoria,
                'categoria' => $evento->tipo,
                'relacionamento_id' => $evento->relacionamento_id,
                'created_at' => $evento->created_at->toISOString(),
                'updated_at' => $evento->updated_at->toISOString(),
            ]
        ], 201);
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
            'evento' => [
                'id' => $evento->id,
                'title' => $evento->titulo,
                'description' => $evento->descricao,
                'date' => $evento->data_evento->format('Y-m-d'),
                'time' => $evento->data_evento->format('H:i'),
                'tipo' => $evento->tipo,
                'categoria' => $evento->categoria,
                'relacionamento_id' => $evento->relacionamento_id,
                'created_at' => $evento->created_at->toISOString(),
                'updated_at' => $evento->updated_at->toISOString(),
            ]
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

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'date' => 'date_format:Y-m-d',
            'time' => 'nullable|date_format:H:i',
            'tipo' => 'in:aniversario,encontro,outro',
            'categoria' => 'in:pessoal,compartilhado',
        ], [
            'date.date_format' => 'A data deve estar no formato YYYY-MM-DD.',
            'time.date_format' => 'O horário deve estar no formato HH:MM.',
            'tipo.in' => 'Tipo inválido.',
            'categoria.in' => 'Categoria inválida.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        $dadosEvento = [];

        if ($request->has('title')) {
            $dadosEvento['titulo'] = $request->title;
        }

        if ($request->has('description')) {
            $dadosEvento['descricao'] = $request->description;
        }

        if ($request->has('date') || $request->has('time')) {
            $date = $request->date ?? $evento->data_evento->format('Y-m-d');
            $time = $request->time ?? $evento->data_evento->format('H:i:s');
            $dadosEvento['data_evento'] = $date . ' ' . $time;
        }

        if ($request->has('tipo')) {
            $dadosEvento['tipo'] = $request->tipo;
        }

        if ($request->has('categoria')) {
            $dadosEvento['categoria'] = $request->categoria;

            // Se mudou para compartilhado, verificar relacionamento
            if ($request->categoria === 'compartilhado' && $evento->categoria !== 'compartilhado') {
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
            } elseif ($request->categoria === 'pessoal') {
                $dadosEvento['relacionamento_id'] = null;
            }
        }

        $evento->update($dadosEvento);

        return response()->json([
            'success' => true,
            'message' => 'Evento atualizado com sucesso!',
            'evento' => [
                'id' => $evento->id,
                'title' => $evento->titulo,
                'description' => $evento->descricao,
                'date' => $evento->data_evento->format('Y-m-d'),
                'time' => $evento->data_evento->format('H:i'),
                'tipo' => $evento->tipo,
                'categoria' => $evento->categoria,
                'relacionamento_id' => $evento->relacionamento_id,
                'created_at' => $evento->created_at->toISOString(),
                'updated_at' => $evento->updated_at->toISOString(),
            ]
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

        $proximoEventoFormatted = null;
        if ($proximoEvento) {
            $proximoEventoFormatted = [
                'id' => $proximoEvento->id,
                'title' => $proximoEvento->titulo,
                'description' => $proximoEvento->descricao,
                'date' => $proximoEvento->data_evento->format('Y-m-d'),
                'time' => $proximoEvento->data_evento->format('H:i'),
                'tipo' => $proximoEvento->categoria,
                'categoria' => $proximoEvento->tipo,
                'relacionamento_id' => $proximoEvento->relacionamento_id,
            ];
        }

        return response()->json([
            'success' => true,
            'estatisticas' => [
                'eventos_pessoais' => $eventosPessoais,
                'eventos_pessoais_futuros' => $eventosPessoaisFuturos,
                'eventos_compartilhados' => $eventosCompartilhados,
                'eventos_compartilhados_futuros' => $eventosCompartilhadosFuturos,
                'total_eventos' => $eventosPessoais + $eventosCompartilhados,
                'total_eventos_futuros' => $eventosPessoaisFuturos + $eventosCompartilhadosFuturos,
                'proximo_evento' => $proximoEventoFormatted
            ]
        ]);
    }

    /**
     * Mapear tipo do mobile para o banco
     */
    private function mapTipoFromMobile($tipo)
    {
        $map = [
            'aniversario' => 'aniversario',
            'encontro' => 'encontro',
            'outro' => 'outro',
        ];

        return $map[$tipo] ?? 'outro';
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
