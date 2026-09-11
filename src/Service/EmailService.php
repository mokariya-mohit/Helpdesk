<?php
declare(strict_types=1);

namespace App\Service;

use Cake\Core\Configure;
use Cake\Mailer\Mailer;

/**
 * Clean & Easy-to-read Email Service
 * 
 * Supports:
 * - Gmail SMTP (smtp.gmail.com)
 * - Hostinger SMTP (smtp.hostinger.com)
 * - Custom / cPanel Webmail SMTP
 * - PHP Native Mail fallback
 */
class EmailService
{
    /**
     * Send a formatted Daily Work Update email to clients and team members
     *
     * @param string|array $to Main recipient(s)
     * @param string $subject Email subject line
     * @param string $contentHtml Formatted HTML body content
     * @param string|array $cc Optional Cc recipient(s)
     * @param string|array $bcc Optional Bcc recipient(s)
     * @param array $sender Sender details ['email' => '...', 'name' => '...']
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendDailyUpdateEmail(
        string|array $to,
        string $subject,
        string $contentHtml,
        string|array $cc = '',
        string|array $bcc = '',
        array $sender = []
    ): array {
        $toList = $this->parseEmailList($to);
        $ccList = $this->parseEmailList($cc);
        $bccList = $this->parseEmailList($bcc);

        // 1. Validation: To & Subject
        if (empty($toList)) {
            return [
                'success' => false,
                'message' => 'Please enter at least one valid recipient email address in the "To" field.',
            ];
        }

        if (empty(trim($subject))) {
            return [
                'success' => false,
                'message' => 'Email subject cannot be empty.',
            ];
        }

        if (empty(trim(strip_tags($contentHtml)))) {
            return [
                'success' => false,
                'message' => 'Email body content cannot be empty. Please ensure your tasks are entered.',
            ];
        }

        // 2. Validate email formats
        $invalidTo = $this->findInvalidEmails($toList);
        if (!empty($invalidTo)) {
            return [
                'success' => false,
                'message' => 'Invalid email address in To field: ' . implode(', ', $invalidTo),
            ];
        }

        $invalidCc = $this->findInvalidEmails($ccList);
        if (!empty($invalidCc)) {
            return [
                'success' => false,
                'message' => 'Invalid email address in Cc field: ' . implode(', ', $invalidCc),
            ];
        }

        $invalidBcc = $this->findInvalidEmails($bccList);
        if (!empty($invalidBcc)) {
            return [
                'success' => false,
                'message' => 'Invalid email address in Bcc field: ' . implode(', ', $invalidBcc),
            ];
        }

        // 3. Sender Configuration - Always use Logged-in User Email & Name
        $fromEmail = !empty($sender['email']) ? trim((string)$sender['email']) : '';
        $fromName = !empty($sender['name']) ? trim((string)$sender['name']) : '';

        if (empty($fromEmail)) {
            $defaultFrom = Configure::read('Email.default.from', 'no-reply@helpdesk.local');
            if (is_array($defaultFrom)) {
                $fromName = $fromName ?: (array_values($defaultFrom)[0] ?? 'Daily Work Update');
                $fromEmail = array_keys($defaultFrom)[0] ?? 'no-reply@helpdesk.local';
            } else {
                $fromEmail = (string)$defaultFrom;
            }
        }
        if (empty($fromName)) {
            $fromName = 'Daily Work Update';
        }

        // 4. Wrap HTML into a clean, modern, responsive email layout
        $fullHtml = $this->wrapInEmailTemplate($subject, $contentHtml);

        // 5. Dynamic Sender Transport Resolution (Direct, Instant & Per-User Isolated)
        try {
            $currentFromEmail = strtolower(trim((string)$fromEmail));
            $userSmtpPassword = trim((string)($sender['smtp_password'] ?? ''));

            // Fallback: If not passed directly, look up user by email from database
            if (empty($userSmtpPassword)) {
                try {
                    $usersTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Users');
                    $u = $usersTable->find()->where(['email' => $currentFromEmail])->first();
                    if ($u && !empty($u->smtp_password)) {
                        $userSmtpPassword = \App\Model\Entity\User::decryptString($u->smtp_password);
                    }
                } catch (\Throwable $lookupEx) {
                    // Ignore table lookup error
                }
            }

            $transportName = 'default';
            $isCustomSmtp = false;

            if (!empty($userSmtpPassword)) {
                // Auto-detect host based on email domain
                $domain = explode('@', $currentFromEmail)[1] ?? 'queueloopsolutions.com';
                $clientDomain = !empty($domain) ? $domain : 'queueloopsolutions.com';

                $isGmail = str_contains($domain, 'gmail.com') || str_contains($domain, 'googlemail.com');
                $smtpHost = $isGmail ? 'ssl://smtp.gmail.com' : 'ssl://smtp.hostinger.com';
                $smtpPort = 465;

                $dynamicTransportKey = 'user_smtp_' . substr(md5($currentFromEmail), 0, 10);
                \Cake\Mailer\TransportFactory::drop($dynamicTransportKey);
                \Cake\Mailer\TransportFactory::setConfig($dynamicTransportKey, [
                    'className' => \Cake\Mailer\Transport\SmtpTransport::class,
                    'host' => $smtpHost,
                    'port' => $smtpPort,
                    'timeout' => 15,
                    'username' => $fromEmail,
                    'password' => $userSmtpPassword,
                    'client' => $clientDomain,
                    'tls' => null,
                ]);

                $transportName = $dynamicTransportKey;
                $isCustomSmtp = true;

                $mailer = new Mailer();
                $mailer->setTransport($transportName);
                $mailer->setFrom([$fromEmail => $fromName]);
                $mailer->setReplyTo([$fromEmail => $fromName]);
                $mailer->setTo($toList);
                if (!empty($ccList)) {
                    $mailer->setCc($ccList);
                }
                if (!empty($bccList)) {
                    $mailer->setBcc($bccList);
                }

                $mailer->setSubject($subject);
                $mailer->setEmailFormat('html');

                try {
                    $mailer->deliver($fullHtml);
                } catch (\Throwable $primarySmtpEx) {
                    // Fallback to Port 587 STARTTLS with valid client EHLO domain if Port 465 SSL was rejected
                    if (!$isGmail) {
                        $fallbackKey = 'user_smtp_tls_' . substr(md5($currentFromEmail), 0, 10);
                        \Cake\Mailer\TransportFactory::drop($fallbackKey);
                        \Cake\Mailer\TransportFactory::setConfig($fallbackKey, [
                            'className' => \Cake\Mailer\Transport\SmtpTransport::class,
                            'host' => 'smtp.hostinger.com',
                            'port' => 587,
                            'timeout' => 15,
                            'username' => $fromEmail,
                            'password' => $userSmtpPassword,
                            'client' => $clientDomain,
                            'tls' => true,
                        ]);

                        $fallbackMailer = new Mailer();
                        $fallbackMailer->setTransport($fallbackKey);
                        $fallbackMailer->setFrom([$fromEmail => $fromName]);
                        $fallbackMailer->setReplyTo([$fromEmail => $fromName]);
                        $fallbackMailer->setTo($toList);
                        if (!empty($ccList)) {
                            $fallbackMailer->setCc($ccList);
                        }
                        if (!empty($bccList)) {
                            $fallbackMailer->setBcc($bccList);
                        }
                        $fallbackMailer->setSubject($subject);
                        $fallbackMailer->setEmailFormat('html');
                        $fallbackMailer->deliver($fullHtml);
                    } else {
                        throw $primarySmtpEx;
                    }
                }
            } else {
                // Check if system default SMTP matches this user's email
                $systemSmtp = \Cake\Mailer\TransportFactory::getConfig('smtp');
                if (!empty($systemSmtp['username']) && strtolower(trim((string)$systemSmtp['username'])) === $currentFromEmail) {
                    $transportName = 'smtp';
                    $isCustomSmtp = true;
                } else {
                    $transportName = 'default';
                    $isCustomSmtp = false;
                }

                $mailer = new Mailer();
                $mailer->setTransport($transportName);
                $mailer->setFrom([$fromEmail => $fromName]);
                $mailer->setReplyTo([$fromEmail => $fromName]);
                $mailer->setTo($toList);
                if (!empty($ccList)) {
                    $mailer->setCc($ccList);
                }
                if (!empty($bccList)) {
                    $mailer->setBcc($bccList);
                }

                $mailer->setSubject($subject);
                $mailer->setEmailFormat('html');
                $mailer->deliver($fullHtml);
            }

            return [
                'success' => true,
                'message' => 'Email sent successfully to ' . implode(', ', $toList) . '!',
                'recipients' => $toList,
                'transport' => $isCustomSmtp ? 'smtp' : 'native',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Email sending error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Save a copy of the sent email to the mailbox's IMAP Sent folder (Hostinger INBOX.Sent)
     * SMTP transfers mail to recipient server, while IMAP stores it in the user's Sent mailbox.
     * Supports both native PHP imap_* extension and pure PHP socket IMAP fallback for live servers.
     */
    protected function saveToImapSentFolder(Mailer $mailer, string $bodyHtml, ?array $smtpConfig = null, string &$debugInfo = ''): bool
    {
        if (empty($smtpConfig)) {
            $debugInfo = 'No SMTP configuration provided';
            return false;
        }

        $user = $smtpConfig['username'] ?? '';
        $pass = $smtpConfig['password'] ?? '';
        if (empty($user) || empty($pass)) {
            $debugInfo = 'SMTP username or password missing';
            return false;
        }

        $rawHost = $smtpConfig['host'] ?? 'smtp.hostinger.com';
        $cleanHost = strtolower(preg_replace('/^(ssl|tls):\/\//i', '', $rawHost));

        // 1. Gmail: Google's SMTP (smtp.gmail.com) natively and automatically copies
        // every authenticated sent message directly into Gmail's "Sent Mail" label.
        if (str_contains($cleanHost, 'gmail.com')) {
            $debugInfo = 'Gmail natively saves to Sent Mail';
            return true;
        }

        // 2. Determine IMAP host based on provider
        if (str_contains($cleanHost, 'office365.com') || str_contains($cleanHost, 'outlook.com')) {
            $imapHost = 'outlook.office365.com';
            $port = 993;
        } elseif (str_contains($cleanHost, 'zoho.com')) {
            $imapHost = 'imap.zoho.com';
            $port = 993;
        } elseif (str_contains($cleanHost, 'yahoo.com')) {
            $imapHost = 'imap.mail.yahoo.com';
            $port = 993;
        } else {
            $imapHost = preg_replace('/^smtp\./i', 'imap.', $cleanHost);
            $port = 993;
        }

        try {
            $fromList = [];
            foreach ($mailer->getFrom() as $addr => $name) {
                $fromList[] = !empty($name) ? '"' . addcslashes($name, '"') . '" <' . $addr . '>' : $addr;
            }
            $toList = [];
            foreach ($mailer->getTo() as $addr => $name) {
                $toList[] = !empty($name) ? '"' . addcslashes($name, '"') . '" <' . $addr . '>' : $addr;
            }
            $ccList = [];
            foreach ($mailer->getCc() as $addr => $name) {
                $ccList[] = !empty($name) ? '"' . addcslashes($name, '"') . '" <' . $addr . '>' : $addr;
            }

            $headers = 'From: ' . implode(', ', $fromList) . "\r\n";
            $headers .= 'To: ' . implode(', ', $toList) . "\r\n";
            if (!empty($ccList)) {
                $headers .= 'Cc: ' . implode(', ', $ccList) . "\r\n";
            }
            $headers .= 'Subject: ' . $mailer->getSubject() . "\r\n";
            $headers .= 'Date: ' . date('r') . "\r\n";
            $headers .= 'Message-ID: <' . bin2hex(random_bytes(16)) . '@' . (explode('@', $user)[1] ?? 'hostinger.com') . ">\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=utf-8\r\n";
            $headers .= "Content-Transfer-Encoding: 8bit\r\n";
            $extraHeaders = $mailer->getMessage()->getHeadersString();
            if (!empty($extraHeaders)) {
                $headers .= $extraHeaders;
            }

            $rawMime = rtrim($headers) . "\r\n\r\n" . $bodyHtml;

            // Universal list of Sent folder names across IMAP servers
            $foldersToTry = [
                'INBOX.Sent',               // Hostinger, Roundcube, Dovecot
                'Sent',                     // Zoho, cPanel, Webmail standard
                'Sent Items',               // Outlook / Exchange / Office 365
                'Sent Messages',            // Apple Mail / macOS Server
                '[Gmail]/Sent Mail',        // Gmail IMAP
                'INBOX/Sent',               // Courier IMAP
            ];

            // Method 1: Try PHP ext-imap if available
            if (function_exists('imap_open')) {
                $baseServer = '{' . $imapHost . ':' . $port . '/imap/ssl/novalidate-cert}';
                foreach ($foldersToTry as $folder) {
                    $mailbox = $baseServer . $folder;
                    $conn = @imap_open($mailbox, $user, $pass, 0, 1);
                    if ($conn) {
                        $appended = @imap_append($conn, $mailbox, $rawMime, "\\Seen");
                        @imap_close($conn);
                        if ($appended) {
                            $debugInfo = "Saved via imap_open to {$folder}";
                            return true;
                        }
                    }
                }
            }

            // Method 2: Pure PHP Socket IMAP Fallback (Works on live Linux/cPanel servers without ext-imap)
            foreach ($foldersToTry as $folder) {
                if ($this->appendViaSocketImap($imapHost, $port, $user, $pass, $folder, $rawMime, $debugInfo)) {
                    return true;
                }
            }
        } catch (\Throwable $e) {
            $debugInfo = 'IMAP Exception: ' . $e->getMessage();
            \Cake\Log\Log::warning('IMAP Sent Box Copy Failed: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Pure PHP Socket-based IMAP APPEND implementation.
     * Supports SSL context (novalidate-cert) and dual port (993 SSL & 143 STARTTLS).
     * Requires zero external PHP extensions (works on any server with standard OpenSSL).
     *
     * @param string $host
     * @param int $port
     * @param string $user
     * @param string $pass
     * @param string $folder
     * @param string $rawMime
     * @param string $debugOut
     * @return bool
     */
    protected function appendViaSocketImap(string $host, int $port, string $user, string $pass, string $folder, string $rawMime, string &$debugOut = ''): bool
    {
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ]);

        $sock = false;
        $activePort = $port;
        $portsToTry = ($port === 993) ? [993, 143] : [$port, 993];

        foreach ($portsToTry as $p) {
            $proto = ($p === 993) ? 'ssl' : 'tcp';
            $sock = @stream_socket_client("{$proto}://{$host}:{$p}", $errno, $errstr, 1, STREAM_CLIENT_CONNECT, $context);
            if ($sock) {
                $activePort = $p;
                break;
            }
        }

        if (!$sock) {
            $debugOut = "Socket connection failed to {$host} (tried 993, 143): {$errstr} ({$errno})";
            return false;
        }
        stream_set_timeout($sock, 8);

        $readResponse = function(string $tag) use ($sock): string {
            $resp = '';
            while (!feof($sock)) {
                $line = fgets($sock);
                if ($line === false) {
                    break;
                }
                $resp .= $line;
                if (str_starts_with($line, $tag . ' ') || str_starts_with($line, '+')) {
                    break;
                }
            }
            return $resp;
        };

        // Welcome greeting
        fgets($sock);

        // If connected via plain TCP on port 143, negotiate STARTTLS
        if ($activePort === 143) {
            fwrite($sock, "A00 STARTTLS\r\n");
            $tlsResp = $readResponse('A00');
            if (str_contains($tlsResp, 'A00 OK')) {
                $crypto = @stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                if (!$crypto) {
                    @fclose($sock);
                    $debugOut = 'STARTTLS crypto handshake failed on port 143';
                    return false;
                }
            } else {
                @fclose($sock);
                $debugOut = 'STARTTLS rejected on port 143: ' . trim($tlsResp);
                return false;
            }
        }

        // 1. LOGIN
        fwrite($sock, "A01 LOGIN \"{$user}\" \"{$pass}\"\r\n");
        $loginResp = $readResponse('A01');
        if (!str_contains($loginResp, 'A01 OK')) {
            @fclose($sock);
            $debugOut = 'IMAP Login failed: ' . trim($loginResp);
            return false;
        }

        // 2. APPEND Command with literal length
        $len = strlen($rawMime);
        fwrite($sock, "A02 APPEND \"{$folder}\" (\\Seen) {{$len}}\r\n");
        $appendPrompt = $readResponse('A02');
        if (!str_starts_with(trim($appendPrompt), '+')) {
            @fwrite($sock, "A03 LOGOUT\r\n");
            @fclose($sock);
            $debugOut = 'IMAP APPEND prompt rejected: ' . trim($appendPrompt);
            return false;
        }

        // 3. Send RAW MIME data followed by CRLF
        fwrite($sock, $rawMime . "\r\n");
        $appendResult = $readResponse('A02');

        // 4. LOGOUT & Close
        @fwrite($sock, "A03 LOGOUT\r\n");
        @fclose($sock);

        $isOk = str_contains($appendResult, 'A02 OK');
        if ($isOk) {
            $debugOut = "Saved to Hostinger {$folder} via port {$activePort}";
        } else {
            $debugOut = 'IMAP APPEND failed: ' . trim($appendResult);
        }

        return $isOk;
    }

    /**
     * Parse and split comma, semicolon, space, or newline-separated email addresses
     *
     * @param string|array $input
     * @return array
     */
    public function parseEmailList(string|array $input): array
    {
        if (is_array($input)) {
            $input = implode(',', $input);
        }

        $input = trim((string)$input);
        if ($input === '') {
            return [];
        }

        $rawList = preg_split('/[,;\s\n\r]+/', $input);
        $valid = [];

        foreach ($rawList as $item) {
            $email = trim($item);
            if ($email !== '') {
                $valid[] = strtolower($email);
            }
        }

        return array_values(array_unique($valid));
    }

    /**
     * Filter and find any invalid email strings
     *
     * @param array $emails
     * @return array
     */
    public function findInvalidEmails(array $emails): array
    {
        $invalid = [];
        foreach ($emails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $invalid[] = $email;
            }
        }
        return $invalid;
    }

