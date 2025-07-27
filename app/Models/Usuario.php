<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'usuarios';

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'data_inicio_relacionamento',
        'status_relacionamento'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'data_inicio_relacionamento' => 'date',
    ];

    public function listaDesejos(): HasMany
    {
        return $this->hasMany(ListaDesejo::class);
    }

    public function itensComprados(): HasMany
    {
        return $this->hasMany(ListaDesejo::class, 'comprado_por');
    }

    public function sentimentos(): HasMany
    {
        return $this->hasMany(Sentimento::class, 'user_id');
    }

    /**
     * Relacionamento ativo do usuário
     */
    public function relacionamentoAtivo()
    {
        return Relacionamento::where(function ($query) {
            $query->where('user_id_1', $this->id)
                  ->orWhere('user_id_2', $this->id);
        })->where('status', 'ativo')->first();
    }

    /**
     * Verifica se o usuário está em um relacionamento ativo
     */
    public function temRelacionamentoAtivo(): bool
    {
        return $this->relacionamentoAtivo() !== null;
    }

    /**
     * Obtém o parceiro do relacionamento ativo
     */
    public function parceiro()
    {
        $relacionamento = $this->relacionamentoAtivo();
        if (!$relacionamento) {
            return null;
        }

        $parceiroId = $relacionamento->user_id_1 === $this->id ?
                     $relacionamento->user_id_2 :
                     $relacionamento->user_id_1;

        return self::find($parceiroId);
    }
}
