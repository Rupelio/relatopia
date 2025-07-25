<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - Relatopia</title>
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
        .title {
            color: #1f2937;
            font-size: 24px;
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
        </div>

        <h1 class="title">Redefinir sua senha</h1>

        <div class="content">
            <p>Olá,</p>

            <p>Você solicitou a redefinição de senha para sua conta no <strong>Relatopia</strong>.</p>

            <p>Para criar uma nova senha, clique no botão abaixo:</p>

            <div style="text-align: center;">
                <a href="{{ route('password.reset', $token) }}" class="button">
                    Redefinir Senha
                </a>
            </div>

            <p>Ou copie e cole este link no seu navegador:</p>
            <p style="word-break: break-all; background-color: #f3f4f6; padding: 10px; border-radius: 4px; font-family: monospace;">
                {{ route('password.reset', $token) }}
            </p>
        </div>

        <div class="warning">
            <strong>⚠️ Importante:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Este link é válido por apenas <strong>60 minutos</strong></li>
                <li>Se você não solicitou esta redefinição, ignore este email</li>
                <li>Por segurança, não compartilhe este link com ninguém</li>
            </ul>
        </div>

        <div class="footer">
            <p>Este email foi enviado automaticamente pelo sistema Relatopia.</p>
            <p>Se você não solicitou esta redefinição de senha, pode ignorar este email com segurança.</p>
        </div>
    </div>
</body>
</html>
