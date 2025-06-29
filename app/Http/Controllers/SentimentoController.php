<?php

namespace App\Http\Controllers;

use App\Models\Sentimento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SentimentoController extends Controller
{
    public function store(Request $request){
        if(!Auth::check()){
            return response()->json(['error' => 'Não autorizado'], 401);
        }
        $request->validate([
            'tipo_sentimento' => 'required|string',
            'nivel_intensidade' => 'required|integer|between:1,10',
            'descricao' => 'required|string|max:500',
            'horario' => 'required|date_format:H:i'
        ]);
        $sentimento = Sentimento::create([
            'user_id' => Auth::id(),
            'tipo_sentimento' => $request->tipo_sentimento,
            'nivel_intensidade' => $request->nivel_intensidade,
            'descricao' => $request->descricao,
            'horario' => now()->format('Y-m-d') . ' ' . $request->horario . ':00'
        ]);

        return response()->json($sentimento, 201);
    }
    public function index(Request $request){
        if(!Auth::check()){
            return response()->json(['error' => 'Não autorizado'], 401);
        }
        $query = Sentimento::where('user_id', Auth::id())->orderBy('horario', 'desc')->paginate(15);
        return response()->json($query, 200);
    }
    public function estatisticasSentimento(){
        if (!Auth::check()) {
            return response()->json(['error' => 'Não autorizado'], 401);
        }

        $estatisticasSentimento = Sentimento::estatisticaPorUsuario(Auth::id());
        return response()->json($estatisticasSentimento);
    }
}
