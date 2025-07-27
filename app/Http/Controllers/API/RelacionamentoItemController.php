<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\RelacionamentoItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RelacionamentoItemController extends Controller
{
    /**
     * Lista todos os itens do relacionamento do usuário
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $usuario = Auth::user();
            $relacionamento = $usuario->relacionamentoAtivo();

            if (!$relacionamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não possui relacionamento ativo'
                ], 404);
            }

            $query = RelacionamentoItem::where('relacionamento_id', $relacionamento->id);

            // Filtros opcionais
            if ($request->has('status')) {
                if ($request->status === 'concluido') {
                    $query->whereNotNull('data_conclusao');
                } else {
                    $query->whereNull('data_conclusao');
                }
            }

            if ($request->has('categoria')) {
                $query->where('categoria', $request->categoria);
            }

            if ($request->has('prioridade')) {
                $query->where('prioridade', $request->prioridade);
            }

            if ($request->has('responsavel')) {
                $query->where('usuario_responsavel_id', $request->responsavel);
            }

            $itens = $query->with(['usuarioResponsavel:id,nome,email', 'usuarioCriador:id,nome,email'])
                          ->orderBy('prioridade', 'desc')
                          ->orderBy('data_prazo', 'asc')
                          ->orderBy('created_at', 'desc')
                          ->get();

            return response()->json([
                'success' => true,
                'data' => $itens,
                'message' => 'Itens do relacionamento recuperados com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar itens do relacionamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cria um novo item no relacionamento
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $usuario = Auth::user();
            $relacionamento = $usuario->relacionamentoAtivo();

            if (!$relacionamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não possui relacionamento ativo'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string|max:255',
                'descricao' => 'nullable|string|max:1000',
                'categoria' => 'nullable|string|max:100',
                'prioridade' => 'nullable|integer|min:1|max:5',
                'data_prazo' => 'nullable|date|after_or_equal:today',
                'usuario_responsavel_id' => 'nullable|integer|exists:usuarios,id',
                'observacoes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Valida se o responsável faz parte do relacionamento
            if ($request->usuario_responsavel_id) {
                $responsavelValido = in_array($request->usuario_responsavel_id, [
                    $relacionamento->usuario1_id,
                    $relacionamento->usuario2_id
                ]);

                if (!$responsavelValido) {
                    return response()->json([
                        'success' => false,
                        'message' => 'O responsável deve ser um dos membros do relacionamento'
                    ], 422);
                }
            }

            $item = RelacionamentoItem::create([
                'relacionamento_id' => $relacionamento->id,
                'usuario_criador_id' => $usuario->id,
                'titulo' => $request->titulo,
                'descricao' => $request->descricao,
                'categoria' => $request->categoria,
                'prioridade' => $request->prioridade ?? 3, // Prioridade média por padrão
                'data_prazo' => $request->data_prazo,
                'usuario_responsavel_id' => $request->usuario_responsavel_id ?? $usuario->id,
                'observacoes' => $request->observacoes
            ]);

            $item->load(['usuarioResponsavel:id,nome,email', 'usuarioCriador:id,nome,email']);

            return response()->json([
                'success' => true,
                'data' => $item,
                'message' => 'Item adicionado ao relacionamento com sucesso'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar item ao relacionamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exibe um item específico do relacionamento
     */
    public function show($id): JsonResponse
    {
        try {
            $usuario = Auth::user();
            $relacionamento = $usuario->relacionamentoAtivo();

            if (!$relacionamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não possui relacionamento ativo'
                ], 404);
            }

            $item = RelacionamentoItem::where('relacionamento_id', $relacionamento->id)
                                     ->with(['usuarioResponsavel:id,nome,email', 'usuarioCriador:id,nome,email'])
                                     ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $item,
                'message' => 'Item recuperado com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Item não encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Atualiza um item do relacionamento
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $usuario = Auth::user();
            $relacionamento = $usuario->relacionamentoAtivo();

            if (!$relacionamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não possui relacionamento ativo'
                ], 404);
            }

            $item = RelacionamentoItem::where('relacionamento_id', $relacionamento->id)
                                     ->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'titulo' => 'sometimes|required|string|max:255',
                'descricao' => 'nullable|string|max:1000',
                'categoria' => 'nullable|string|max:100',
                'prioridade' => 'nullable|integer|min:1|max:5',
                'data_prazo' => 'nullable|date|after_or_equal:today',
                'usuario_responsavel_id' => 'nullable|integer|exists:usuarios,id',
                'observacoes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Valida se o responsável faz parte do relacionamento
            if ($request->usuario_responsavel_id) {
                $responsavelValido = in_array($request->usuario_responsavel_id, [
                    $relacionamento->usuario1_id,
                    $relacionamento->usuario2_id
                ]);

                if (!$responsavelValido) {
                    return response()->json([
                        'success' => false,
                        'message' => 'O responsável deve ser um dos membros do relacionamento'
                    ], 422);
                }
            }

            $item->update($request->only([
                'titulo', 'descricao', 'categoria', 'prioridade',
                'data_prazo', 'usuario_responsavel_id', 'observacoes'
            ]));

            $item->load(['usuarioResponsavel:id,nome,email', 'usuarioCriador:id,nome,email']);

            return response()->json([
                'success' => true,
                'data' => $item,
                'message' => 'Item atualizado com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove um item do relacionamento
     */
    public function destroy($id): JsonResponse
    {
        try {
            $usuario = Auth::user();
            $relacionamento = $usuario->relacionamentoAtivo();

            if (!$relacionamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não possui relacionamento ativo'
                ], 404);
            }

            $item = RelacionamentoItem::where('relacionamento_id', $relacionamento->id)
                                     ->findOrFail($id);

            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removido com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Alterna o status de conclusão de um item
     */
    public function toggleResolved($id): JsonResponse
    {
        try {
            $usuario = Auth::user();
            $relacionamento = $usuario->relacionamentoAtivo();

            if (!$relacionamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não possui relacionamento ativo'
                ], 404);
            }

            $item = RelacionamentoItem::where('relacionamento_id', $relacionamento->id)
                                     ->findOrFail($id);

            // Toggle do status de conclusão
            if ($item->data_conclusao) {
                // Se já está concluído, desmarca
                $item->update([
                    'data_conclusao' => null,
                    'usuario_conclusao_id' => null
                ]);
                $message = 'Item marcado como pendente';
            } else {
                // Se não está concluído, marca como concluído
                $item->update([
                    'data_conclusao' => now(),
                    'usuario_conclusao_id' => $usuario->id
                ]);
                $message = 'Item marcado como concluído';
            }

            $item->load(['usuarioResponsavel:id,nome,email', 'usuarioCriador:id,nome,email']);

            return response()->json([
                'success' => true,
                'data' => $item,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status do item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna estatísticas dos itens do relacionamento
     */
    public function estatisticas(): JsonResponse
    {
        try {
            $usuario = Auth::user();
            $relacionamento = $usuario->relacionamentoAtivo();

            if (!$relacionamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não possui relacionamento ativo'
                ], 404);
            }

            $itens = RelacionamentoItem::where('relacionamento_id', $relacionamento->id)->get();

            $total = $itens->count();
            $concluidos = $itens->whereNotNull('data_conclusao')->count();
            $pendentes = $total - $concluidos;
            $atrasados = $itens->whereNull('data_conclusao')
                              ->where('data_prazo', '<', now())
                              ->count();

            // Distribuição por categoria
            $itensPorCategoria = $itens->whereNotNull('categoria')
                                      ->groupBy('categoria')
                                      ->map(function ($grupo) {
                                          return $grupo->count();
                                      });

            // Distribuição por prioridade
            $itensPorPrioridade = $itens->groupBy('prioridade')
                                       ->map(function ($grupo) {
                                           return $grupo->count();
                                       })
                                       ->sortKeys();

            // Itens por responsável
            $itensPorResponsavel = $itens->groupBy('usuario_responsavel_id')
                                        ->map(function ($grupo, $usuarioId) use ($relacionamento) {
                                            $usuario = $usuarioId == $relacionamento->usuario1_id ?
                                                      $relacionamento->usuario1 :
                                                      $relacionamento->usuario2;
                                            return [
                                                'nome' => $usuario->nome,
                                                'total' => $grupo->count(),
                                                'concluidos' => $grupo->whereNotNull('data_conclusao')->count()
                                            ];
                                        });

            // Itens concluídos nos últimos 30 dias
            $conclusoesRecentes = $itens->where('data_conclusao', '>=', now()->subDays(30))
                                       ->whereNotNull('data_conclusao')
                                       ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_itens' => $total,
                    'itens_concluidos' => $concluidos,
                    'itens_pendentes' => $pendentes,
                    'itens_atrasados' => $atrasados,
                    'percentual_conclusao' => $total > 0 ? round(($concluidos / $total) * 100, 1) : 0,
                    'itens_por_categoria' => $itensPorCategoria,
                    'itens_por_prioridade' => $itensPorPrioridade,
                    'itens_por_responsavel' => $itensPorResponsavel,
                    'conclusoes_ultimos_30_dias' => $conclusoesRecentes
                ],
                'message' => 'Estatísticas recuperadas com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar estatísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna itens do relacionamento visíveis pelo parceiro
     */
    public function itensParceiro(): JsonResponse
    {
        try {
            $usuario = Auth::user();
            $relacionamento = $usuario->relacionamentoAtivo();

            if (!$relacionamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não possui relacionamento ativo'
                ], 404);
            }

            // Retorna todos os itens do relacionamento (ambos podem ver tudo)
            $itens = RelacionamentoItem::where('relacionamento_id', $relacionamento->id)
                                      ->with(['usuarioResponsavel:id,nome,email', 'usuarioCriador:id,nome,email'])
                                      ->orderBy('prioridade', 'desc')
                                      ->orderBy('data_prazo', 'asc')
                                      ->orderBy('created_at', 'desc')
                                      ->get();

            return response()->json([
                'success' => true,
                'data' => $itens,
                'message' => 'Itens do relacionamento recuperados com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar itens do relacionamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
