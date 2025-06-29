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
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');
        $tipoSentimento = $request->input('tipo_sentimento');

        // Comece com a query base
        if ($dataInicio && $dataFim) {
            $query = Sentimento::porPeriodo(Auth::id(), $dataInicio, $dataFim);
        } else {
            $query = Sentimento::where('user_id', Auth::id())->orderBy('horario', 'desc');
        }

        // Filtro por tipo de sentimento
        if ($tipoSentimento) {
            $query = $query->where('tipo_sentimento', $tipoSentimento);
        }

        // Paginação (10 por página, por exemplo)
        $sentimentos = $query->paginate(10)->withQueryString();
        return view('interface.historico', [
            'estatisticas' => $estatisticas,
            'sentimentos' => $sentimentos
        ]);
    }
}
