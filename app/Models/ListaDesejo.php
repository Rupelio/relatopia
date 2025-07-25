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
        return match($this->prioridade) {
            'alta' => 'red',
            'media' => 'yellow',
            'baixa' => 'green',
            default => 'gray'
        };
    }

    public function getPrioridadeIconAttribute(): string
    {
        return match($this->prioridade) {
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
