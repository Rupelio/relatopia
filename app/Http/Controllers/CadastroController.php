<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class CadastroController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if($request->isMethod('GET')){
            return view('cadastro.cadastro');
        }
        if($request->isMethod('POST')){
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email:rfc,dns|unique:usuarios',
                'password' => 'required|min:6|confirmed',
            ], [
                'name.required' => 'O nome é obrigatório.',
                'name.max' => 'O nome não pode ter mais de 255 caracteres.',
                'email.required' => 'O email é obrigatório.',
                'email.email' => 'Por favor, digite um email válido (ex: nome@exemplo.com).',
                'email.unique' => 'Este email já está cadastrado.',
                'password.required' => 'A senha é obrigatória.',
                'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
                'password.confirmed' => 'As senhas não coincidem.',
            ]);

            Usuario::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password
            ]);

            return redirect('/login')->with('success', 'Conta criada com sucesso!');
        }
    }
}
