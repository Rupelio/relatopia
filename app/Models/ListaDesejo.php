<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListaDesejo extends Model
{
    use HasFactory;

    protected $table = 'lista_desejos';

    protected $fillable = [
        'usuario_id',
        'titulo',
        'descricao',
        'link_compra',
        'preco_estimado',
        'prioridade',
        'comprado',
        'comprado_por',
        'data_compra',
        'observacoes'
    ];

    protected $casts = [
        'comprado' => 'boolean',
        'preco_estimado' => 'decimal:2',
        'data_compra' => 'datetime'
    ];

    protected $appends = ['item', 'link', 'preco_real'];

    // Accessors para compatibilidade com API móvel
    public function getItemAttribute()
    {
        return $this->titulo;
    }

    public function getLinkAttribute()
    {
        return $this->link_compra;
    }

    public function getPrecoRealAttribute()
    {
        return $this->comprado ? $this->preco_estimado : null;
    }

    // Converter prioridade entre string e número para API móvel
    public function getPrioridadeAttribute($value)
    {
        $map = ['baixa' => 1, 'media' => 3, 'alta' => 5];
        return $map[$value] ?? 3;
    }

    public function setPrioridadeAttribute($value)
    {
        if (is_numeric($value)) {
            $map = [1 => 'baixa', 2 => 'baixa', 3 => 'media', 4 => 'alta', 5 => 'alta'];
            $this->attributes['prioridade'] = $map[$value] ?? 'media';
        } else {
            $this->attributes['prioridade'] = $value;
        }
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public function compradorPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'comprado_por');
    }

    public function getPrioridadeColorAttribute(): string
    {
        $prioridadeOriginal = $this->getOriginal('prioridade');
        return match($prioridadeOriginal) {
            'alta' => 'red',
            'media' => 'yellow',
            'baixa' => 'green',
            default => 'gray'
        };
    }

    public function getPrioridadeIconAttribute(): string
    {
        $prioridadeOriginal = $this->getOriginal('prioridade');
        return match($prioridadeOriginal) {
            'alta' => '🔥',
            'media' => '⚡',
            'baixa' => '💡',
            default => '📌'
        };
    }

    public function getPrecoFormatadoAttribute(): string
    {
        return $this->preco_estimado ? 'R$ ' . number_format($this->preco_estimado, 2, ',', '.') : 'Preço não informado';
    }
}
