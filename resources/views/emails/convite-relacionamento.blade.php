<table width="100%" cellpadding="0" cellspacing="0" style="background:#f6f6f6;padding:30px 0;">
    <tr>
        <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background:#fff;border-radius:8px;box-shadow:0 2px 8px #0001;padding:32px;">
                <tr>
                    <td align="center" style="padding-bottom:24px;">
                        <h2 style="color:#10b981;margin:0 0 8px 0;font-family:sans-serif;">Convite para Relacionamento</h2>
                        <p style="color:#333;font-size:16px;font-family:sans-serif;margin:0;">
                            Olá! Você foi convidado para o relacionamento:<br>
                            <strong style="color:#6366f1;">{{ $relacionamento->nome }}</strong>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding-bottom:24px;">
                        <a href="{{ url('/relacionamento/convite/'.$relacionamento->token) }}"
                           style="display:inline-block;padding:12px 32px;background:#10b981;color:#fff;text-decoration:none;border-radius:6px;font-size:16px;font-family:sans-serif;font-weight:bold;">
                            Aceitar Convite
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="color:#666;font-size:14px;font-family:sans-serif;text-align:center;">
                        Para aceitar, acesse seu perfil e aceite o convite na área de convites pendentes.<br><br>
                        <span style="color:#b91c1c;">Se não reconhece este convite, ignore este e-mail.</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
