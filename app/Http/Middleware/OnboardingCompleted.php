<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OnboardingCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Verificar se o usuário completou o onboarding
        // Por ora, vamos verificar se tem data_inicio_relacionamento preenchida
        $onboarding_completed = !empty($user->data_inicio_relacionamento) && !empty($user->status_relacionamento);

        if (!$onboarding_completed) {
            return redirect()->route('onboarding');
        }

        return $next($request);
    }
}
