<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'token',
        'created_at'
    ];

    public $timestamps = false;

    // Tabela padrão do Laravel para reset de senha não tem ID auto-incremento
    protected $primaryKey = null;
    public $incrementing = false;

    /**
     * Verifica se o token ainda é válido (60 minutos)
     */
    public function isExpired()
    {
        return $this->created_at < now()->subMinutes(60);
    }

    /**
     * Encontra um token válido
     */
    public static function findValidToken($email, $token)
    {
        $tokenRecord = self::where('email', $email)
                          ->where('token', $token)
                          ->first();

        if (!$tokenRecord || $tokenRecord->isExpired()) {
            return null;
        }

        return $tokenRecord;
    }

    /**
     * Remove tokens expirados
     */
    public static function removeExpiredTokens()
    {
        self::where('created_at', '<', now()->subMinutes(60))->delete();
    }

    /**
     * Remove todos os tokens de um email
     */
    public static function removeByEmail($email)
    {
        self::where('email', $email)->delete();
    }
}
