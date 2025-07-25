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

    // Métodos para dados do parceiro
    public function indexParceiro($relacionamentoId, Request $request) {
        if (!Auth::check()) {
            return response()->json(['error' => 'Não autorizado'], 401);
        }

        $user = Auth::user();
        $relacionamento = \App\Models\Relacionamento::findOrFail($relacionamentoId);

        // Verifica se o usuário faz parte do relacionamento
        if ($relacionamento->user_id_1 !== $user->id && $relacionamento->user_id_2 !== $user->id) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        // Descobre o parceiro
        $parceiroId = $relacionamento->user_id_1 === $user->id ? $relacionamento->user_id_2 : $relacionamento->user_id_1;

        $query = RelacionamentoItem::where('user_id', $parceiroId);
        if ($request->has('categoria')) {
            $query->where('categoria', $request->categoria);
        }
        $itens = $query->orderBy('created_at', 'desc')->get();

        return response()->json($itens);
    }

    public function estatisticasParceiro($relacionamentoId) {
        if (!Auth::check()) {
            return response()->json(['error' => 'Não autorizado'], 401);
        }

        $user = Auth::user();
        $relacionamento = \App\Models\Relacionamento::findOrFail($relacionamentoId);

        // Verifica se o usuário faz parte do relacionamento
        if ($relacionamento->user_id_1 !== $user->id && $relacionamento->user_id_2 !== $user->id) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        // Descobre o parceiro
        $parceiroId = $relacionamento->user_id_1 === $user->id ? $relacionamento->user_id_2 : $relacionamento->user_id_1;

        $estatisticas = RelacionamentoItem::estatisticasPorUsuario($parceiroId);
        return response()->json($estatisticas);
    }
}
