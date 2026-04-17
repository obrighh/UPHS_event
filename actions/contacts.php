<?php

declare(strict_types=1);

/**
 * Public contact form validation and handler functions.
 */

function contact_validate_submission(array $input): bool
{
    $name = trim((string) ($input['name'] ?? ''));
    $email = trim((string) ($input['email'] ?? ''));
    $message = trim((string) ($input['message'] ?? ''));

    if ($name === '' || $email === '' || $message === '') {
        return false;
    }

    if (strlen($name) > 200 || strlen($email) > 200 || strlen($message) > 8000) {
        return false;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    return true;
}

function contact_table_has_column(mysqli $conn, string $column): bool
{
    $col = $conn->real_escape_string($column);
    $r = $conn->query("SHOW COLUMNS FROM `contact` LIKE '{$col}'");

    return $r && $r->num_rows > 0;
}

function handle_contact_submission(): void
{
    require __DIR__ . '/conn.php';
    require_once __DIR__ . '/contact_inquiry_mail.php';

    if (!isset($_POST['submitContact'])) {
        header('Location: ../index.php');
        exit;
    }

    if (!contact_validate_submission($_POST)) {
        header('Location: ../index.php?contact=invalid#contact-section');
        exit;
    }

    $name = trim((string) $_POST['name']);
    $email = trim((string) $_POST['email']);
    $message = trim((string) $_POST['message']);

    $recipients = contact_resolve_inbox_recipients($conn);
    if ($recipients === []) {
        header('Location: ../index.php?contact=not_configured#contact-section');
        exit;
    }

    $hasIsRead = contact_table_has_column($conn, 'is_read');

    if ($hasIsRead) {
        $sql = 'INSERT INTO `contact` (`name`, `email`, `message`, `is_read`) VALUES (?,?,?,0)';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            header('Location: ../index.php?contact=error#contact-section');
            exit;
        }
        $stmt->bind_param('sss', $name, $email, $message);
    } else {
        $sql = 'INSERT INTO `contact` (`name`, `email`, `message`) VALUES (?,?,?)';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            header('Location: ../index.php?contact=error#contact-section');
            exit;
        }
        $stmt->bind_param('sss', $name, $email, $message);
    }

    if (!$stmt->execute()) {
        $stmt->close();
        header('Location: ../index.php?contact=error#contact-section');
        exit;
    }
    $stmt->close();

    $fromEnvelope = $recipients[0];
    $mailOk = true;
    foreach ($recipients as $to) {
        if (!contact_send_inquiry_email($to, $name, $email, $message, $fromEnvelope)) {
            $mailOk = false;
        }
    }

    if ($mailOk) {
        header('Location: ../index.php?contact=success#contact-section');
    } else {
        // Saved to database; mail may be disabled on localhost (configure sendmail/SMTP for production).
        header('Location: ../index.php?contact=saved_no_mail#contact-section');
    }
    exit;
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    handle_contact_submission();
}
