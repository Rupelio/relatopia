<?php

namespace App\Http\Controllers;

use App\Mail\ConviteRelacionamentoMail;
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
        $relacionamento = Relacionamento::where(function($q) use ($user){
            $q->where('user_id_1', $user->id)
            ->orWhere('user_id_2', $user->id);
        })->where('status', 'ativo')->first();
        return view('interface.perfil', compact('relacionamento'));
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
        $request->validate([
            'email' => 'required|email|exists:usuarios,email'
        ], [
            'email.required' => 'Informe o e-mail do co-participante',
            'email.email' =>'E-mail inválido',
            'email.exists' => 'Usuário não encontrado'
        ]);

        $user = $request->user();
        $coparticipante = Usuario::where('email', $request->email)->first();

        $existe = Relacionamento::where(function($query) use ($user, $coparticipante){
            $query->where('user_id_1', $user->id)->where('user_id_2', $coparticipante->id);
        })->orWhere(function($query) use ($user, $coparticipante){
            $query->where('user_id_1', $coparticipante->id)->where('user_id_2', $user->id);
        })->first();

        if($existe){
            return response()->json(['message' => 'Já existe um relacionamento entre vocês.'], 422);
        }

        $relacionamento = Relacionamento::create([
            'user_id_1' => $user->id,
            'user_id_2' => $coparticipante->id,
            'status' => 'pendente',
            'token' => Str::random(40),
        ]);

        $relacionamento->criarPermissoesPadrao();

        Mail::to($coparticipante->email)->send(new ConviteRelacionamentoMail($relacionamento));

        return response()->json(['message' => 'Convite enviado! Aguarde a aceitação do co-participante.']);
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
}
