<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Relacionamento;
use App\Mail\VerificarEmailMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

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

            $user = Usuario::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password
            ]);

            // Se veio de um convite, criar relacionamento pendente
            if ($request->convite_id) {
                try {
                    $remetente = Usuario::findOrFail(base64_decode($request->convite_id));

                    // Verificar se não existe relacionamento já
                    $relacionamentoExistente = Relacionamento::where(function($query) use ($user, $remetente){
                        $query->where('user_id_1', $remetente->id)->where('user_id_2', $user->id);
                    })->orWhere(function($query) use ($user, $remetente){
                        $query->where('user_id_1', $user->id)->where('user_id_2', $remetente->id);
                    })->first();

                    if (!$relacionamentoExistente) {
                        $relacionamento = Relacionamento::create([
                            'user_id_1' => $remetente->id,
                            'user_id_2' => $user->id,
                            'status' => 'pendente',
                            'token' => \Illuminate\Support\Str::random(40),
                        ]);

                        $relacionamento->criarPermissoesPadrao();
                    }
                } catch (\Exception $e) {
                    // Log do erro mas não impede o cadastro
                    Log::error('Erro ao processar convite: ' . $e->getMessage());
                }
            }

            // Faz login automático do usuário
            Auth::login($user);

            // Gera URL de verificação de email
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(60),
                [
                    'id' => $user->id,
                    'hash' => sha1($user->email)
                ]
            );

            // Envia email de verificação
            try {
                Mail::to($user->email)->send(new VerificarEmailMail($user, $verificationUrl));
            } catch (\Exception $e) {
                // Se falhar o envio do email, continua mesmo assim
                Log::error('Erro ao enviar email de verificação: ' . $e->getMessage());
            }

            return redirect()->route('verification.notice')->with('success', 'Conta criada com sucesso! Verifique seu email para continuar.');
        }
    }
}
