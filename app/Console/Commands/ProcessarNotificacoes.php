<?php

namespace App\Console\Commands;

use App\Models\Evento;
use App\Mail\NotificacaoEventoMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ProcessarNotificacoes extends Command
{
    protected $signature = 'eventos:notificar';
    protected $description = 'Processar e enviar notificações de eventos';

    public function handle()
    {
        $this->info('Iniciando processamento de notificações de eventos...');

        // Buscar eventos que precisam de notificação
        $eventos = Evento::pendentesNotificacao()->get();

        if ($eventos->count() === 0) {
            $this->info('Nenhum evento pendente de notificação encontrado.');
            return 0;
        }

        $this->info("Encontrados {$eventos->count()} evento(s) para notificar.");

        $sucessos = 0;
        $erros = 0;

        foreach ($eventos as $evento) {
            try {
                // Verificar se ainda está dentro do prazo de notificação
                $agora = Carbon::now();
                $tempoNotificacao = $evento->data_evento->subMinutes($evento->notificar_minutos_antes);

                if ($agora >= $tempoNotificacao && $agora < $evento->data_evento) {
                    // Enviar email
                    Mail::to($evento->usuario->email)->send(new NotificacaoEventoMail($evento));

                    // Marcar como enviada
                    $evento->marcarNotificacaoEnviada();

                    $this->line("✅ Notificação enviada para: {$evento->usuario->email} - Evento: {$evento->titulo}");
                    $sucessos++;
                } else {
                    $this->line("⏭️ Evento fora do prazo de notificação: {$evento->titulo}");
                }
            } catch (\Exception $e) {
                $this->error("❌ Erro ao enviar notificação para evento {$evento->id}: {$e->getMessage()}");
                $erros++;
            }
        }

        $this->info("\n📊 Resumo:");
        $this->info("✅ Notificações enviadas com sucesso: {$sucessos}");
        if ($erros > 0) {
            $this->error("❌ Erros encontrados: {$erros}");
        }

        return 0;
    }
}
