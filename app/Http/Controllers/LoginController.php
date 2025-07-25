<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if($request->isMethod('GET')){
            return view('cadastro.login');
        }
        if($request->isMethod('POST')){
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ], [
                'email.required' => 'O email é obrigatório.',
                'email.email' => 'Digite um email válido.',
                'password.required' => 'A senha é obrigatória.',
            ]);
            if(Auth::attempt($credentials)){
                $request->session()->regenerate();

                $user = Auth::user();

                // Verificar se email está verificado
                if (!$user->hasVerifiedEmail()) {
                    return redirect()->route('verification.notice');
                }

                // Verificar se completou onboarding
                $onboarding_completed = !empty($user->data_inicio_relacionamento) && !empty($user->status_relacionamento);

                if (!$onboarding_completed) {
                    return redirect()->route('onboarding');
                }

                return redirect()->intended('/dashboard');
            }
            return back()->withErrors([
                'email' => 'Email ou senha incorretos.',
            ])->onlyInput('email');
        }
    }
}
