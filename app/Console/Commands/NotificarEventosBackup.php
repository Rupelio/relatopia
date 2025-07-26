<?php

namespace App\Console\Commands;

use App\Models\Evento;
use App\Mail\NotificacaoEventoMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class NotificarEventosBackup extends Command
{
    protected $signature = 'eventos:notificar-backup';
    protected $description = 'Sistema backup para notificações de eventos que falharam no job queue';

    public function handle()
    {
        $this->info('🔍 Verificando eventos que precisam de notificação (backup)...');

        // Buscar eventos que precisam de notificação AGORA (com margem de 2 minutos)
        $eventos = Evento::where('notificar_email', true)
                    ->where('notificacao_enviada', false)
                    ->where('data_evento', '>', Carbon::now())
                    ->whereRaw("datetime(data_evento) <= datetime('now', '+' || (notificar_minutos_antes + 2) || ' minutes')")
                    ->whereRaw("datetime(data_evento) >= datetime('now', '+' || (notificar_minutos_antes - 2) || ' minutes')")
                    ->get();

        if ($eventos->count() === 0) {
            $this->info('✅ Nenhum evento pendente encontrado no backup.');
            return 0;
        }

        $this->info("📧 Encontrados {$eventos->count()} evento(s) para notificar via backup.");

        $sucessos = 0;
        $erros = 0;

        foreach ($eventos as $evento) {
            try {
                // Enviar email diretamente
                Mail::to($evento->usuario->email)->send(new NotificacaoEventoMail($evento));

                // Marcar como enviada
                $evento->marcarNotificacaoEnviada();

                $this->line("✅ [BACKUP] Email enviado: {$evento->titulo} → {$evento->usuario->email}");
                $sucessos++;

            } catch (\Exception $e) {
                $this->error("❌ [BACKUP] Erro no evento {$evento->id}: {$e->getMessage()}");
                $erros++;
            }
        }

        $this->info("\n📊 Resumo do Backup:");
        $this->info("✅ Emails enviados: {$sucessos}");
        if ($erros > 0) {
            $this->error("❌ Erros: {$erros}");
        }

        return 0;
    }
}
