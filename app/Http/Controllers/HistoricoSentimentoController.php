<?php

namespace App\Http\Controllers;

use App\Models\Sentimento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoricoSentimentoController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $estatisticas = Sentimento::estatisticaPorUsuario(Auth::id());
        $sentimentos = Sentimento::recentes(Auth::id(), 15);
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');
        $tipoSentimento = $request->input('tipo_sentimento');
        return view('interface.historico', [
            'estatisticas' => $estatisticas,
            'sentimentos' => $sentimentos
        ]);
    }
}
