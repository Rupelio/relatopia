<?php

use App\Http\Controllers\CadastroController;
use App\Http\Controllers\HistoricoSentimentoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InterfaceUsuarioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PerfilUsuarioController;
use App\Http\Controllers\RelacionamentoItemController;
use App\Http\Controllers\SentimentoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class);
Route::match(['GET', 'POST'], '/login', LoginController::class)->name('login');
Route::match(['GET', 'POST'], '/cadastro', CadastroController::class)->name('cadastro');

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', InterfaceUsuarioController::class)->name('dashboard');
    Route::get('/perfil', [PerfilUsuarioController::class, 'index'])->name('perfil');
    Route::get('/historico', HistoricoSentimentoController::class)->name('historico');

    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('logout');

    // API para relacionamento itens (usando prefixo /api para manter URLs)
    Route::prefix('api')->group(function () {
        Route::post('/relacionamento-itens', [RelacionamentoItemController::class, 'store']);
        Route::get('/relacionamento-itens', [RelacionamentoItemController::class, 'index']);
        Route::put('/relacionamento-itens/{id}/toggle', [RelacionamentoItemController::class, 'toggleResolved']);
        Route::delete('/relacionamento-itens/{id}', [RelacionamentoItemController::class, 'destroy']);
        Route::get('/estatisticas', [RelacionamentoItemController::class, 'estatisticas']);
        Route::post('/alterar-senha', [PerfilUsuarioController::class, 'alterarSenha']);
        Route::post('/relacionamento', [PerfilUsuarioController::class, 'atualizarRelacionamento']);
        Route::post('/sentimento', [SentimentoController::class, 'store']);
        Route::get('/sentimento', [SentimentoController::class, 'index']);
        Route::put('/sentimento/{id}', [SentimentoController::class, 'update']);
        Route::get('/sentimento/{id}', [SentimentoController::class, 'show']);
        Route::get('/estatisticasSentimento', [SentimentoController::class, 'estatisticasSentimento']);
    });
});
