@echo off
REM 🚀 SCRIPT DE CONFIGURAÇÃO AUTOMÁTICA PARA WINDOWS

echo 🔧 Configurando Sistema de Notificações Automáticas...

REM Verificar se estamos no diretório correto
if not exist "artisan" (
    echo ❌ Erro: Execute este script na pasta raiz do projeto Laravel
    pause
    exit /b 1
)

REM Obter o caminho absoleto do projeto
set PROJECT_PATH=%cd%

echo 📁 Projeto detectado em: %PROJECT_PATH%

echo ⏰ Para configurar em produção (Linux/Ubuntu):
echo.
echo Adicione esta linha no crontab do servidor:
echo * * * * * cd %PROJECT_PATH% ^&^& php artisan schedule:run ^>^> /dev/null 2^>^&1
echo.

echo 🔍 Verificando configurações locais...

REM Verificar se os comandos estão registrados
php artisan schedule:list

echo.
echo 🎯 Testando sistema localmente...

REM Executar scheduler uma vez para testar
php artisan schedule:run

echo.
echo ✅ Configuração local concluída!
echo.
echo 🎉 SISTEMA CONFIGURADO PARA AUTOMAÇÃO!
echo.
echo Como configurar em produção:
echo 1. Acesse o servidor via SSH
echo 2. Execute: crontab -e
echo 3. Adicione: * * * * * cd %PROJECT_PATH% ^&^& php artisan schedule:run ^>^> /dev/null 2^>^&1
echo 4. Salve e saia
echo.
echo 📊 Para monitorar:
echo - Logs: tail -f storage/logs/laravel.log
echo - Cron: crontab -l
echo - Schedule: php artisan schedule:list
echo.
echo 🚀 Após configurar o cron, o sistema funciona 100%% automaticamente!

pause
