<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lembrete de Evento - Relatopia</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #374151;
            background-color: #f9fafb;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 8px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 30px;
        }
        .evento-card {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #0284c7;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .evento-titulo {
            font-size: 20px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 8px;
        }
        .evento-descricao {
            color: #64748b;
            margin-bottom: 16px;
            line-height: 1.5;
        }
        .evento-info {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 16px;
        }
        .info-item {
            display: flex;
            align-items: center;
            color: #475569;
            font-weight: 500;
        }
        .info-icon {
            width: 16px;
            height: 16px;
            margin-right: 6px;
            color: #0284c7;
        }
        .evento-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .tag {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .tag-tipo {
            background-color: #dcfce7;
            color: #166534;
        }
        .tag-categoria {
            background-color: #fef3c7;
            color: #92400e;
        }
        .alert-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 4px solid #f59e0b;
            padding: 16px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .alert-text {
            color: #92400e;
            font-weight: 500;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer-text {
            color: #64748b;
            font-size: 14px;
            margin: 0;
        }
        .footer-link {
            color: #059669;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .evento-info {
                flex-direction: column;
            }
            .container {
                margin: 10px;
                border-radius: 8px;
            }
            .header, .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🗓️ Lembrete de Evento</h1>
            <p>Seu evento está se aproximando!</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Olá, <strong>{{ $usuario->name }}</strong>!</p>

            <p>Este é um lembrete sobre seu evento que acontecerá em breve:</p>

            <!-- Evento Card -->
            <div class="evento-card">
                <div class="evento-titulo">{{ $evento->titulo }}</div>

                @if($evento->descricao)
                    <div class="evento-descricao">{{ $evento->descricao }}</div>
                @endif

                <div class="evento-info">
                    <div class="info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $dataFormatada }}
                    </div>
                    <div class="info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $horaFormatada }}
                    </div>
                </div>

                <div class="evento-tags">
                    <span class="tag tag-tipo">{{ ucfirst($evento->tipo) }}</span>
                    <span class="tag tag-categoria">{{ ucfirst($evento->categoria) }}</span>
                </div>
            </div>

            <!-- Alert -->
            <div class="alert-box">
                <div class="alert-text">
                    ⏰ Este evento acontecerá em {{ $tempoNotificacao }}!
                </div>
            </div>

            <p>Não se esqueça de se preparar e aproveitar seu evento!</p>

            <!-- Botão -->
            <a href="{{ url('/calendario') }}" class="button">
                Ver Calendário Completo
            </a>

            @if($evento->tipo === 'compartilhado')
                <p><em>💕 Este é um evento compartilhado com seu parceiro(a). Vocês podem vê-lo no calendário juntos!</em></p>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                Este email foi enviado automaticamente pelo
                <a href="{{ url('/') }}" class="footer-link">Relatopia</a>.
                <br>
                Para gerenciar suas notificações, acesse seu
                <a href="{{ url('/calendario') }}" class="footer-link">calendário</a>.
            </p>
        </div>
    </div>
</body>
</html>
