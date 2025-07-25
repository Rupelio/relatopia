<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Mail\VerificarEmailMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VerificacaoEmailController extends Controller
{
    /**
     * Mostra a tela de verificação pendente
     */
    public function mostrarVerificacaoPendente()
    {
        if (Auth::user() && Auth::user()->hasVerifiedEmail()) {
            // Se verificado, redirecionar baseado no onboarding
            $user = Auth::user();
            $onboarding_completed = !empty($user->data_inicio_relacionamento) && !empty($user->status_relacionamento);

            if ($onboarding_completed) {
                return redirect()->route('dashboard');
            } else {
                return redirect()->route('onboarding');
            }
        }

        return view('auth.verificar-email');
    }

    /**
     * Envia email de verificação
     */
    public function enviarVerificacao(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('success', 'Seu email já está verificado!');
        }

        // Gera URL de verificação segura
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email)
            ]
        );

        // Envia email
        try {
            Mail::to($user->email)->send(new VerificarEmailMail($user, $verificationUrl));

            return back()->with('success', 'Email de verificação enviado! Verifique sua caixa de entrada.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao enviar email. Tente novamente.');
        }
    }

    /**
     * Verifica o email do usuário
     */
    public function verificarEmail(Request $request, $id, $hash)
    {
        $user = Usuario::findOrFail($id);

        // Verifica se o hash confere
        if (!hash_equals((string) $hash, sha1($user->email))) {
            return redirect()->route('verification.notice')->with('error', 'Link de verificação inválido.');
        }

        // Verifica se a URL não expirou
        if (!$request->hasValidSignature()) {
            return redirect()->route('verification.notice')->with('error', 'Link de verificação expirado.');
        }

        // Verifica se já não está verificado
        if ($user->hasVerifiedEmail()) {
            // Se já verificado, redirecionar baseado no onboarding
            $onboarding_completed = !empty($user->data_inicio_relacionamento) && !empty($user->status_relacionamento);

            if ($onboarding_completed) {
                return redirect()->route('dashboard')->with('success', 'Seu email já estava verificado!');
            } else {
                return redirect()->route('onboarding')->with('success', 'Seu email já estava verificado!');
            }
        }

        // Marca como verificado
        $user->markEmailAsVerified();

        // Redirecionar para onboarding se não completou, senão para dashboard
        $onboarding_completed = !empty($user->data_inicio_relacionamento) && !empty($user->status_relacionamento);

        if ($onboarding_completed) {
            return redirect()->route('dashboard')->with('success', 'Email verificado com sucesso! Bem-vindo de volta ao Relatopia!');
        } else {
            return redirect()->route('onboarding')->with('success', 'Email verificado com sucesso! Vamos configurar seu relacionamento!');
        }
    }
}
