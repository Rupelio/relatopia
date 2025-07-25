<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Email - Relatopia</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            color: #10b981;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .welcome {
            color: #1f2937;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .content {
            color: #4b5563;
            font-size: 16px;
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #10b981, #0d9488);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
        }
        .button:hover {
            opacity: 0.9;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
            text-align: center;
        }
        .features {
            background-color: #f3f4f6;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: #374151;
        }
        .feature-icon {
            color: #10b981;
            margin-right: 10px;
            font-weight: bold;
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Relatopia</div>
            <h1 class="welcome">Bem-vindo, {{ $user->name }}! 🎉</h1>
            <p class="subtitle">Você está quase lá! Só falta verificar seu email.</p>
        </div>

        <div class="content">
            <p>Estamos muito felizes em ter você no <strong>Relatopia</strong>!</p>

            <p>Para garantir a segurança da sua conta e começar a usar todos os recursos, precisamos verificar seu endereço de email.</p>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">
                    ✉️ Verificar Meu Email
                </a>
            </div>

            <p>Ou copie e cole este link no seu navegador:</p>
            <p style="word-break: break-all; background-color: #f3f4f6; padding: 10px; border-radius: 4px; font-family: monospace;">
                {{ $verificationUrl }}
            </p>
        </div>

        <div class="features">
            <h3 style="color: #1f2937; margin-bottom: 15px;">🚀 O que você pode fazer após verificar:</h3>
            <div class="feature-item">
                <span class="feature-icon">💑</span>
                Conectar-se com seu parceiro(a)
            </div>
            <div class="feature-item">
                <span class="feature-icon">📝</span>
                Compartilhar pensamentos e sentimentos
            </div>
            <div class="feature-item">
                <span class="feature-icon">📊</span>
                Acompanhar o progresso do relacionamento
            </div>
            <div class="feature-item">
                <span class="feature-icon">🎯</span>
                Definir metas juntos
            </div>
        </div>

        <div class="warning">
            <strong>⚠️ Importante:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Este link é válido por apenas <strong>60 minutos</strong></li>
                <li>Se você não se cadastrou no Relatopia, ignore este email</li>
                <li>Por segurança, não compartilhe este link com ninguém</li>
            </ul>
        </div>

        <div class="footer">
            <p>Este email foi enviado automaticamente pelo sistema Relatopia.</p>
            <p>Se você não se cadastrou, pode ignorar este email com segurança.</p>
            <p style="margin-top: 15px;">
                <strong>Suporte:</strong> Precisa de ajuda? Responda este email e nossa equipe irá te ajudar!
            </p>
        </div>
    </div>
</body>
</html>
