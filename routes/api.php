<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EventoController as ApiEventoController;
use App\Http\Controllers\API\SentimentoController as ApiSentimentoController;
use App\Http\Controllers\API\RelacionamentoController as ApiRelacionamentoController;
use App\Http\Controllers\API\ListaDesejoController as ApiListaDesejoController;
use App\Http\Controllers\API\RelacionamentoItemController as ApiRelacionamentoItemController;

// Rotas públicas de autenticação para mobile
Route::prefix('mobile')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Rotas protegidas para mobile
Route::prefix('mobile')->middleware('auth:sanctum')->group(function () {
    // Usuário autenticado
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'updatePassword']);

    // Verificação de email
    Route::post('/email/verification-notification', [AuthController::class, 'sendVerification']);
    Route::post('/email/verify', [AuthController::class, 'verifyEmail']);

    // Eventos
    Route::apiResource('eventos', ApiEventoController::class);
    Route::get('/eventos/estatisticas', [ApiEventoController::class, 'estatisticas']);
    Route::get('/eventos/pessoais', [ApiEventoController::class, 'eventosPessoais']);
    Route::get('/eventos/compartilhados', [ApiEventoController::class, 'eventosCompartilhados']);

    // Sentimentos
    Route::apiResource('sentimentos', ApiSentimentoController::class);
    Route::get('/sentimentos/estatisticas', [ApiSentimentoController::class, 'estatisticas']);

    // Relacionamentos
    Route::get('/relacionamento', [ApiRelacionamentoController::class, 'show']);
    Route::post('/relacionamento/convite', [ApiRelacionamentoController::class, 'enviarConvite']);
    Route::post('/relacionamento/convite/{token}/aceitar', [ApiRelacionamentoController::class, 'aceitarConvite']);
    Route::post('/relacionamento/convite/{token}/recusar', [ApiRelacionamentoController::class, 'recusarConvite']);
    Route::delete('/relacionamento', [ApiRelacionamentoController::class, 'desfazerVinculo']);
    Route::get('/relacionamento/convites', [ApiRelacionamentoController::class, 'convitesPendentes']);

    // Relacionamento Itens (tarefas do relacionamento)
    Route::apiResource('relacionamento-itens', ApiRelacionamentoItemController::class);
    Route::put('/relacionamento-itens/{id}/toggle', [ApiRelacionamentoItemController::class, 'toggleResolved']);
    Route::get('/relacionamento-itens/estatisticas', [ApiRelacionamentoItemController::class, 'estatisticas']);

    // Lista de Desejos
    Route::apiResource('lista-desejos', ApiListaDesejoController::class);
    Route::put('/lista-desejos/{item}/comprar', [ApiListaDesejoController::class, 'marcarComoComprado']);
    Route::get('/lista-desejos/estatisticas', [ApiListaDesejoController::class, 'estatisticas']);

    // Dados do parceiro (quando em relacionamento)
    Route::prefix('parceiro')->group(function () {
        Route::get('/sentimentos', [ApiSentimentoController::class, 'sentimentosParceiro']);
        Route::get('/lista-desejos', [ApiListaDesejoController::class, 'listaDesejoParceiro']);
        Route::get('/relacionamento-itens', [ApiRelacionamentoItemController::class, 'itensParceiro']);
    });

    // Onboarding
    Route::post('/onboarding/complete', [AuthController::class, 'completeOnboarding']);
});
