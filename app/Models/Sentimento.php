<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sentimento extends Model
{
    protected $table = 'sentimentos';
    protected $fillable = [
        'user_id',
        'tipo_sentimento',
        'nivel_intensidade',
        'descricao',
        'horario'
    ];
    protected $casts = [
        'horario' => 'datetime',
        'nivel_intensidade' => 'integer'
    ];

    protected $appends = ['usuario_id'];

    // Accessor para compatibilidade com API móvel
    public function getUsuarioIdAttribute()
    {
        return $this->user_id;
    }

    // Mutator para aceitar usuario_id da API móvel
    public function setUsuarioIdAttribute($value)
    {
        $this->attributes['user_id'] = $value;
    }
    public function usuario(): BelongsTo{
        return $this->belongsTo(Usuario::class, 'user_id');
    }
    public static function estatisticaPorUsuario($userId){
        $semana = Carbon::now()->subDays(7);
        $mes = Carbon::now()->subDays(30);
        $total = self::where('user_id', $userId)->count();
        $mediaIntensidade = self::where('user_id', $userId)->avg('nivel_intensidade');
        $maisComum = self::where('user_id', $userId)
                    ->groupBy('tipo_sentimento')
                    ->selectRaw('tipo_sentimento, COUNT(*) as quantidade')
                    ->orderBy('quantidade', 'desc')
                    ->first();
        $estaSemana = self::where('user_id', $userId)
                        ->where('horario', '>=', $semana)
                        ->count();
        $esteMes = self::where('user_id', $userId)
                    ->where('horario', '>=', $mes)
                    ->count();

        return [
            'total' => $total,
            'media_intensidade' => $mediaIntensidade,
            'mais_comum' => $maisComum,
            'esta_semana' => $estaSemana,
            'este_mes' => $esteMes
        ];
    }
    public static function contarPorTipo($userId, $tipo){
        return self::where('user_id', $userId)
                    ->where('tipo_sentimento', $tipo)
                    ->count();
    }
    public static function porPeriodo($userId, $dataInicio, $dataFim){
        return self::where('user_id', $userId)
                    ->where('horario', '>=', $dataInicio)
                    ->where('horario', '<=', $dataFim)
                    ->orderBy('horario', 'desc');
    }
    public static function recentes($userId, $limite = 5){
        return self::where('user_id', $userId)
                    ->orderBy('horario', 'desc')
                    ->limit($limite)
                    ->get();
    }
    public static function porIntensidade($userId, $minimo, $maximo){
        return self::where('user_id', $userId)
                    ->where('nivel_intensidade', '>=', $minimo)
                    ->where('nivel_intensidade', '<=', $maximo)
                    ->orderBy('nivel_intensidade', 'desc')
                    ->get();
    }
    public static function sentimentoHoje($userId){
        return self::where('user_id', $userId)
                    ->whereDate('horario', today())
                    ->count();
    }
    public static function ultimoSentimento($userId){
        return self::where('user_id', $userId)
                    ->orderBy('horario', 'desc')
                    ->first();
    }
}
