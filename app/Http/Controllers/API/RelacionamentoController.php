<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Relacionamento;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Mail\ConviteRelacionamentoMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class RelacionamentoController extends Controller
{
    /**
     * Exibe o relacionamento atual do usuário
     */
    public function show(): JsonResponse
    {
        try {
            $usuario = Auth::user();
            $relacionamento = $usuario->relacionamentoAtivo();

            if (!$relacionamento) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'Usuário não possui relacionamento ativo'
                ]);
            }

            // Carrega informações do parceiro
            $parceiro = $relacionamento->usuario1_id === $usuario->id ?
                       $relacionamento->usuario2 :
                       $relacionamento->usuario1;

            $relacionamentoData = $relacionamento->toArray();
            $relacionamentoData['parceiro'] = [
                'id' => $parceiro->id,
                'nome' => $parceiro->nome,
                'email' => $parceiro->email,
                'foto_perfil' => $parceiro->foto_perfil
            ];

            return response()->json([
                'success' => true,
                'data' => $relacionamentoData,
                'message' => 'Relacionamento recuperado com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar relacionamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envia convite de relacionamento
     */
    public function enviarConvite(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:usuarios,email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $usuario = Auth::user();
            $emailConvidado = $request->email;

            // Verifica se não está tentando convidar a si mesmo
            if ($usuario->email === $emailConvidado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não pode convidar a si mesmo'
                ], 422);
            }

            // Verifica se o usuário já tem relacionamento ativo
            if ($usuario->relacionamentoAtivo()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você já possui um relacionamento ativo'
                ], 422);
            }

            $convidado = Usuario::where('email', $emailConvidado)->first();

            // Verifica se o convidado já tem relacionamento ativo
            if ($convidado->relacionamentoAtivo()) {
                return response()->json([
                    'success' => false,
                    'message' => 'O usuário convidado já possui um relacionamento ativo'
                ], 422);
            }

            // Verifica se já existe um convite pendente entre estes usuários
            $conviteExistente = Relacionamento::where(function ($query) use ($usuario, $convidado) {
                $query->where('usuario1_id', $usuario->id)
                      ->where('usuario2_id', $convidado->id);
            })->orWhere(function ($query) use ($usuario, $convidado) {
                $query->where('usuario1_id', $convidado->id)
                      ->where('usuario2_id', $usuario->id);
            })->where('status', 'pendente')->first();

            if ($conviteExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Já existe um convite pendente entre vocês'
                ], 422);
            }

            // Cria o relacionamento com status pendente
            $token = Str::random(64);

            $relacionamento = Relacionamento::create([
                'usuario1_id' => $usuario->id,
                'usuario2_id' => $convidado->id,
                'status' => 'pendente',
                'token_convite' => $token,
                'data_convite' => now()
            ]);

            // Envia email de convite
            try {
                Mail::to($convidado->email)->send(new ConviteRelacionamentoMail($usuario, $token));
            } catch (\Exception $e) {
                // Se falhar ao enviar email, ainda retorna sucesso mas informa o problema
                Log::warning('Falha ao enviar email de convite: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'relacionamento_id' => $relacionamento->id,
                    'token' => $token,
                    'convidado' => [
                        'nome' => $convidado->nome,
                        'email' => $convidado->email
                    ]
                ],
                'message' => 'Convite enviado com sucesso'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar convite',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aceita um convite de relacionamento
     */
    public function aceitarConvite($token): JsonResponse
    {
        try {
            $usuario = Auth::user();

            // Verifica se o usuário já tem relacionamento ativo
            if ($usuario->relacionamentoAtivo()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você já possui um relacionamento ativo'
                ], 422);
            }

            $relacionamento = Relacionamento::where('token_convite', $token)
                                           ->where('status', 'pendente')
                                           ->first();

            if (!$relacionamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Convite não encontrado ou já foi processado'
                ], 404);
            }

            // Verifica se o usuário é o convidado
            if ($relacionamento->usuario2_id !== $usuario->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para aceitar este convite'
                ], 403);
            }

            // Aceita o convite
            $relacionamento->update([
                'status' => 'ativo',
                'data_inicio' => now(),
                'token_convite' => null
            ]);

            // Carrega informações do parceiro
            $parceiro = $relacionamento->usuario1;

            return response()->json([
                'success' => true,
                'data' => [
                    'relacionamento' => $relacionamento,
                    'parceiro' => [
                        'id' => $parceiro->id,
                        'nome' => $parceiro->nome,
                        'email' => $parceiro->email,
                        'foto_perfil' => $parceiro->foto_perfil
                    ]
                ],
                'message' => 'Convite aceito com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao aceitar convite',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recusa um convite de relacionamento
     */
    public function recusarConvite($token): JsonResponse
    {
        try {
            $usuario = Auth::user();

            $relacionamento = Relacionamento::where('token_convite', $token)
                                           ->where('status', 'pendente')
                                           ->first();

            if (!$relacionamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Convite não encontrado ou já foi processado'
                ], 404);
            }

            // Verifica se o usuário é o convidado
            if ($relacionamento->usuario2_id !== $usuario->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para recusar este convite'
                ], 403);
            }

            // Recusa o convite (exclui o registro)
            $relacionamento->delete();

            return response()->json([
                'success' => true,
                'message' => 'Convite recusado com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recusar convite',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Desfaz um relacionamento
     */
    public function desfazerVinculo(): JsonResponse
    {
        try {
            $usuario = Auth::user();
            $relacionamento = $usuario->relacionamentoAtivo();

            if (!$relacionamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não possui relacionamento ativo'
                ], 404);
            }

            // Atualiza o status do relacionamento
            $relacionamento->update([
                'status' => 'inativo',
                'data_fim' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Relacionamento desfeito com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao desfazer relacionamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista convites pendentes do usuário
     */
    public function convitesPendentes(): JsonResponse
    {
        try {
            $usuario = Auth::user();

            // Convites recebidos
            $convitesRecebidos = Relacionamento::where('usuario2_id', $usuario->id)
                                              ->where('status', 'pendente')
                                              ->with('usuario1:id,nome,email,foto_perfil')
                                              ->get();

            // Convites enviados
            $convitesEnviados = Relacionamento::where('usuario1_id', $usuario->id)
                                             ->where('status', 'pendente')
                                             ->with('usuario2:id,nome,email,foto_perfil')
                                             ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'convites_recebidos' => $convitesRecebidos,
                    'convites_enviados' => $convitesEnviados
                ],
                'message' => 'Convites recuperados com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar convites',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
