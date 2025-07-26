<?php

namespace App\Jobs;

use App\Models\Evento;
use App\Mail\NotificacaoEventoMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EnviarNotificacaoEventoJob implements ShouldQueue
{
    use Queueable;

    public $evento;

    /**
     * Create a new job instance.
     */
    public function __construct(Evento $evento)
    {
        $this->evento = $evento;

        // Calcular quando executar (data do evento - minutos antes)
        $agora = Carbon::now();
        $dataNotificacao = $evento->data_evento->copy()->subMinutes($evento->notificar_minutos_antes);

        // Se a hora da notificação já passou, executar imediatamente
        if ($dataNotificacao->isPast()) {
            $this->delay(0); // Executar agora
        } else {
            // Agendar para executar no momento certo
            $this->delay($dataNotificacao);
        }
    }    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Verificar se o evento ainda existe e não foi cancelado
            $evento = Evento::find($this->evento->id);

            if (!$evento) {
                Log::info("Evento {$this->evento->id} não existe mais, cancelando notificação");
                return;
            }

            // Verificar se a notificação já foi enviada
            if ($evento->notificacao_enviada) {
                Log::info("Notificação do evento {$evento->id} já foi enviada");
                return;
            }

            // Verificar se o evento ainda está no futuro
            if ($evento->data_evento->isPast()) {
                Log::info("Evento {$evento->id} já passou, cancelando notificação");
                return;
            }

            // Enviar o email
            Mail::to($evento->usuario->email)->send(new NotificacaoEventoMail($evento));

            // Marcar como enviada
            $evento->update(['notificacao_enviada' => true]);

            Log::info("✅ Notificação enviada com sucesso para evento: {$evento->titulo} - Email: {$evento->usuario->email}");

        } catch (\Exception $e) {
            Log::error("❌ Erro ao enviar notificação do evento {$this->evento->id}: " . $e->getMessage());
            throw $e; // Re-throw para que o job seja marcado como falhou
        }
    }
}
