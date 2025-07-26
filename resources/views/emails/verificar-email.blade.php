<table width="100%" cellpadding="0" cellspacing="0" style="background:#f6f6f6;padding:30px 0;">
    <tr>
        <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background:#fff;border-radius:8px;box-shadow:0 2px 8px #0001;padding:32px;">
                <tr>
                    <td align="center" style="padding-bottom:24px;">
                        <h2 style="color:#10b981;margin:0 0 8px 0;font-family:sans-serif;">✉️ Verificar Email</h2>
                        <p style="color:#333;font-size:16px;font-family:sans-serif;margin:0;">
                            Bem-vindo, <strong>{{ $user->name }}</strong>!<br>
                            Você está quase lá! Só falta verificar seu email
                        </p>
                    </td>
                </tr>
                
                <!-- Conteúdo -->
                <tr>
                    <td style="padding-bottom:20px;">
                        <p style="color:#333;font-size:14px;font-family:sans-serif;margin:0 0 16px 0;text-align:center;">
                            Estamos muito felizes em ter você no <strong style="color:#10b981;">Relatópia</strong>!
                        </p>
                        <p style="color:#333;font-size:14px;font-family:sans-serif;margin:0 0 20px 0;text-align:center;">
                            Para garantir a segurança da sua conta e começar a usar todos os recursos, precisamos verificar seu endereço de email.
                        </p>
                    </td>
                </tr>
                
                <!-- Recursos disponíveis -->
                <tr>
                    <td style="padding-bottom:20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;border-radius:6px;padding:16px;">
                            <tr>
                                <td>
                                    <p style="color:#1f2937;font-size:14px;font-family:sans-serif;font-weight:bold;margin:0 0 12px 0;">
                                        🚀 O que você pode fazer após verificar:
                                    </p>
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding:2px 0;">
                                                <span style="color:#374151;font-size:12px;font-family:sans-serif;">
                                                    💑 Conectar-se com seu parceiro(a)
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:2px 0;">
                                                <span style="color:#374151;font-size:12px;font-family:sans-serif;">
                                                    📝 Compartilhar pensamentos e sentimentos
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:2px 0;">
                                                <span style="color:#374151;font-size:12px;font-family:sans-serif;">
                                                    📊 Acompanhar o progresso do relacionamento
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:2px 0;">
                                                <span style="color:#374151;font-size:12px;font-family:sans-serif;">
                                                    🎯 Definir metas juntos
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                
                <!-- Botão -->
                <tr>
                    <td align="center" style="padding-bottom:20px;">
                        <a href="{{ $verificationUrl }}"
                           style="display:inline-block;padding:12px 32px;background:#10b981;color:#fff;text-decoration:none;border-radius:6px;font-size:16px;font-family:sans-serif;font-weight:bold;">
                            Verificar Meu Email
                        </a>
                    </td>
                </tr>
                
                <!-- Link manual -->
                <tr>
                    <td style="padding-bottom:20px;">
                        <p style="color:#333;font-size:12px;font-family:sans-serif;margin:0 0 8px 0;text-align:center;">
                            Ou copie e cole este link no seu navegador:
                        </p>
                        <p style="color:#6b7280;font-size:11px;font-family:monospace;margin:0;text-align:center;word-break:break-all;background:#f3f4f6;padding:8px;border-radius:4px;">
                            {{ $verificationUrl }}
                        </p>
                    </td>
                </tr>
                
                <!-- Aviso importante -->
                <tr>
                    <td style="padding-bottom:24px;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#fef3c7;border-left:4px solid #f59e0b;border-radius:6px;padding:12px;">
                            <tr>
                                <td>
                                    <p style="color:#92400e;font-size:12px;font-family:sans-serif;font-weight:bold;margin:0 0 8px 0;">
                                        ⚠️ Importante:
                                    </p>
                                    <p style="color:#92400e;font-size:11px;font-family:sans-serif;margin:0;">
                                        • Este link é válido por apenas <strong>60 minutos</strong><br>
                                        • Se você não se cadastrou no Relatópia, ignore este email<br>
                                        • Por segurança, não compartilhe este link com ninguém
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                
                <!-- Footer -->
                <tr>
                    <td style="color:#666;font-size:12px;font-family:sans-serif;text-align:center;">
                        Este email foi enviado automaticamente pelo <strong style="color:#10b981;">Relatópia</strong>.<br>
                        Se você não se cadastrou, pode ignorar este email com segurança.<br><br>
                        <span style="color:#10b981;"><strong>Suporte:</strong> Precisa de ajuda? Responda este email!</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