    /**
     * Automatically convert plain text URLs into clickable hyperlinks with email-friendly inline styles
     *
     * @param string $html
     * @return string
     */
    public static function autolinkUrls(string $html): string
    {
        // Convert URLs that are not already inside an <a href=...> tag
        $pattern = '/(?<!href=["\'])(?<!src=["\'])(https?:\/\/[^\s<"\']+(?:[^\s<.,:;"\')\]]))/i';
        $autolinked = preg_replace($pattern, '<a href="$1" target="_blank" rel="noopener noreferrer" style="color: rgb(59, 130, 246); text-decoration: underline; word-break: break-all;">$1</a>', $html);

        // Also match standalone www. links
        $wwwPattern = '/(^|[\s(])(www\.[^\s<"\']+(?:[^\s<.,:;"\')\]]))/i';
        $autolinked = preg_replace($wwwPattern, '$1<a href="https://$2" target="_blank" rel="noopener noreferrer" style="color: rgb(59, 130, 246); text-decoration: underline; word-break: break-all;">$2</a>', $autolinked);

        return $autolinked;
    }

    /**
     * Clean, left-aligned, corporate email HTML wrapper matching standard Gmail appearance
     *
     * @param string $subject
     * @param string $contentHtml
     * @return string
     */
    private function wrapInEmailTemplate(string $subject, string $contentHtml): string
    {
        $processedHtml = self::autolinkUrls($contentHtml);

        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($subject) . '</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13.5px;
            line-height: 1.55;
            color: #222222;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            text-align: left;
        }
        .email-body-content {
            width: 100%;
            margin: 0;
            padding: 0;
            text-align: left;
            background-color: #ffffff;
        }
        ol {
            padding-left: 22px;
            margin-top: 4px;
            margin-bottom: 12px;
            text-align: left;
        }
        ul {
            list-style-type: disc;
            padding-left: 22px;
            margin-top: 3px;
            margin-bottom: 6px;
            text-align: left;
        }
        li {
            margin-bottom: 5px;
            line-height: 1.5;
            text-align: left;
        }
        b {
            color: #111111;
        }
        u {
            text-underline-offset: 2px;
        }
        a {
            color: rgb(59, 130, 246) !important;
            text-decoration: underline;
            cursor: pointer;
        }
        a:hover {
            color: rgb(30, 58, 138) !important;
            text-decoration: none !important;
        }
    </style>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size: 13.5px; line-height: 1.55; color: #222222; background-color: #ffffff; margin: 0; padding: 0; text-align: left;">
    <div class="email-body-content" style="width: 100%; margin: 0; padding: 0; text-align: left; background-color: #ffffff;">
        ' . $processedHtml . '
    </div>
</body>
</html>';
    }

    /**
     * Send OTP Verification Email for Password Reset
     *
     * @param string $toEmail Recipient email address
     * @param string $userName Recipient user name
     * @param string $otp 6-digit verification code
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendOtpEmail(string $toEmail, string $userName, string $otp): array
    {
        $subject = 'Password Reset OTP - Helpdesk Journal (' . $otp . ')';
        $safeName = htmlspecialchars($userName ?: 'User');
        $host = !empty($_SERVER['HTTP_HOST']) ? preg_replace('/:[0-9]+$/', '', $_SERVER['HTTP_HOST']) : 'help-desk.gt.tc';
        $fromDomain = ($host === 'localhost' || $host === '127.0.0.1') ? 'help-desk.gt.tc' : $host;
        $fromEmail = 'no-reply@' . $fromDomain;
        $fromName = 'Helpdesk Journal';

        $bodyHtml = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($subject) . '</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f8; font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f4f6f8; padding: 30px 10px;">
        <tr>
            <td align="center">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 500px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); padding: 26px 24px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: 700; letter-spacing: 0.5px;">Helpdesk Daily Journal</h1>
                            <p style="color: #e0e7ff; margin: 4px 0 0; font-size: 13px;">Password Reset Request</p>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="padding: 28px 24px;">
                            <p style="font-size: 15px; color: #1e293b; margin: 0 0 14px;">Hello <strong>' . $safeName . '</strong>,</p>
                            <p style="font-size: 13.5px; color: #475569; line-height: 1.5; margin: 0 0 20px;">We received a request to reset your password. Use the following 6-digit One-Time Password (OTP) to proceed:</p>
                            
                            <div style="background-color: #f1f5f9; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 18px; text-align: center; margin: 20px 0;">
                                <div style="font-size: 32px; font-weight: 800; color: #4f46e5; letter-spacing: 8px; font-family: Consolas, monospace;">' . htmlspecialchars($otp) . '</div>
                                <div style="font-size: 11.5px; color: #64748b; margin-top: 6px;">Valid for 15 minutes</div>
                            </div>

                            <p style="font-size: 12.5px; color: #64748b; line-height: 1.5; margin: 20px 0 0;">If you did not request this password reset, please ignore this email or contact support if you suspect unauthorized access.</p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 14px 24px; text-align: center; border-top: 1px solid #f1f5f9; font-size: 11.5px; color: #94a3b8;">
                            &copy; ' . date('Y') . ' Helpdesk. All Rights Reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

        // 1. Check if Master System SMTP credentials are configured
        $systemConfig = \Cake\Mailer\TransportFactory::getConfig('default') ?: \Cake\Mailer\TransportFactory::getConfig('smtp');
        if (empty($systemConfig['password'])) {
            $systemConfig = \Cake\Core\Configure::read('EmailTransport.default') ?: \Cake\Core\Configure::read('EmailTransport.smtp');
        }
        if (!empty($systemConfig['password']) && !empty($systemConfig['username'])) {
            $smtpUser = $systemConfig['username'];
            $smtpPass = $systemConfig['password'];
            $fromEmail = $smtpUser;
        }

        // 2. Otherwise check if user or any registered admin has an SMTP password stored in MySQL
        if (empty($smtpPass)) {
            try {
                $usersTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Users');
                $u = $usersTable->find()->where(['email' => $toEmail])->first();
                if ($u && !empty($u->smtp_password)) {
                    $smtpUser = $u->email;
                    $smtpPass = \App\Model\Entity\User::decryptString($u->smtp_password);
                }
                if (empty($smtpPass)) {
                    $anyUser = $usersTable->find()->where(['smtp_password IS NOT' => null])->first();
                    if ($anyUser && !empty($anyUser->smtp_password)) {
                        $smtpUser = $anyUser->email;
                        $smtpPass = \App\Model\Entity\User::decryptString($anyUser->smtp_password);
                    }
                }
            } catch (\Throwable $ex) {}
        }

        $sent = false;
        $lastError = null;

        // 1. Try sending via authenticated SMTP
        if (!empty($smtpUser) && !empty($smtpPass)) {
            $domain = explode('@', $smtpUser)[1] ?? 'gmail.com';
            $isGmail = str_contains($domain, 'gmail.com') || str_contains($domain, 'googlemail.com');
            $smtpHost = $isGmail ? 'ssl://smtp.gmail.com' : 'ssl://smtp.hostinger.com';
            $cleanPass = str_replace(' ', '', $smtpPass);

            $transportKey = 'otp_smtp_' . substr(md5($smtpUser), 0, 8);
            \Cake\Mailer\TransportFactory::drop($transportKey);
            \Cake\Mailer\TransportFactory::setConfig($transportKey, [
                'className' => \Cake\Mailer\Transport\SmtpTransport::class,
                'host' => $smtpHost,
                'port' => 465,
                'timeout' => 15,
                'username' => $smtpUser,
                'password' => $cleanPass,
                'client' => $isGmail ? null : $domain,
                'tls' => null,
                'context' => [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                ],
            ]);

            try {
                $mailer = new Mailer();
                $mailer->setTransport($transportKey)
                       ->setFrom([$smtpUser => $fromName])
                       ->setReplyTo([$smtpUser => $fromName])
                       ->setTo($toEmail)
                       ->setSubject($subject)
                       ->setEmailFormat('html')
                       ->deliver($bodyHtml);
                $sent = true;
            } catch (\Throwable $smtpErr) {
                $lastError = $smtpErr->getMessage();
                // Try TLS Port 587 fallback
                try {
                    $tlsKey = 'otp_smtp_tls_' . substr(md5($smtpUser), 0, 8);
                    \Cake\Mailer\TransportFactory::drop($tlsKey);
                    \Cake\Mailer\TransportFactory::setConfig($tlsKey, [
                        'className' => \Cake\Mailer\Transport\SmtpTransport::class,
                        'host' => $isGmail ? 'smtp.gmail.com' : 'smtp.hostinger.com',
                        'port' => 587,
                        'timeout' => 15,
                        'username' => $smtpUser,
                        'password' => $cleanPass,
                        'client' => $isGmail ? null : $domain,
                        'tls' => true,
                        'context' => [
                            'ssl' => [
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true,
                            ],
                        ],
                    ]);
                    $mailer = new Mailer();
                    $mailer->setTransport($tlsKey)
                           ->setFrom([$smtpUser => $fromName])
                           ->setTo($toEmail)
                           ->setSubject($subject)
                           ->setEmailFormat('html')
                           ->deliver($bodyHtml);
                    $sent = true;
                } catch (\Throwable $tlsErr) {
                    $lastError = $tlsErr->getMessage();
                }
            }
        }

        // 2. Try default CakePHP mailer transport
        if (!$sent) {
            try {
                $mailer = new Mailer('default');
                $mailer->setFrom([$fromEmail => $fromName])
                       ->setReplyTo([$fromEmail => $fromName])
                       ->setTo($toEmail)
                       ->setSubject($subject)
                       ->setEmailFormat('html')
                       ->deliver($bodyHtml);
                $sent = true;
            } catch (\Throwable $e) {
                if (!$lastError) $lastError = $e->getMessage();
            }
        }

        // 3. Fallback to PHP mail() function
        if (!$sent) {
            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
            $headers .= "From: " . $fromName . " <" . $fromEmail . ">\r\n";
            $headers .= "Reply-To: " . $fromEmail . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
            $sent = @mail($toEmail, $subject, $bodyHtml, $headers, "-f" . $fromEmail);
        }

        return [
            'success' => $sent,
            'message' => $sent ? 'OTP sent to your email address.' : 'Failed to send OTP email.',
            'sent' => $sent,
            'error' => $lastError,
            'otp' => $otp,
        ];
    }
}
