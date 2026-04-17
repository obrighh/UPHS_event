<?php

declare(strict_types=1);

/**
 * Resolve who receives public contact inquiries + send formatted mail.
 */

function accounts_inquiry_notify_column_exists(mysqli $conn): bool
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }
    $r = @$conn->query("SHOW COLUMNS FROM `accounts` LIKE 'inquiry_notify_email'");
    $cached = $r && $r->num_rows > 0;

    return $cached;
}

/**
 * Inboxes for the public contact form: every active Admin (ut_id = 1).
 * Uses inquiry_notify_email when set; otherwise account email.
 *
 * @return list<string>
 */
function contact_resolve_inbox_recipients(mysqli $conn): array
{
    $hasInquiryCol = accounts_inquiry_notify_column_exists($conn);
    $sql = $hasInquiryCol
        ? 'SELECT `email`, `inquiry_notify_email` FROM `accounts` WHERE `ut_id` = 1 AND `user_status` = 1 ORDER BY `user_id` ASC'
        : 'SELECT `email` FROM `accounts` WHERE `ut_id` = 1 AND `user_status` = 1 ORDER BY `user_id` ASC';

    $res = $conn->query($sql);
    if (!$res) {
        return [];
    }

    $out = [];
    while ($row = $res->fetch_assoc()) {
        $prefer = '';
        if ($hasInquiryCol) {
            $prefer = trim((string) ($row['inquiry_notify_email'] ?? ''));
        }
        $fallback = trim((string) ($row['email'] ?? ''));
        $addr = $prefer !== '' ? $prefer : $fallback;
        if ($addr !== '' && filter_var($addr, FILTER_VALIDATE_EMAIL)) {
            $out[strtolower($addr)] = $addr;
        }
    }

    return array_values($out);
}

function contact_send_inquiry_email(string $to, string $visitorName, string $visitorEmail, string $message, string $fromEnvelopeEmail): bool
{
    // Try using PHPMailer for SMTP authentication (preferred)
    if (file_exists(__DIR__ . '/../includes/PHPMailer.php')) {
        return contact_send_inquiry_email_phpmailer($to, $visitorName, $visitorEmail, $message, $fromEnvelopeEmail);
    }

    // Fallback to native mail() function
    return contact_send_inquiry_email_native($to, $visitorName, $visitorEmail, $message, $fromEnvelopeEmail);
}

function load_contact_smtp_credentials(): array
{
    $smtpEmail = '';
    $smtpPassword = '';
    $configFile = __DIR__ . '/../.env.local';

    if (file_exists($configFile)) {
        include $configFile;
        $smtpEmail = trim((string) ($_ENV['SMTP_EMAIL'] ?? ''));
        $smtpPassword = trim((string) ($_ENV['SMTP_PASSWORD'] ?? ''));
    }

    if ($smtpEmail === '') {
        $smtpEmail = trim((string) getenv('SMTP_EMAIL'));
    }
    if ($smtpPassword === '') {
        $smtpPassword = trim((string) getenv('SMTP_PASSWORD'));
    }

    return ['email' => $smtpEmail, 'password' => $smtpPassword];
}

function contact_send_inquiry_email_phpmailer(string $to, string $visitorName, string $visitorEmail, string $message, string $fromEnvelopeEmail): bool
{
    require_once __DIR__ . '/../includes/PHPMailer.php';
    require_once __DIR__ . '/../includes/SMTP.php';
    require_once __DIR__ . '/../includes/Exception.php';

    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $smtp = load_contact_smtp_credentials();
        $smtpUsername = $smtp['email'] !== '' ? $smtp['email'] : $fromEnvelopeEmail;
        $mail->Username = $smtpUsername;
        $mail->Password = $smtp['password'];
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Check if we have credentials
        if (empty($mail->Username) || empty($mail->Password)) {
            error_log('SMTP credentials not configured in .env.local');
            return false;
        }

        // Email content
        $nameClean = preg_replace("/[\r\n]+/", ' ', trim($visitorName));
        $subject = '[UPHS Events] New website inquiry from ' . $nameClean;
        $submitted = date('Y-m-d H:i:s T');

        $body = "You have received a new inquiry from the campus events website.\r\n\r\n";
        $body .= "────────────────────────────────────────\r\n";
        $body .= 'Name: ' . $visitorName . "\r\n";
        $body .= 'Email (reply to this address): ' . $visitorEmail . "\r\n";
        $body .= 'Submitted: ' . $submitted . "\r\n";
        $body .= "────────────────────────────────────────\r\n\r\n";
        $body .= "Message:\r\n\r\n" . $message . "\r\n\r\n";
        $body .= "— End of inquiry —\r\n";

        // Set sender and recipient
        $mail->setFrom($smtpUsername, 'UPHS Events');
        $mail->addAddress($to);
        $mail->addReplyTo($visitorEmail, $visitorName);

        // Set subject and body
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->isHTML(false);

        return $mail->send();
    } catch (\Exception $e) {
        error_log('PHPMailer Error: ' . $e->getMessage());
        return false;
    }
}

function contact_send_inquiry_email_native(string $to, string $visitorName, string $visitorEmail, string $message, string $fromEnvelopeEmail): bool
{
    $nameClean = preg_replace("/[\r\n]+/", ' ', trim($visitorName));
    $subject = '[UPHS Events] New website inquiry from ' . $nameClean;
    if (function_exists('mb_encode_mimeheader')) {
        $subject = mb_encode_mimeheader($subject, 'UTF-8', 'B', "\r\n");
    }

    $submitted = date('Y-m-d H:i:s T');
    $body = "You have received a new inquiry from the campus events website.\r\n\r\n";
    $body .= "────────────────────────────────────────\r\n";
    $body .= 'Name: ' . $visitorName . "\r\n";
    $body .= 'Email (reply to this address): ' . $visitorEmail . "\r\n";
    $body .= 'Submitted: ' . $submitted . "\r\n";
    $body .= "────────────────────────────────────────\r\n\r\n";
    $body .= "Message:\r\n\r\n" . $message . "\r\n\r\n";
    $body .= "— End of inquiry —\r\n";

    $fromHeader = 'UPHS GMA Events <' . $fromEnvelopeEmail . '>';
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . $fromHeader,
        'Reply-To: ' . $visitorEmail,
        'X-Mailer: PHP/' . PHP_VERSION,
    ];

    return @mail($to, $subject, $body, implode("\r\n", $headers));
}
