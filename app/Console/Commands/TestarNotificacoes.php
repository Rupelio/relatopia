<?php

namespace App\Console\Commands;

use App\Models\Evento;
use App\Mail\NotificacaoEventoMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class TestarNotificacoes extends Command
{
    protected $signature = 'test:notificacoes';
    protected $description = 'Testar sistema de notificações - comando de debug';

    public function handle()
    {
        $this->info('🔍 TESTANDO SISTEMA DE NOTIFICAÇÕES...');

        // Primeiro, vamos criar um evento de teste que dispare AGORA
        $this->info('🧪 Criando evento de teste...');

        $eventoTeste = Evento::create([
            'titulo' => 'TESTE DE NOTIFICAÇÃO - ' . now()->format('H:i:s'),
            'descricao' => 'Evento criado automaticamente para testar notificações',
            'data_evento' => now()->addMinutes(5), // 5 minutos no futuro
            'tipo' => 'pessoal',
            'categoria' => 'outro', // Categoria válida
            'notificar_email' => true,
            'notificar_minutos_antes' => 1, // 1 minuto antes (então vai disparar em 4 minutos)
            'notificacao_enviada' => false,
            'usuario_id' => 1, // Assumindo que existe um usuário com ID 1
        ]);

        $this->info("✅ Evento de teste criado: ID {$eventoTeste->id}");

        // Buscar todos os eventos futuros com notificação habilitada
        $eventos = Evento::where('notificar_email', true)
                    ->where('data_evento', '>', Carbon::now())
                    ->where('notificacao_enviada', false)
                    ->orderBy('data_evento')
                    ->get();

        $this->info("📋 Total de eventos futuros com notificação: {$eventos->count()}");

        foreach ($eventos as $evento) {
            $agora = Carbon::now();
            $dataEvento = $evento->data_evento;
            $minutosAntes = $evento->notificar_minutos_antes;
            $horaNotificacao = $dataEvento->copy()->subMinutes($minutosAntes);

            $this->line("\n📅 Evento ID: {$evento->id}");
            $this->line("   Título: {$evento->titulo}");
            $this->line("   Data do evento: {$dataEvento}");
            $this->line("   Notificar {$minutosAntes} min antes: {$horaNotificacao}");
            $this->line("   Agora: {$agora}");

            $diferenca = $agora->diffInMinutes($horaNotificacao, false);

            if ($horaNotificacao->isPast()) {
                $this->error("   ❌ DEVERIA TER ENVIADO! ({$diferenca} min atrasado)");

                // Forçar envio agora
                $this->warn("   🚀 FORÇANDO ENVIO AGORA...");

                try {
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
                        $this->info("   ✅ Email enviado para: {$email}");
                    }

                    // Marcar como enviada
                    $evento->update(['notificacao_enviada' => true]);
                    $this->info("   ✅ Marcado como enviado no banco");

                } catch (\Exception $e) {
                    $this->error("   ❌ ERRO ao enviar: {$e->getMessage()}");
                }

            } elseif ($diferenca <= 2) {
                $this->warn("   ⏰ PRÓXIMO! Faltam {$diferenca} minutos");
            } else {
                $this->line("   ⏳ Faltam {$diferenca} minutos para enviar");
            }
        }

        return 0;
    }
}
