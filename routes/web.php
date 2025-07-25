<?php

use App\Http\Controllers\CadastroController;
use App\Http\Controllers\HistoricoSentimentoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InterfaceUsuarioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PerfilUsuarioController;
use App\Http\Controllers\RedefinirSenhaController;
use App\Http\Controllers\RelacionamentoController;
use App\Http\Controllers\RelacionamentoItemController;
use App\Http\Controllers\SentimentoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class);
Route::match(['GET', 'POST'], '/login', LoginController::class)->name('login');
Route::match(['GET', 'POST'], '/cadastro', CadastroController::class)->name('cadastro');

// Rotas para redefinição de senha (sem autenticação)
Route::get('/esqueci-senha', [RedefinirSenhaController::class, 'mostrarFormularioSolicitacao'])->name('password.request');
Route::post('/esqueci-senha', [RedefinirSenhaController::class, 'enviarLinkRedefinicao'])->name('password.email');
Route::get('/redefinir-senha/{token}', [RedefinirSenhaController::class, 'mostrarFormularioRedefinicao'])->name('password.reset');
Route::post('/redefinir-senha', [RedefinirSenhaController::class, 'redefinirSenha'])->name('password.update');

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', InterfaceUsuarioController::class)->name('dashboard');
    Route::get('/perfil', [PerfilUsuarioController::class, 'index'])->name('perfil');
    Route::get('/historico', HistoricoSentimentoController::class)->name('historico');
    Route::get('/relacionamento/convite/{token}', [RelacionamentoController::class, 'mostrarConvite']);
    Route::post('/relacionamento/convite/{token}/aceitar', [RelacionamentoController::class, 'aceitarConvite']);
    Route::post('/relacionamento/convite/{token}/recusar', [RelacionamentoController::class, 'recusarConvite']);
    Route::delete('/desfazer-vinculo/{id}', [PerfilUsuarioController::class, 'desfazerVinculo'])->name('desfazer-vinculo');
    Route::get('/dashboard-parceiro/{relacionamento}', [InterfaceUsuarioController::class, 'dashboardParceiro'])->name('dashboard-parceiro');
    Route::get('/historico-parceiro/{relacionamento}', [HistoricoSentimentoController::class, 'historicoParceiro'])->name('historico-parceiro');

    // Rotas para gerenciar convites no perfil
    Route::post('/aceitar-convite-perfil/{id}', [PerfilUsuarioController::class, 'aceitarConvitePerfil'])->name('aceitar-convite-perfil');
    Route::post('/recusar-convite-perfil/{id}', [PerfilUsuarioController::class, 'recusarConvitePerfil'])->name('recusar-convite-perfil');
    Route::delete('/cancelar-convite/{id}', [PerfilUsuarioController::class, 'cancelarConvite'])->name('cancelar-convite');


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

        // Rotas para dados do parceiro
        Route::get('/parceiro/{relacionamento}/relacionamento-itens', [RelacionamentoItemController::class, 'indexParceiro']);
        Route::get('/parceiro/{relacionamento}/estatisticas', [RelacionamentoItemController::class, 'estatisticasParceiro']);

        Route::post('/alterar-senha', [PerfilUsuarioController::class, 'alterarSenha']);
        Route::post('/relacionamento', [PerfilUsuarioController::class, 'atualizarRelacionamento']);
        Route::post('/sentimento', [SentimentoController::class, 'store']);
        Route::get('/sentimento', [SentimentoController::class, 'index']);
        Route::put('/sentimento/{id}', [SentimentoController::class, 'update']);
        Route::get('/sentimento/{id}', [SentimentoController::class, 'show']);
        Route::get('/estatisticasSentimento', [SentimentoController::class, 'estatisticasSentimento']);
        Route::post('/vincular-coparticipante', [PerfilUsuarioController::class, 'vincularCoparticipante']);
    });
});
