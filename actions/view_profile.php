<?php

declare(strict_types=1);

session_start();
require __DIR__ . '/conn.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = (int) $_SESSION['id'];
if ($user_id <= 0) {
    session_destroy();
    header('Location: ../login.php');
    exit;
}

$sql = 'SELECT f_name, m_name, l_name, username FROM accounts WHERE user_id = ? LIMIT 1';

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    header('Location: ../login.php');
    exit;
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!is_array($row)) {
    session_destroy();
    header('Location: ../login.php');
    exit;
}

$f_name = (string) ($row['f_name'] ?? '');
$m_name = (string) ($row['m_name'] ?? '');
$l_name = (string) ($row['l_name'] ?? '');
$username = (string) ($row['username'] ?? '');
