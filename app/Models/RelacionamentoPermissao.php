<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelacionamentoPermissao extends Model
{
    protected $table = 'relacionamento_permissoes';
    protected $fillable = ['relacionamento_id', 'permissao', 'valor'];

    public function relacionamento(){
        return $this->belongsTo(Relacionamento::class);
    }
}
