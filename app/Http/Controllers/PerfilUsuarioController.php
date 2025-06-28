<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PerfilUsuarioController extends Controller
{
    public function index()
    {
        return view('interface.perfil');
    }
    public function alterarSenha(Request $request){
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.required' => 'A senha atual é obrigatória',
            'new_password.required' => 'A nova senha é obrigatória',
            'new_password.confirmed' => 'A confirmação da senha não confere',
            'new_password.min' => 'A nova senha deve ter pelo menos 8 caracteres',
        ]);
        $user = $request->user();

        if(!Hash::check($request->current_password, $user->password)){
            return response()->json([
                'errors' => [
                    'current_password' => ['A senha atual está incorreta']
                ]
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Senha alterada com sucesso!'
        ]);
    }
    public function atualizarRelacionamento(Request $request){
        $request->validate([
            'data_inicio' => 'nullable|date|before_or_equal:today',
            'status' => 'nullable|in:namoro,noivado,casamento,uniao-estavel'
        ], [
            'data_inicio.date' => 'Data inválida',
            'data_inicio.before_or_equal' => 'A data não pode ser futura',
            'status.in' => 'Status inválido'
        ]);
        $user = $request->user();
        $user->update([
            'data_inicio_relacionamento' => $request->data_inicio,
            'status_relacionamento' => $request->status
        ]);
        return response()->json([
            'message' => 'Informações do relacionamento atualizadas com sucesso!'
        ]);
    }
}
