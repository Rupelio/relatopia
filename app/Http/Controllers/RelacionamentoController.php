<?php

namespace App\Http\Controllers;

use App\Models\Relacionamento;
use Illuminate\Http\Request;

class RelacionamentoController extends Controller
{
    public function mostrarConvite($token){
        $relacionamento = Relacionamento::where('token', $token)->where('status', 'pendente')->firstOrFail();
        return view('interface.convite', compact('relacionamento'));
    }
    public function aceitarConvite(Request $request, $token){
    $relacionamento = Relacionamento::where('token', $token)->where('status', 'pendente')->firstOrFail();
    // Confirme se o usuário logado é o user_id_2
    if ($request->user()->id !== $relacionamento->user_id_2) {
        abort(403);
    }
    $relacionamento->status = 'ativo';
    $relacionamento->save();
    // Redirecione ou mostre mensagem de sucesso
    return redirect('/perfil')->with('success', 'Convite aceito com sucesso!');
    }
    public function recusarConvite(Request $request, $token){
    $relacionamento = Relacionamento::where('token', $token)->where('status', 'pendente')->firstOrFail();
    // Confirme se o usuário logado é o user_id_2
    if ($request->user()->id !== $relacionamento->user_id_2) {
        abort(403);
    }
    $relacionamento->status = 'recusado';
    $relacionamento->save();
    // Redirecione ou mostre mensagem de sucesso
    return redirect('/perfil')->with('success', 'Convite aceito com sucesso!');
    }
}
