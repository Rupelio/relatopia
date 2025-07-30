<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Configure CORS for API
        $middleware->web(append: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'onboarding.completed' => \App\Http\Middleware\OnboardingCompleted::class,
        ]);
    })
    ->withSchedule(function ($schedule) {
        // Processar queue de jobs a cada minuto (para os jobs agendados)
        $schedule->command('queue:work --once --timeout=60')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Backup: verificar eventos que precisam de notificação a cada 5 minutos
        $schedule->command('eventos:notificar-backup')
                 ->everyFiveMinutes()
                 ->withoutOverlapping();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
