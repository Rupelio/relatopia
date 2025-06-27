<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Authenticatable
{
    use HasFactory;
    protected $table = 'usuarios';
    protected $fillable = [
        'name',
        'email',
        'password',
        'data_inicio_relacionamento',
        'status_relacionamento'
    ];
    protected $hidden = [
        'password',
        'remember_token'
    ];
    protected $casts = [
        'password' => 'hashed',
        'data_inicio_relacionamento' => 'date',
    ];
}
