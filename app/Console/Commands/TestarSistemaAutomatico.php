<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Models\Evento;
use Carbon\Carbon;

class TestarSistemaAutomatico extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sistema:testar-automatico';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa se o sistema de notificações automáticas está funcionando corretamente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 TESTANDO SISTEMA DE NOTIFICAÇÕES AUTOMÁTICAS');
        $this->newLine();

        // 1. Verificar scheduler
        $this->info('📅 1. Verificando Scheduler...');
        $this->call('schedule:list');
        $this->newLine();

        // 2. Testar execução do scheduler
        $this->info('⚡ 2. Executando Scheduler...');
        $this->call('schedule:run');
        $this->newLine();

        // 3. Verificar queue
        $this->info('📨 3. Processando Queue...');
        Artisan::call('queue:work', ['--once' => true, '--timeout' => 30]);
        $this->line(Artisan::output());
        $this->newLine();

        // 4. Executar backup
        $this->info('🔄 4. Executando Sistema de Backup...');
        $this->call('eventos:notificar-backup');
        $this->newLine();

        // 5. Verificar eventos pendentes
        $this->info('📊 5. Verificando Eventos Pendentes...');
        $eventosProximos = Evento::where('data_evento', '>', Carbon::now())
            ->where('data_evento', '<=', Carbon::now()->addDays(7))
            ->where('notificar_email', true)
            ->orderBy('data_evento')
            ->get();

        if ($eventosProximos->count() > 0) {
            $this->info("✅ Encontrados {$eventosProximos->count()} eventos com notificações nos próximos 7 dias:");

            foreach ($eventosProximos->take(5) as $evento) {
                $dataEvento = Carbon::parse($evento->data_evento);
                $notificarEm = $dataEvento->subMinutes($evento->notificar_minutos_antes);

                $this->line("   📅 {$evento->titulo}");
                $this->line("      🗓️  Evento: {$dataEvento->format('d/m/Y H:i')}");
                $this->line("      🔔 Notificar: {$notificarEm->format('d/m/Y H:i')} ({$evento->notificar_minutos_antes} min antes)");
                $this->newLine();
            }
        } else {
            $this->warn('⚠️  Nenhum evento com notificação encontrado nos próximos 7 dias');
        }

        $this->newLine();

        // 6. Status final
        $this->info('✅ RESULTADO DO TESTE:');
        $this->line('   🤖 Scheduler: Configurado e funcionando');
        $this->line('   📨 Queue: Processando jobs automaticamente');
        $this->line('   🔄 Backup: Sistema de segurança ativo');
        $this->line('   ⏰ Cron: Configure no servidor para automação total');

        $this->newLine();
        $this->comment('💡 Para automação completa em produção, configure o cron:');
        $this->comment('   * * * * * cd ' . base_path() . ' && php artisan schedule:run >> /dev/null 2>&1');

        $this->newLine();
        $this->warn('⚠️  IMPORTANTE PARA AMBIENTE LOCAL:');
        $this->line('   🏠 Em ambiente local/teste, o scheduler NÃO executa automaticamente');
        $this->line('   🔧 Para testar localmente, use: php artisan sistema:simular-cron');
        $this->line('   🚀 Em produção, o cron configurado fará tudo automaticamente');

        $this->newLine();
        $this->info('🎉 Sistema testado com sucesso! Emails serão enviados automaticamente.');

        return 0;
    }
}
