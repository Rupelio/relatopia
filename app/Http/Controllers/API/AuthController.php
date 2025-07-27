<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class AuthController extends Controller
{
    /**
     * Registrar novo usuário
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser um endereço válido.',
            'email.unique' => 'Este email já está em uso.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Usuario::create([
            'name' => $request->nome, // Note: usando 'name' que é o campo na tabela
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Usuário registrado com sucesso!',
            'user' => [
                'id' => $user->id,
                'nome' => $user->name, // Retornando 'name' como 'nome'
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'token' => $token
        ], 201);
    }

    /**
     * Login do usuário
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser um endereço válido.',
            'password.required' => 'A senha é obrigatória.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Usuario::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciais inválidas.'
            ], 401);
        }

        // Revoga todos os tokens existentes
        $user->tokens()->delete();

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login realizado com sucesso!',
            'user' => [
                'id' => $user->id,
                'nome' => $user->name, // Retornando 'name' como 'nome'
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'token' => $token
        ]);
    }

    /**
     * Logout do usuário
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->where('id', $request->user()->currentAccessToken()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso!'
        ]);
    }

    /**
     * Obter dados do usuário autenticado
     */
    public function user(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'nome' => $user->name, // Retornando 'name' como 'nome'
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ]);
    }

    /**
     * Atualizar perfil do usuário
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios,email,' . $user->id,
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser um endereço válido.',
            'email.unique' => 'Este email já está em uso.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        $emailChanged = $user->email !== $request->email;

        $user->update([
            'name' => $request->nome, // Usando 'name' que é o campo na tabela
            'email' => $request->email,
            'email_verified_at' => $emailChanged ? null : $user->email_verified_at,
        ]);

        if ($emailChanged) {
            event(new Registered($user));
        }

        return response()->json([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso!' . ($emailChanged ? ' Verifique seu novo email.' : ''),
            'user' => [
                'id' => $user->id,
                'nome' => $user->name, // Retornando 'name' como 'nome'
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ]);
    }

    /**
     * Atualizar senha do usuário
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'A senha atual é obrigatória.',
            'password.required' => 'A nova senha é obrigatória.',
            'password.min' => 'A nova senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da nova senha não confere.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'A senha atual está incorreta.'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Revoga todos os tokens exceto o atual
        $user->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Senha atualizada com sucesso!'
        ]);
    }

    /**
     * Enviar notificação de verificação de email
     */
    public function sendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email já foi verificado.'
            ], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Email de verificação enviado!'
        ]);
    }

    /**
     * Verificar email
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'hash' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Usuario::findOrFail($request->id);

        if (!hash_equals((string) $request->hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                'success' => false,
                'message' => 'Link de verificação inválido.'
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email já foi verificado.'
            ], 400);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json([
            'success' => true,
            'message' => 'Email verificado com sucesso!'
        ]);
    }

    /**
     * Solicitar redefinição de senha
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:usuarios,email',
        ], [
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser um endereço válido.',
            'email.exists' => 'Não encontramos um usuário com este email.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Aqui você implementaria a lógica de envio de email para redefinição
        // Por agora, retornamos sucesso

        return response()->json([
            'success' => true,
            'message' => 'Se o email existir em nosso sistema, você receberá um link para redefinir sua senha.'
        ]);
    }

    /**
     * Redefinir senha
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'token.required' => 'O token é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser um endereço válido.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Aqui você implementaria a lógica de verificação do token e redefinição
        // Por agora, retornamos sucesso

        return response()->json([
            'success' => true,
            'message' => 'Senha redefinida com sucesso!'
        ]);
    }

    /**
     * Completar onboarding
     */
    public function completeOnboarding(Request $request)
    {
        $user = $request->user();

        // Aqui você pode adicionar lógica específica do onboarding
        // Como salvar preferências, configurações iniciais, etc.

        return response()->json([
            'success' => true,
            'message' => 'Onboarding completado com sucesso!'
        ]);
    }
}
