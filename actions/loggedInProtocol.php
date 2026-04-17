<?php

declare(strict_types=1);

function keepLogged(int $ut_id): void
{
    switch ($ut_id) {
        case 1:
            header('Location: users/admin/html/home.php');
            exit;
        case 3:
            header('Location: users/organization/html/home.php');
            exit;
        default:
            session_destroy();
            header('Location: login.php');
            exit;
    }
}

require __DIR__ . '/conn.php';

if (!isset($_SESSION['id'])) {
    return;
}

$id = (int) $_SESSION['id'];
if ($id <= 0) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$sql = 'SELECT ut_id FROM accounts WHERE user_id = ? LIMIT 1';
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    return;
}

$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result ? $result->fetch_assoc() : null;
$stmt->close();
$conn->close();

if (!is_array($user)) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$ut_id = (int) ($user['ut_id'] ?? 0);
keepLogged($ut_id);
