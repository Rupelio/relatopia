<?php

namespace App\Http\Controllers;

use App\Models\RelacionamentoItem;
use App\Models\Sentimento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterfaceUsuarioController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $estatisticasRelacionamento = RelacionamentoItem::estatisticasPorUsuario(Auth::id());
        $estatisticasSentimentos = Sentimento::estatisticaPorUsuario(Auth::id());
        $sentimentoHoje = Sentimento::sentimentoHoje(Auth::id());
        $ultimoSentimento = Sentimento::ultimoSentimento(Auth::id());
        return view('interface.dashboard', [
            'estatisticas' => [
                'relacionamento' => $estatisticasRelacionamento,
                'sentimentos' => [
                    'total' => $estatisticasSentimentos['total'],
                    'hoje' => $sentimentoHoje,
                    'ultimo' => $ultimoSentimento
                ]
            ]
        ]);
    }
}
