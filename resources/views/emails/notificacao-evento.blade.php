<table width="100%" cellpadding="0" cellspacing="0" style="background:#f6f6f6;padding:30px 0;">
    <tr>
        <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background:#fff;border-radius:8px;box-shadow:0 2px 8px #0001;padding:32px;">
                <tr>
                    <td align="center" style="padding-bottom:24px;">
                        <h2 style="color:#10b981;margin:0 0 8px 0;font-family:sans-serif;">🗓️ Lembrete de Evento</h2>
                        <p style="color:#333;font-size:16px;font-family:sans-serif;margin:0;">
                            Olá, <strong>{{ $usuario->name }}</strong>!<br>
                            Seu evento está se aproximando
                        </p>
                    </td>
                </tr>

                <!-- Evento Info -->
                <tr>
                    <td style="padding-bottom:20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f9ff;border:2px solid #0284c7;border-radius:8px;padding:20px;">
                            <tr>
                                <td>
                                    <h3 style="color:#0f172a;font-size:18px;font-family:sans-serif;margin:0 0 8px 0;font-weight:bold;">
                                        {{ $evento->titulo }}
                                    </h3>

                                    @if($evento->descricao)
                                        <p style="color:#64748b;font-size:14px;font-family:sans-serif;margin:0 0 12px 0;line-height:1.4;">
                                            {{ $evento->descricao }}
                                        </p>
                                    @endif

                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding:4px 0;">
                                                <span style="color:#475569;font-size:14px;font-family:sans-serif;font-weight:500;">
                                                    📅 {{ $dataFormatada }}
                                                </span>
                                            </td>
                                            <td style="padding:4px 0;">
                                                <span style="color:#475569;font-size:14px;font-family:sans-serif;font-weight:500;">
                                                    🕐 {{ $horaFormatada }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>

                                    <div style="margin-top:12px;">
                                        <span style="background:#dcfce7;color:#166534;padding:4px 12px;border-radius:20px;font-size:12px;font-family:sans-serif;font-weight:500;margin-right:8px;">
                                            {{ ucfirst($evento->tipo) }}
                                        </span>
                                        <span style="background:#fef3c7;color:#92400e;padding:4px 12px;border-radius:20px;font-size:12px;font-family:sans-serif;font-weight:500;">
                                            {{ ucfirst($evento->categoria) }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Alert -->
                <tr>
                    <td style="padding-bottom:20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#fef3c7;border-left:4px solid #f59e0b;border-radius:6px;padding:16px;">
                            <tr>
                                <td>
                                    <p style="color:#92400e;font-size:14px;font-family:sans-serif;font-weight:500;margin:0;">
                                        ⏰ Este evento acontecerá em {{ $tempoNotificacao }}!
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Mensagem -->
                <tr>
                    <td style="padding-bottom:24px;">
                        <p style="color:#333;font-size:14px;font-family:sans-serif;margin:0;text-align:center;">
                            Não se esqueça de se preparar e aproveitar seu evento!
                        </p>

                        @if($evento->tipo === 'compartilhado')
                            <p style="color:#6366f1;font-size:14px;font-family:sans-serif;margin:12px 0 0 0;text-align:center;font-style:italic;">
                                💕 Este é um evento compartilhado com seu parceiro(a)
                            </p>
                        @endif
                    </td>
                </tr>

                <!-- Botão -->
                <tr>
                    <td align="center" style="padding-bottom:24px;">
                        @if($evento->tipo === 'compartilhado')
                            <a href="{{ route('calendario.casal') }}"
                               style="display:inline-block;padding:12px 32px;background:#10b981;color:#fff;text-decoration:none;border-radius:6px;font-size:16px;font-family:sans-serif;font-weight:bold;">
                                Ver Calendário do Casal
                            </a>
                        @else
                            <a href="{{ route('calendario.individual') }}"
                               style="display:inline-block;padding:12px 32px;background:#10b981;color:#fff;text-decoration:none;border-radius:6px;font-size:16px;font-family:sans-serif;font-weight:bold;">
                                Ver Meu Calendário
                            </a>
                        @endif
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="color:#666;font-size:12px;font-family:sans-serif;text-align:center;">
                        Este email foi enviado automaticamente pelo <strong style="color:#10b981;">Relatópia</strong>.<br>
                        Para gerenciar suas notificações, acesse seu calendário.<br><br>
                        <span style="color:#b91c1c;">Se não solicitou este lembrete, ignore este e-mail.</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
