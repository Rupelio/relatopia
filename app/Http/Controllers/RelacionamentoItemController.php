<?php

namespace App\Http\Controllers;

use App\Models\RelacionamentoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RelacionamentoItemController extends Controller
{
    public function store(Request $request){
        if(!Auth::check()){
            return response()->json(['error' => 'Não autorizado'], 401);
        }
        $request->validate([
            'categoria' =>'required|string',
            'descricao' => 'required|string|max:500'
        ]);
        $item = RelacionamentoItem::create([
            'user_id' => Auth::id(),
            'categoria' => $request->categoria,
            'descricao' => $request->descricao,
            'resolvido' => false
        ]);
        return response()->json($item, 201);
    }
    public function index(Request $request){
        if(!Auth::check()){
            return response()->json(['error' => 'Não autorizado'], 401);
        }
        $query = RelacionamentoItem::where('user_id', Auth::id());
        if($request->has('categoria')){
            $query->where('categoria', $request->categoria);
        }
        $itens = $query->orderBy('created_at', 'desc')->get();

        return response()->json($itens);
    }
    public function toggleResolved($id) {
        if(!Auth::check()){
            return response()->json(['error' => 'Não autorizado'], 401);
        }

        $item = RelacionamentoItem::where('user_id', Auth::id())->findOrFail($id);
        $item->resolvido = !$item->resolvido;
        $item->save();

        return response()->json($item);
    }

    // Método para deletar
    public function destroy($id) {
        if(!Auth::check()){
            return response()->json(['error' => 'Não autorizado'], 401);
        }

        $item = RelacionamentoItem::where('user_id', Auth::id())->findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Item removido com sucesso']);
    }
    public function estatisticas(){
        if (!Auth::check()) {
            return response()->json(['error' => 'Não autorizado'], 401);
        }

        $estatisticas = RelacionamentoItem::estatisticasPorUsuario(Auth::id());
        return response()->json($estatisticas);
    }
}
