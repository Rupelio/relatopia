<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Relacionamento extends Model
{
    protected $fillable = ['user_id_1', 'user_id_2', 'status', 'token'];

    public function usuario1(): BelongsTo{
        return $this->belongsTo(Usuario::class, 'user_id_1');
    }
    public function usuario2(): BelongsTo{
        return $this->belongsTo(Usuario::class, 'user_id_2');
    }

    public function criarPermissoesPadrao(){
        $permissoes = [
            ['permissao' => 'ver_sentimentos', 'valor' => true],
            ['permissao' => 'editar_sentimentos', 'valor' => false],
        ];
        foreach($permissoes as $p){
            $this->permissoes()->create($p);
        }
    }
    public function permissoes(): HasMany{
        return $this->hasMany(RelacionamentoPermissao::class);
    }
}
