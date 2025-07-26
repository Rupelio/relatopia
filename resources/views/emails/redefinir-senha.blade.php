<table width="100%" cellpadding="0" cellspacing="0" style="background:#f6f6f6;padding:30px 0;">
    <tr>
        <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background:#fff;border-radius:8px;box-shadow:0 2px 8px #0001;padding:32px;">
                <tr>
                    <td align="center" style="padding-bottom:24px;">
                        <h2 style="color:#10b981;margin:0 0 8px 0;font-family:sans-serif;">🔒 Redefinir Senha</h2>
                        <p style="color:#333;font-size:16px;font-family:sans-serif;margin:0;">
                            Você solicitou a redefinição de senha<br>
                            para sua conta no <strong style="color:#10b981;">Relatópia</strong>
                        </p>
                    </td>
                </tr>
                
                <!-- Conteúdo -->
                <tr>
                    <td style="padding-bottom:20px;">
                        <p style="color:#333;font-size:14px;font-family:sans-serif;margin:0 0 16px 0;text-align:center;">
                            Para criar uma nova senha, clique no botão abaixo:
                        </p>
                    </td>
                </tr>
                
                <!-- Botão -->
                <tr>
                    <td align="center" style="padding-bottom:20px;">
                        <a href="{{ route('password.reset', $token) }}"
                           style="display:inline-block;padding:12px 32px;background:#10b981;color:#fff;text-decoration:none;border-radius:6px;font-size:16px;font-family:sans-serif;font-weight:bold;">
                            Redefinir Senha
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
                            {{ route('password.reset', $token) }}
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
                                        • Se você não solicitou esta redefinição, ignore este email<br>
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
                        Se você não solicitou esta redefinição de senha, pode ignorar este email com segurança.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
