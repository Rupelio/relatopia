#!/bin/bash

# 🚀 SCRIPT DE CONFIGURAÇÃO AUTOMÁTICA PARA PRODUÇÃO
# Execute este script no seu servidor para configurar tudo automaticamente

echo "🔧 Configurando Sistema de Notificações Automáticas..."

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Erro: Execute este script na pasta raiz do projeto Laravel"
    exit 1
fi

# Obter o caminho absoluto do projeto
PROJECT_PATH=$(pwd)

echo "📁 Projeto detectado em: $PROJECT_PATH"

# Configurar cron job
echo "⏰ Configurando Cron Job..."

# Criar entrada do cron
CRON_JOB="* * * * * cd $PROJECT_PATH && php artisan schedule:run >> /dev/null 2>&1"

# Verificar se o cron já existe
if crontab -l 2>/dev/null | grep -q "schedule:run"; then
    echo "✅ Cron job já configurado"
else
    # Adicionar o cron job
    (crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
    echo "✅ Cron job adicionado com sucesso"
fi

# Verificar configurações
echo "🔍 Verificando configurações..."

# Verificar se os comandos estão registrados
php artisan schedule:list

echo "🎯 Testando sistema..."

# Executar scheduler uma vez para testar
php artisan schedule:run

# Verificar permissões de storage
echo "📝 Verificando permissões..."
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

echo "✅ Configuração concluída!"
echo ""
echo "🎉 SISTEMA TOTALMENTE AUTOMÁTICO CONFIGURADO!"
echo ""
echo "Como funciona:"
echo "1. O cron executa a cada minuto: php artisan schedule:run"
echo "2. O scheduler processa a queue a cada minuto"
echo "3. O backup roda a cada 5 minutos"
echo "4. Emails são enviados automaticamente nos horários definidos"
echo ""
echo "📊 Para monitorar:"
echo "- Logs: tail -f storage/logs/laravel.log"
echo "- Cron: crontab -l"
echo "- Schedule: php artisan schedule:list"
echo ""
echo "🚀 O sistema agora funciona 100% automaticamente!"
