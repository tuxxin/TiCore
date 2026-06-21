<?php
namespace Tuxxin\TiCore\Addons\ContactMailer;

use TiCore\Core\Logger;

/**
 * Thin MailerSend REST client (https://api.mailersend.com/v1/email) over curl.
 * Sends transactional email; degrades gracefully when unconfigured.
 */
final class Mailer
{
    private const ENDPOINT = 'https://api.mailersend.com/v1/email';

    /**
     * Send a contact message.
     *
     * @return array{ok:bool, configured:bool, code:int, error:?string}
     *   - configured=false means no API key → caller should log a warning and
     *     still show the thanks page (do NOT surface an error to the visitor).
     */
    public function sendContact(string $name, string $email, string $message): array
    {
        $cfg    = config('contact-mailer') ?? [];
        $apiKey = (string) ($cfg['mailersend_key'] ?? '');

        if ($apiKey === '') {
            return ['ok' => false, 'configured' => false, 'code' => 0, 'error' => 'MailerSend not configured'];
        }

        $from       = (string) ($cfg['from_email'] ?? 'noreply@example.com');
        $fromName   = (string) ($cfg['from_name'] ?? 'TiCore Contact Form');
        $to         = (string) ($cfg['to_email'] ?? 'you@example.com');
        $toName     = (string) ($cfg['to_name'] ?? 'TiCore');
        $prefix     = (string) ($cfg['subject_prefix'] ?? 'TiCore contact');

        $safeName = $name !== '' ? $name : 'Visitor';
        $subject  = "{$prefix}: {$safeName}";
        $text     = "New contact from {$safeName} <{$email}>\n\n{$message}";
        $html     = $this->buildHtml($safeName, $email, $message);

        $payload = [
            'from'     => ['email' => $from, 'name' => $fromName],
            'to'       => [['email' => $to, 'name' => $toName]],
            'reply_to' => ['email' => $email, 'name' => $safeName],
            'subject'  => $subject,
            'html'     => $html,
            'text'     => $text,
        ];

        $ch = curl_init(self::ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
                'X-Requested-With: XMLHttpRequest',
            ],
            CURLOPT_TIMEOUT        => 12,
        ]);
        $response = curl_exec($ch);
        $code     = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($code >= 200 && $code < 300) {
            Logger::info("contact-mailer: sent from {$email}");
            return ['ok' => true, 'configured' => true, 'code' => $code, 'error' => null];
        }

        Logger::error("contact-mailer: MailerSend failed HTTP {$code} — {$response} — {$curlErr}");
        return ['ok' => false, 'configured' => true, 'code' => $code, 'error' => 'send_failed'];
    }

    private function buildHtml(string $name, string $email, string $message): string
    {
        $e   = fn(string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
        $msg = nl2br($e($message));
        $name = $e($name); $email = $e($email);

        return <<<HTML
<!DOCTYPE html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"></head>
<body style="margin:0;padding:0;background:#eef2f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#eef2f7;padding:30px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">
<tr><td style="background:linear-gradient(135deg,#ff7a18 0%,#ad5300 100%);padding:22px 30px;border-radius:12px 12px 0 0;color:#fff;font-size:19px;font-weight:700;">TiCore — New Contact Message</td></tr>
<tr><td style="background:#fff;padding:28px 30px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
  <p style="margin:0 0 6px;color:#64748b;font-size:13px;">From</p>
  <p style="margin:0 0 18px;font-size:15px;font-weight:600;color:#0f172a;">{$name} &lt;<a href="mailto:{$email}" style="color:#c2410c;text-decoration:none;">{$email}</a>&gt;</p>
  <p style="margin:0 0 6px;color:#64748b;font-size:13px;">Message</p>
  <div style="padding:16px;background:#f8fafc;border-radius:8px;font-size:14px;line-height:1.7;color:#1f2937;">{$msg}</div>
</td></tr>
<tr><td style="background:#0f172a;padding:14px 30px;border-radius:0 0 12px 12px;text-align:center;color:rgba(255,255,255,0.55);font-size:12px;">Sent from the TiCore contact-mailer addon — reply directly to respond.</td></tr>
</table>
</td></tr>
</table>
</body></html>
HTML;
    }
}
