<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ListaDesejo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ListaDesejoController extends Controller
{
    /**
     * Converte prioridade numérica para string
     */
    private function convertePrioridade($prioridade)
    {
        $map = [1 => 'baixa', 2 => 'baixa', 3 => 'media', 4 => 'alta', 5 => 'alta'];
        return $map[$prioridade] ?? 'media';
    }

    /**
     * Lista todos os itens da lista de desejos do usuário
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = ListaDesejo::where('usuario_id', Auth::id());

            // Filtros opcionais
            if ($request->has('status')) {
                if ($request->status === 'comprado') {
                    $query->whereNotNull('data_compra');
                } else {
                    $query->whereNull('data_compra');
                }
            }

            if ($request->has('categoria')) {
                $query->where('categoria', $request->categoria);
            }

            if ($request->has('prioridade')) {
                $query->where('prioridade', $request->prioridade);
            }

            $itens = $query->orderBy('prioridade', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->get();

            return response()->json([
                'success' => true,
                'data' => $itens,
                'message' => 'Lista de desejos recuperada com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar lista de desejos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cria um novo item na lista de desejos
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'item' => 'required|string|max:255',
                'descricao' => 'nullable|string|max:1000',
                'preco_estimado' => 'nullable|numeric|min:0',
                'categoria' => 'nullable|string|max:100',
                'prioridade' => 'nullable|integer|min:1|max:5',
                'link' => 'nullable|url|max:500',
                'observacoes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $item = ListaDesejo::create([
                'usuario_id' => Auth::id(),
                'titulo' => $request->item,
                'descricao' => $request->descricao,
                'preco_estimado' => $request->preco_estimado,
                'prioridade' => $this->convertePrioridade($request->prioridade ?? 3),
                'link_compra' => $request->link,
                'observacoes' => $request->observacoes
            ]);

            return response()->json([
                'success' => true,
                'data' => $item,
                'message' => 'Item adicionado à lista de desejos com sucesso'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar item à lista de desejos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exibe um item específico da lista de desejos
     */
    public function show($id): JsonResponse
    {
        try {
            $item = ListaDesejo::where('usuario_id', Auth::id())
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
     * Atualiza um item da lista de desejos
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $item = ListaDesejo::where('usuario_id', Auth::id())
                              ->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'item' => 'sometimes|required|string|max:255',
                'descricao' => 'nullable|string|max:1000',
                'preco_estimado' => 'nullable|numeric|min:0',
                'categoria' => 'nullable|string|max:100',
                'prioridade' => 'nullable|integer|min:1|max:5',
                'link' => 'nullable|url|max:500',
                'observacoes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $item->update($request->only([
                'item', 'descricao', 'preco_estimado', 'categoria',
                'prioridade', 'link', 'observacoes'
            ]));

            return response()->json([
                'success' => true,
                'data' => $item->fresh(),
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
     * Remove um item da lista de desejos
     */
    public function destroy($id): JsonResponse
    {
        try {
            $item = ListaDesejo::where('usuario_id', Auth::id())
                              ->findOrFail($id);

            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removido da lista de desejos com sucesso'
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
     * Marca um item como comprado
     */
    public function marcarComoComprado($id): JsonResponse
    {
        try {
            $item = ListaDesejo::where('usuario_id', Auth::id())
                              ->findOrFail($id);

            // Toggle do status de comprado
            if ($item->data_compra) {
                // Se já está comprado, desmarca
                $item->update([
                    'data_compra' => null,
                    'preco_real' => null
                ]);
                $message = 'Item desmarcado como comprado';
            } else {
                // Se não está comprado, marca como comprado
                $item->update([
                    'data_compra' => now(),
                    'preco_real' => $item->preco_estimado // Pode ser atualizado depois
                ]);
                $message = 'Item marcado como comprado';
            }

            return response()->json([
                'success' => true,
                'data' => $item->fresh(),
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
     * Retorna estatísticas da lista de desejos
     */
    public function estatisticas(): JsonResponse
    {
        try {
            $itens = ListaDesejo::where('usuario_id', Auth::id())->get();

            $total = $itens->count();
            $comprados = $itens->whereNotNull('data_compra')->count();
            $pendentes = $total - $comprados;

            $valorTotalEstimado = $itens->whereNull('data_compra')->sum('preco_estimado');
            $valorTotalGasto = $itens->whereNotNull('data_compra')->sum('preco_real');

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

            // Itens comprados nos últimos 30 dias
            $comprasRecentes = $itens->where('data_compra', '>=', now()->subDays(30))
                                    ->whereNotNull('data_compra')
                                    ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_itens' => $total,
                    'itens_comprados' => $comprados,
                    'itens_pendentes' => $pendentes,
                    'percentual_comprado' => $total > 0 ? round(($comprados / $total) * 100, 1) : 0,
                    'valor_total_estimado' => $valorTotalEstimado,
                    'valor_total_gasto' => $valorTotalGasto,
                    'economia' => $valorTotalEstimado - $valorTotalGasto,
                    'itens_por_categoria' => $itensPorCategoria,
                    'itens_por_prioridade' => $itensPorPrioridade,
                    'compras_ultimos_30_dias' => $comprasRecentes
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
     * Retorna lista de desejos do parceiro (quando em relacionamento)
     */
    public function listaDesejoParceiro(): JsonResponse
    {
        try {
            $usuario = Auth::user();

            // Verifica se o usuário tem relacionamento ativo
            $relacionamento = $usuario->relacionamentoAtivo();

            if (!$relacionamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não possui relacionamento ativo'
                ], 404);
            }

            // Determina o parceiro
            $parceiroId = $relacionamento->usuario1_id === $usuario->id ?
                         $relacionamento->usuario2_id :
                         $relacionamento->usuario1_id;

            $itens = ListaDesejo::where('usuario_id', $parceiroId)
                               ->orderBy('prioridade', 'desc')
                               ->orderBy('created_at', 'desc')
                               ->get();

            return response()->json([
                'success' => true,
                'data' => $itens,
                'message' => 'Lista de desejos do parceiro recuperada com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar lista de desejos do parceiro',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
