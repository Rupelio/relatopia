<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelacionamentoItem extends Model
{
    use HasFactory;

    protected $table = 'relacionamento_itens';

    protected $fillable = [
        'user_id',
        'categoria',
        'descricao',
        'resolvido'
    ];

    protected $casts = [
        'resolvido' => 'boolean'
    ];

    public function usuario(): BelongsTo{
        return $this->belongsTo(Usuario::class, 'user_id');
    }
    public static function estatisticasPorUsuario($userId){
        return [
            'reclamacoes' => self::contarPorCategoria($userId, 'reclamacoes'),
            'positivos' => self::contarPorCategoria($userId, 'positivos'),
            'meus_desejos' => self::contarPorCategoria($userId, 'meus_desejos'),
            'nossos_desejos' => self::contarPorCategoria($userId, 'nossos_desejos'),
            'melhorar_mim' => self::contarPorCategoria($userId, 'melhorar_mim'),
            'melhorar_juntos' => self::contarPorCategoria($userId, 'melhorar_juntos'),
            'total_itens' => self::totalPorUsuario($userId),
            'total_desejos' => self::totalDesejos($userId),
            'total_melhorias' => self::totalMelhorias($userId),
        ];
    }
    public static function contarPorCategoria($userId, $categoria){
        return self::where('user_id', $userId)
                    ->where('categoria', $categoria)
                    ->count();
    }
    public static function totalPorUsuario($userId){
        return self::where('user_id', $userId)->count();
    }
    public static function totalDesejos($userId){
        return self::where('user_id', $userId)
                ->whereIn('categoria', ['meus_desejos', 'nossos_desejos'])
                ->count();
    }

    public static function totalMelhorias($userId){
        return self::where('user_id', $userId)
                ->whereIn('categoria', ['melhorar_mim', 'melhorar_juntos'])
                ->count();
    }
}
