<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;
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
}
