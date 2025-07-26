<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SimularCronLocal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sistema:simular-cron {--stop : Para a simulação}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simula o cron para teste local - executa o scheduler automaticamente a cada minuto';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('stop')) {
            $this->info('🛑 Para parar a simulação, pressione Ctrl+C');
            return 0;
        }

        $this->info('🚀 SIMULADOR DE CRON PARA TESTE LOCAL');
        $this->info('💡 Este comando simula o cron que você configuraria em produção');
        $this->info('⚡ Executará "php artisan schedule:run" a cada minuto automaticamente');
        $this->newLine();
        $this->comment('🔧 Em produção, configure apenas: * * * * * cd /projeto && php artisan schedule:run');
        $this->newLine();
        $this->info('🏃‍♂️ Iniciando simulação... (Pressione Ctrl+C para parar)');
        $this->newLine();

        $contador = 1;

        while (true) {
            $timestamp = now()->format('Y-m-d H:i:s');
            $this->line("🕐 [{$timestamp}] Execução #{$contador}");

            // Executar o scheduler (simula o que o cron faria)
            Artisan::call('schedule:run');
            $output = Artisan::output();

            // Mostrar apenas se algum comando foi executado
            if (trim($output) && !str_contains($output, 'No scheduled commands are ready to run')) {
                $this->line("   📋 Scheduler executou:");
                $lines = explode("\n", trim($output));
                foreach ($lines as $line) {
                    if (trim($line)) {
                        $this->line("   → " . trim($line));
                    }
                }
            } else {
                $this->comment("   ⏳ Nenhum comando agendado pronto para executar");
            }

            $this->newLine();
            $contador++;

            // Aguardar 60 segundos (1 minuto)
            sleep(60);
        }
    }
}
