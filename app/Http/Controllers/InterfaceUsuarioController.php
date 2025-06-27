<?php

namespace App\Http\Controllers;

use App\Models\RelacionamentoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterfaceUsuarioController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $estatisticas = RelacionamentoItem::estatisticasPorUsuario(Auth::id());
        return view('interface.dashboard', [
            'estatisticas' => $estatisticas
        ]);
    }
}
