<?php

namespace App\Http\Controllers;

use App\Mail\RedefinirSenhaMail;
use App\Models\PasswordResetToken;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RedefinirSenhaController extends Controller
{
    public function mostrarFormularioSolicitacao()
    {
        return view('auth.esqueci-senha');
    }

    public function enviarLinkRedefinicao(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,email'
        ], [
            'email.required' => 'O campo email é obrigatório',
            'email.email' => 'Digite um email válido',
            'email.exists' => 'Este email não está cadastrado no sistema'
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        // Remove tokens antigos para este usuário
        PasswordResetToken::where('email', $request->email)->delete();

        // Cria novo token
        $token = Str::random(60);
        PasswordResetToken::create([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now()
        ]);

        // Envia email
        Mail::to($request->email)->send(new RedefinirSenhaMail($token, $usuario));

        return back()->with('status', 'Link de redefinição enviado para seu email!');
    }

    public function mostrarFormularioRedefinicao($token)
    {
        return view('auth.redefinir-senha', ['token' => $token]);
    }

    public function redefinirSenha(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:usuarios,email',
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'email.required' => 'O campo email é obrigatório',
            'email.email' => 'Digite um email válido',
            'email.exists' => 'Este email não está cadastrado',
            'password.required' => 'A senha é obrigatória',
            'password.confirmed' => 'A confirmação da senha não confere',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres',
            'token.required' => 'Token inválido'
        ]);

        // Verifica se existe um token válido
        $passwordReset = PasswordResetToken::where('email', $request->email)
            ->where('created_at', '>', now()->subHours(1)) // Token válido por 1 hora
            ->first();

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return back()->withErrors(['email' => 'Token inválido ou expirado']);
        }

        // Atualiza a senha do usuário
        $usuario = Usuario::where('email', $request->email)->first();
        $usuario->update([
            'password' => Hash::make($request->password)
        ]);

        // Remove o token usado
        PasswordResetToken::where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Senha redefinida com sucesso! Faça login com sua nova senha.');
    }
}
