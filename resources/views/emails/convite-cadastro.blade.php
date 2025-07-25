<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convite para Relatópia</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 8px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
        }
        .welcome {
            text-align: center;
            margin-bottom: 30px;
        }
        .welcome h2 {
            color: #1f2937;
            font-size: 24px;
            margin: 0 0 12px 0;
            font-weight: 600;
        }
        .welcome p {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin: 0;
        }
        .invite-card {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
            border-left: 4px solid #10b981;
        }
        .invite-card .avatar {
            width: 60px;
            height: 60px;
            background: #10b981;
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: 600;
        }
        .invite-card h3 {
            color: #1f2937;
            margin: 0 0 8px 0;
            font-size: 18px;
            font-weight: 600;
        }
        .invite-card p {
            color: #6b7280;
            margin: 0;
            font-size: 14px;
        }
        .features {
            margin: 30px 0;
        }
        .features h3 {
            color: #1f2937;
            font-size: 18px;
            margin: 0 0 20px 0;
            text-align: center;
            font-weight: 600;
        }
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .feature-list li {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            color: #4b5563;
            font-size: 14px;
        }
        .feature-list li:before {
            content: "💕";
            margin-right: 12px;
            font-size: 16px;
        }
        .cta-button {
            display: block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
            font-size: 16px;
            margin: 30px 0;
            transition: transform 0.2s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
        }
        .footer {
            background: #f9fafb;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            color: #6b7280;
            font-size: 12px;
            margin: 0;
            line-height: 1.5;
        }
        .footer a {
            color: #10b981;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💕 Relatópia</h1>
            <p>A plataforma para fortalecer relacionamentos</p>
        </div>

        <div class="content">
            <div class="welcome">
                <h2>Você foi convidado(a)!</h2>
                <p>{{ $remetente->name }} quer compartilhar com você uma jornada incrível de crescimento no relacionamento.</p>
            </div>

            <div class="invite-card">
                <div class="avatar">{{ strtoupper(substr($remetente->name, 0, 1)) }}</div>
                <h3>{{ $remetente->name }}</h3>
                <p>{{ $remetente->email }}</p>
            </div>

            <div class="features">
                <h3>O que vocês poderão fazer juntos:</h3>
                <ul class="feature-list">
                    <li>Registrar aspectos positivos do relacionamento</li>
                    <li>Identificar pontos de melhoria pessoal e conjunta</li>
                    <li>Acompanhar o humor e sentimentos diários</li>
                    <li>Criar e gerenciar listas de desejos</li>
                    <li>Definir metas e sonhos compartilhados</li>
                    <li>Visualizar estatísticas do relacionamento</li>
                </ul>
            </div>

            <a href="{{ $linkCadastro }}" class="cta-button">
                🚀 Aceitar convite e criar conta
            </a>

            <div style="text-align: center; margin-top: 20px;">
                <p style="color: #6b7280; font-size: 14px;">
                    <strong>Completamente gratuito!</strong><br>
                    Cadastre-se em menos de 2 minutos e comece hoje mesmo.
                </p>
            </div>
        </div>

        <div class="footer">
            <p>
                Este convite foi enviado por <strong>{{ $remetente->name }}</strong> através da plataforma Relatópia.<br>
                Se você não conhece esta pessoa ou recebeu este email por engano, pode ignorá-lo com segurança.
            </p>
            <p style="margin-top: 12px;">
                <a href="{{ url('/') }}">Visite nosso site</a> para saber mais sobre o Relatópia.
            </p>
        </div>
    </div>
</body>
</html>
