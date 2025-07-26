<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descricao',
        'data_evento',
        'tipo',
        'categoria',
        'notificar_email',
        'notificar_minutos_antes',
        'notificacao_enviada',
        'usuario_id',
        'relacionamento_id'
    ];

    protected $casts = [
        'data_evento' => 'datetime',
        'notificar_email' => 'boolean',
        'notificacao_enviada' => 'boolean',
    ];

    /**
     * Relacionamento com o usuário que criou o evento
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Relacionamento com o relacionamento (para eventos compartilhados)
     */
    public function relacionamento()
    {
        return $this->belongsTo(Relacionamento::class);
    }

    /**
     * Verifica se o evento é compartilhado
     */
    public function isCompartilhado()
    {
        return $this->tipo === 'compartilhado';
    }

    /**
     * Verifica se o evento é pessoal
     */
    public function isPessoal()
    {
        return $this->tipo === 'pessoal';
    }

    /**
     * Retorna a data formatada em português
     */
    public function getDataFormatadaAttribute()
    {
        return $this->data_evento->locale('pt_BR')->format('d/m/Y H:i');
    }

    /**
     * Verifica se o evento já passou
     */
    public function jaPassou()
    {
        return $this->data_evento->isPast();
    }

    /**
     * Retorna quantos dias faltam para o evento
     */
    public function diasRestantes()
    {
        if ($this->jaPassou()) {
            return 0;
        }

        return Carbon::now()->diffInDays($this->data_evento);
    }

    /**
     * Scope para eventos de um usuário específico
     */
    public function scopeParaUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * Scope para eventos compartilhados de um relacionamento
     */
    public function scopeCompartilhadosDoRelacionamento($query, $relacionamentoId)
    {
        return $query->where('relacionamento_id', $relacionamentoId)
                    ->where('tipo', 'compartilhado');
    }

    /**
     * Scope para eventos pessoais
     */
    public function scopePessoais($query)
    {
        return $query->where('tipo', 'pessoal');
    }

    /**
     * Scope para eventos por categoria
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Scope para eventos futuros
     */
    public function scopeFuturos($query)
    {
        return $query->where('data_evento', '>', Carbon::now());
    }

    /**
     * Scope para eventos que precisam de notificação
     */
    public function scopePendentesNotificacao($query)
    {
        return $query->where('notificar_email', true)
                    ->where('notificacao_enviada', false)
                    ->where('data_evento', '>', Carbon::now())
                    ->whereRaw("datetime(data_evento) <= datetime('now', '+' || notificar_minutos_antes || ' minutes')");
    }

    /**
     * Marca a notificação como enviada
     */
    public function marcarNotificacaoEnviada()
    {
        $this->update(['notificacao_enviada' => true]);
    }
}
