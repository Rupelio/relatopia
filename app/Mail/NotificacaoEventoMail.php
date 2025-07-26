<?php

namespace App\Mail;

use App\Models\Evento;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacaoEventoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $evento;

    public function __construct(Evento $evento)
    {
        $this->evento = $evento;
    }

    public function build()
    {
        $assunto = "Lembrete: {$this->evento->titulo}";

        return $this->subject($assunto)
                    ->view('emails.notificacao-evento')
                    ->with([
                        'evento' => $this->evento,
                        'usuario' => $this->evento->usuario,
                        'dataFormatada' => $this->evento->data_evento->format('d/m/Y'),
                        'horaFormatada' => $this->evento->data_evento->format('H:i'),
                        'tempoNotificacao' => $this->formatarTempoNotificacao($this->evento->notificar_minutos_antes)
                    ]);
    }

    private function formatarTempoNotificacao($minutos)
    {
        if ($minutos < 60) {
            return "{$minutos} minutos";
        } elseif ($minutos < 1440) {
            $horas = floor($minutos / 60);
            return $horas == 1 ? "1 hora" : "{$horas} horas";
        } else {
            $dias = floor($minutos / 1440);
            return $dias == 1 ? "1 dia" : "{$dias} dias";
        }
    }
}
