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
        $eventos = Evento::pendentesNotificacao()
                    ->get()
                    ->filter(function($evento) {
                        $agora = Carbon::now();
                        $tempoNotificacao = Carbon::parse($evento->data_evento)->subMinutes($evento->notificar_minutos_antes);

                        // Verificar se está na janela de notificação (chegou a hora)
                        return $agora >= $tempoNotificacao;
                    });

        if ($eventos->count() === 0) {
            $this->info('Nenhum evento pendente de notificação encontrado.');
            return 0;
        }

        $this->info("Encontrados {$eventos->count()} evento(s) para notificar.");

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
                    $this->line("✅ Email enviado: {$evento->titulo} → {$email}");
                }

                // Marcar como enviada
                $evento->marcarNotificacaoEnviada();
                $sucessos++;

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
