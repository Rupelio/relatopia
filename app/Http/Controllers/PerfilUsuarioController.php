<?php

namespace App\Http\Controllers;

use App\Mail\ConviteRelacionamentoMail;
use App\Mail\ConviteCadastroMail;
use App\Models\Relacionamento;
use App\Models\RelacionamentoPermissao;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class PerfilUsuarioController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Relacionamento ativo
        $relacionamento = Relacionamento::where(function($q) use ($user){
            $q->where('user_id_1', $user->id)
            ->orWhere('user_id_2', $user->id);
        })->where('status', 'ativo')->first();

        // Convites pendentes enviados
        $conviteEnviado = Relacionamento::where('user_id_1', $user->id)
            ->where('status', 'pendente')->first();

        // Convites pendentes recebidos
        $conviteRecebido = Relacionamento::where('user_id_2', $user->id)
            ->where('status', 'pendente')->first();

        return view('interface.perfil', compact('relacionamento', 'conviteEnviado', 'conviteRecebido'));
    }
    public function alterarSenha(Request $request){
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.required' => 'A senha atual é obrigatória',
            'new_password.required' => 'A nova senha é obrigatória',
            'new_password.confirmed' => 'A confirmação da senha não confere',
            'new_password.min' => 'A nova senha deve ter pelo menos 8 caracteres',
        ]);
        $user = $request->user();

        if(!Hash::check($request->current_password, $user->password)){
            return response()->json([
                'errors' => [
                    'current_password' => ['A senha atual está incorreta']
                ]
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Senha alterada com sucesso!'
        ]);
    }
    public function atualizarRelacionamento(Request $request){
        $request->validate([
            'data_inicio' => 'nullable|date|before_or_equal:today',
            'status' => 'nullable|in:namoro,noivado,casamento,uniao-estavel'
        ], [
            'data_inicio.date' => 'Data inválida',
            'data_inicio.before_or_equal' => 'A data não pode ser futura',
            'status.in' => 'Status inválido'
        ]);
        $user = $request->user();
        $user->update([
            'data_inicio_relacionamento' => $request->data_inicio,
            'status_relacionamento' => $request->status
        ]);
        return response()->json([
            'message' => 'Informações do relacionamento atualizadas com sucesso!'
        ]);
    }
    public function vincularCoparticipante(Request $request){
        // Validação unificada - sempre permite email não cadastrado
        $request->validate([
            'email' => 'required|email'
        ], [
            'email.required' => 'Informe o e-mail do co-participante',
            'email.email' => 'E-mail inválido'
        ]);

        $user = $request->user();
        $fromOnboarding = $request->input('from_onboarding', false);

        // Verificar se não está tentando convidar a si mesmo
        if (strtolower($request->email) === strtolower($user->email)) {
            return response()->json(['message' => 'Você não pode convidar a si mesmo.'], 422);
        }

        $coparticipante = Usuario::where('email', $request->email)->first();

        // Se o usuário existe, verificar se já há relacionamento
        if ($coparticipante) {
            $existe = Relacionamento::where(function($query) use ($user, $coparticipante){
                $query->where('user_id_1', $user->id)->where('user_id_2', $coparticipante->id);
            })->orWhere(function($query) use ($user, $coparticipante){
                $query->where('user_id_1', $coparticipante->id)->where('user_id_2', $user->id);
            })->first();

            if($existe){
                if($existe->status === 'pendente') {
                    return response()->json(['message' => 'Já existe um convite pendente entre vocês.'], 422);
                }
                return response()->json(['message' => 'Já existe um relacionamento ativo entre vocês.'], 422);
            }

            // Criar relacionamento com usuário cadastrado
            $relacionamento = Relacionamento::create([
                'user_id_1' => $user->id,
                'user_id_2' => $coparticipante->id,
                'status' => 'pendente',
                'token' => Str::random(40),
            ]);

            $relacionamento->criarPermissoesPadrao();
            Mail::to($coparticipante->email)->send(new ConviteRelacionamentoMail($relacionamento));

            return response()->json(['message' => 'Convite enviado! Aguarde a aceitação do co-participante.']);
        } else {
            // Usuário não existe - enviar convite de cadastro
            try {
                Mail::to($request->email)->send(new ConviteCadastroMail($user, $request->email));
                return response()->json(['message' => 'Convite enviado para que a pessoa se cadastre na plataforma! Assim que ela criar a conta, vocês serão automaticamente vinculados.']);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Erro ao enviar convite de cadastro. Tente novamente.'], 500);
            }
        }
    }
    public function desfazerVinculo($id){
        $relacionamento = Relacionamento::findOrFail($id);
        $user = Auth::user();
        if ($relacionamento->user_id_1 != $user->id && $relacionamento->user_id_2 != $user->id) {
            abort(403);
        }
        $relacionamento->delete();
        return redirect()->back()->with('success', 'Vínculo desfeito com sucesso!');
    }

    public function aceitarConvitePerfil($id)
    {
        $user = Auth::user();
        $relacionamento = Relacionamento::findOrFail($id);

        // Verifica se o usuário é o destinatário do convite
        if ($relacionamento->user_id_2 !== $user->id || $relacionamento->status !== 'pendente') {
            abort(403);
        }

        $relacionamento->update(['status' => 'ativo']);

        return response()->json(['message' => 'Convite aceito com sucesso!']);
    }

    public function recusarConvitePerfil($id)
    {
        $user = Auth::user();
        $relacionamento = Relacionamento::findOrFail($id);

        // Verifica se o usuário é o destinatário do convite
        if ($relacionamento->user_id_2 !== $user->id || $relacionamento->status !== 'pendente') {
            abort(403);
        }

        $relacionamento->delete();

        return response()->json(['message' => 'Convite recusado.']);
    }

    public function cancelarConvite($id)
    {
        $user = Auth::user();
        $relacionamento = Relacionamento::findOrFail($id);

        // Verifica se o usuário é quem enviou o convite
        if ($relacionamento->user_id_1 !== $user->id || $relacionamento->status !== 'pendente') {
            abort(403);
        }

        $relacionamento->delete();

        return response()->json(['message' => 'Convite cancelado.']);
    }

    public function onboarding()
    {
        $user = Auth::user();

        // Se já completou o onboarding (tem dados do relacionamento), redirecionar para dashboard
        $onboarding_completed = !empty($user->data_inicio_relacionamento) && !empty($user->status_relacionamento);

        if ($onboarding_completed) {
            return redirect()->route('dashboard');
        }

        return view('interface.onboarding');
    }

    public function finalizarOnboarding(Request $request)
    {
        // Por ora, apenas retorna sucesso pois a lógica já foi aplicada
        // quando os dados do relacionamento foram salvos
        return response()->json(['message' => 'Onboarding finalizado com sucesso!']);
    }
}
