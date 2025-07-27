<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sentimento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SentimentoController extends Controller
{
    /**
     * Lista todos os sentimentos do usuário autenticado
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Sentimento::where('user_id', Auth::id());

            // Filtros opcionais
            if ($request->has('data_inicio')) {
                $query->where('data_sentimento', '>=', $request->data_inicio);
            }

            if ($request->has('data_fim')) {
                $query->where('data_sentimento', '<=', $request->data_fim);
            }

            if ($request->has('emocao')) {
                $query->where('emocao', $request->emocao);
            }

            $sentimentos = $query->orderBy('data_sentimento', 'desc')
                                ->orderBy('created_at', 'desc')
                                ->get();

            return response()->json([
                'success' => true,
                'data' => $sentimentos,
                'message' => 'Sentimentos recuperados com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar sentimentos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cria um novo sentimento
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'tipo_sentimento' => 'required|string|in:feliz,empolgado,grato,calmo,confiante,amoroso,esperancoso,triste,ansioso,raiva,frustrado,preocupado,sozinho,estressado,confuso,cansado,nostalgico,entediado',
                'nivel_intensidade' => 'required|integer|min:1|max:10',
                'descricao' => 'required|string|max:1000',
                'horario' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $sentimento = Sentimento::create([
                'user_id' => Auth::id(),
                'tipo_sentimento' => $request->tipo_sentimento,
                'nivel_intensidade' => $request->nivel_intensidade,
                'descricao' => $request->descricao,
                'horario' => $request->horario ?? now()
            ]);

            return response()->json([
                'success' => true,
                'data' => $sentimento,
                'message' => 'Sentimento criado com sucesso'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar sentimento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exibe um sentimento específico
     */
    public function show($id): JsonResponse
    {
        try {
            $sentimento = Sentimento::where('user_id', Auth::id())
                                   ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $sentimento,
                'message' => 'Sentimento recuperado com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sentimento não encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Atualiza um sentimento específico
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $sentimento = Sentimento::where('user_id', Auth::id())
                                   ->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'emocao' => 'sometimes|required|string|max:50',
                'intensidade' => 'sometimes|required|integer|min:1|max:10',
                'descricao' => 'nullable|string|max:1000',
                'data_sentimento' => 'sometimes|required|date',
                'fatores_externos' => 'nullable|string|max:500',
                'categoria' => 'nullable|string|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $sentimento->update($request->only([
                'emocao', 'intensidade', 'descricao', 'data_sentimento',
                'fatores_externos', 'categoria'
            ]));

            return response()->json([
                'success' => true,
                'data' => $sentimento->fresh(),
                'message' => 'Sentimento atualizado com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar sentimento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove um sentimento específico
     */
    public function destroy($id): JsonResponse
    {
        try {
            $sentimento = Sentimento::where('user_id', Auth::id())
                                   ->findOrFail($id);

            $sentimento->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sentimento excluído com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir sentimento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna estatísticas dos sentimentos
     */
    public function estatisticas(Request $request): JsonResponse
    {
        try {
            $periodo = $request->get('periodo', '30'); // Últimos 30 dias por padrão
            $dataInicio = Carbon::now()->subDays($periodo);

            $sentimentos = Sentimento::where('user_id', Auth::id())
                                    ->where('data_sentimento', '>=', $dataInicio)
                                    ->get();

            $total = $sentimentos->count();
            $mediaIntensidade = $total > 0 ? round($sentimentos->avg('intensidade'), 1) : 0;

            // Emoções mais frequentes
            $emocoesMaisFrequentes = $sentimentos->groupBy('emocao')
                                                ->map(function ($grupo) {
                                                    return $grupo->count();
                                                })
                                                ->sortDesc()
                                                ->take(5);

            // Distribuição por intensidade
            $distribucaoIntensidade = $sentimentos->groupBy('intensidade')
                                                 ->map(function ($grupo) {
                                                     return $grupo->count();
                                                 })
                                                 ->sortKeys();

            // Sentimentos por categoria
            $sentimentosPorCategoria = $sentimentos->whereNotNull('categoria')
                                                  ->groupBy('categoria')
                                                  ->map(function ($grupo) {
                                                      return $grupo->count();
                                                  });

            // Tendência dos últimos 7 dias
            $tendencia = [];
            for ($i = 6; $i >= 0; $i--) {
                $data = Carbon::now()->subDays($i)->format('Y-m-d');
                $sentimentosDia = $sentimentos->filter(function ($sentimento) use ($data) {
                    return Carbon::parse($sentimento->data_sentimento)->format('Y-m-d') === $data;
                });

                $tendencia[] = [
                    'data' => $data,
                    'total' => $sentimentosDia->count(),
                    'media_intensidade' => $sentimentosDia->count() > 0 ?
                        round($sentimentosDia->avg('intensidade'), 1) : 0
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_sentimentos' => $total,
                    'media_intensidade' => $mediaIntensidade,
                    'emocoes_mais_frequentes' => $emocoesMaisFrequentes,
                    'distribuicao_intensidade' => $distribucaoIntensidade,
                    'sentimentos_por_categoria' => $sentimentosPorCategoria,
                    'tendencia_7_dias' => $tendencia,
                    'periodo_analise' => $periodo . ' dias'
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
     * Retorna sentimentos do parceiro (quando em relacionamento)
     */
    public function sentimentosParceiro(): JsonResponse
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

            $sentimentos = Sentimento::where('user_id', $parceiroId)
                                    ->orderBy('data_sentimento', 'desc')
                                    ->orderBy('created_at', 'desc')
                                    ->get();

            return response()->json([
                'success' => true,
                'data' => $sentimentos,
                'message' => 'Sentimentos do parceiro recuperados com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar sentimentos do parceiro',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
