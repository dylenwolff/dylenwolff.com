<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $messageSubject }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f6fb;color:#172033;font-family:Arial,Helvetica,sans-serif;-webkit-text-size-adjust:100%;">
<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">A practical digital improvement idea from Dylen Wolff.</div>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f3f6fb;">
    <tr><td align="center" style="padding:28px 12px;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:640px;">
            <tr>
                <td style="padding:0 4px 14px;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0"><tr>
                        <td style="width:40px;height:40px;border-radius:12px;background:#1265e8;color:#fff;text-align:center;font-size:15px;font-weight:800;letter-spacing:-.5px;">DW</td>
                        <td style="padding-left:12px;"><div style="font-size:16px;line-height:20px;font-weight:800;color:#101828;">Dylen Wolff</div><div style="font-size:12px;line-height:18px;color:#667085;">Software Engineer &amp; Digital Solutions Developer</div></td>
                    </tr></table>
                </td>
            </tr>
            <tr><td style="height:4px;background:#1265e8;border-radius:16px 16px 0 0;"></td></tr>
            <tr>
                <td style="background:#ffffff;border:1px solid #dfe6f1;border-top:0;border-radius:0 0 16px 16px;padding:34px 38px;box-shadow:0 8px 24px rgba(16,24,40,.05);">
                    @foreach(preg_split('/\R{2,}/', trim($messageBody)) as $paragraph)
                        <p style="margin:0 0 18px;font-size:15px;line-height:25px;color:#344054;">{!! nl2br(e($paragraph)) !!}</p>
                    @endforeach

                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:28px;border-top:1px solid #e7ecf3;">
                        <tr><td style="padding-top:22px;font-size:12px;line-height:19px;color:#667085;">
                            <strong style="color:#344054;">Secure, practical digital solutions built around the way each organisation works.</strong><br>
                            <a href="https://dylenwolff.com" style="color:#1265e8;text-decoration:none;">dylenwolff.com</a>
                            &nbsp;&nbsp;•&nbsp;&nbsp;
                            <a href="mailto:connect@dylenwolff.com" style="color:#1265e8;text-decoration:none;">connect@dylenwolff.com</a>
                            &nbsp;&nbsp;•&nbsp;&nbsp; Colombo, Sri Lanka
                        </td></tr>
                    </table>
                </td>
            </tr>
            <tr><td style="padding:14px 18px 0;text-align:center;font-size:11px;line-height:17px;color:#98a2b3;">You received this message because I identified a relevant opportunity for your organisation. If it is not relevant, reply and I will not follow up.</td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>
