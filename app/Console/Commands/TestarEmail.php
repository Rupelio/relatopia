<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class TestarEmail extends Command
{
    protected $signature = 'test:email-config';
    protected $description = 'Testa configurações de email';

    public function handle()
    {
        $this->info('🔍 TESTANDO CONFIGURAÇÕES DE EMAIL...');
        $this->line('');

        $this->info('📧 Configurações do .env:');
        $this->line('MAIL_MAILER: ' . env('MAIL_MAILER'));
        $this->line('MAIL_HOST: ' . env('MAIL_HOST'));
        $this->line('MAIL_PORT: ' . env('MAIL_PORT'));
        $this->line('MAIL_ENCRYPTION: ' . env('MAIL_ENCRYPTION'));
        $this->line('MAIL_USERNAME: ' . env('MAIL_USERNAME'));
        $this->line('');

        $this->info('⚙️ Configurações do Laravel:');
        $this->line('Default mailer: ' . Config::get('mail.default'));
        $this->line('SMTP host: ' . Config::get('mail.mailers.smtp.host'));
        $this->line('SMTP port: ' . Config::get('mail.mailers.smtp.port'));
        $this->line('SMTP encryption: ' . Config::get('mail.mailers.smtp.encryption'));
        $this->line('SMTP username: ' . Config::get('mail.mailers.smtp.username'));
        $this->line('');

        $this->info('🎯 Teste de envio simples...');

        try {
            \Illuminate\Support\Facades\Mail::raw('Teste de configuração SMTP', function($message) {
                $message->to(env('MAIL_USERNAME'))
                        ->subject('Teste de Configuração');
            });
            $this->info('✅ Email enviado com sucesso!');
        } catch (\Exception $e) {
            $this->error('❌ ERRO: ' . $e->getMessage());
        }

        return 0;
    }
}
