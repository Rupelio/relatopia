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
    public function update(Request $request, $id){
        if(!Auth::check()){
            return response()->json(['error' => 'Não autorizado'], 401);
        }
        $request->validate([
            'tipo_sentimento' => 'required|string',
            'nivel_intensidade' => 'required|integer|between:1,10',
            'descricao' => 'required|string|max:500',
        ]);
        $query = Sentimento::where('id', $id)
                            ->where('user_id', Auth::id())
                            ->firstOrFail();
        $query->update([
            'tipo_sentimento' => $request->tipo_sentimento,
            'nivel_intensidade' => $request->nivel_intensidade,
            'descricao' => $request->descricao,
        ]);
        return response()->json($query, 200);
    }
    public function show($id){
        if(!Auth::check()){
            return response()->json(['error' => 'Não autorizado'], 401);
        }
        $sentimento = Sentimento::where('id', $id)
                                ->where('user_id', Auth::id())
                                ->firstOrFail();
        return response()->json($sentimento, 200);
    }
    public function estatisticasSentimento(){
        if (!Auth::check()) {
            return response()->json(['error' => 'Não autorizado'], 401);
        }

        $estatisticasSentimento = Sentimento::estatisticaPorUsuario(Auth::id());
        $sentimentoHoje = Sentimento::sentimentoHoje(Auth::id());
        $ultimoSentimento = Sentimento::ultimoSentimento(Auth::id());
        return response()->json([
            'total' => $estatisticasSentimento['total'],
            'hoje' => $sentimentoHoje,
            'ultimo' => $ultimoSentimento,
        ]);
    }
}
