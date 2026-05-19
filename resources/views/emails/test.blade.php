<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $mailSubject ?? 'Test email' }}</title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',system-ui,sans-serif;background:#f4f8ff;color:#1e293b;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f8ff;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border-radius:12px;border:1px solid rgba(37,99,199,0.12);">
                    <tr>
                        <td style="padding:24px 28px;">
                            <p style="margin:0 0 8px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;">Goalgus API</p>
                            <h1 style="margin:0 0 16px;font-size:20px;color:#0f2d5c;">Test email</h1>
                            <p style="margin:0 0 16px;font-size:15px;line-height:1.6;white-space:pre-wrap;">{{ $bodyMessage }}</p>
                            <p style="margin:0;font-size:13px;color:#64748b;">Sent at {{ now()->format('Y-m-d H:i:s T') }} from {{ config('app.url') }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
