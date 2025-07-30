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
                    ->get()
                    ->filter(function($evento) {
                        $agora = Carbon::now();
                        $tempoNotificacao = Carbon::parse($evento->data_evento)->subMinutes($evento->notificar_minutos_antes);

                        // Verificar se está na janela de notificação (±2 minutos)
                        return $tempoNotificacao->between(
                            $agora->copy()->subMinutes(2),
                            $agora->copy()->addMinutes(2)
                        );
                    });

        if ($eventos->count() === 0) {
            $this->info('✅ Nenhum evento pendente encontrado no backup.');
            return 0;
        }

        $this->info("📧 Encontrados {$eventos->count()} evento(s) para notificar via backup.");

        $sucessos = 0;
        $erros = 0;

        foreach ($eventos as $evento) {
            try {
                // Lista de emails para enviar
                $emailsParaEnviar = [$evento->usuario->email];

                // Se for evento compartilhado, adicionar email do parceiro
                if ($evento->tipo === 'compartilhado' && $evento->relacionamento) {
                    $parceiro = $evento->relacionamento->user_id_1 === $evento->usuario_id
                        ? $evento->relacionamento->usuario2
                        : $evento->relacionamento->usuario1;

                    if ($parceiro && $parceiro->email !== $evento->usuario->email) {
                        $emailsParaEnviar[] = $parceiro->email;
                    }
                }

                // Enviar email para todos os destinatários
                foreach ($emailsParaEnviar as $email) {
                    Mail::to($email)->send(new NotificacaoEventoMail($evento));
                    $this->line("✅ [BACKUP] Email enviado: {$evento->titulo} → {$email}");
                }

                // Marcar como enviada
                $evento->marcarNotificacaoEnviada();
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
