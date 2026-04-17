<?php

declare(strict_types=1);

require __DIR__ . '/conn.php';

if (!isset($_POST['login_submit'])) {
    return;
}

$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if ($email === '') {
    return;
}

$sql = 'SELECT * FROM accounts WHERE username = ? OR email = ? LIMIT 1';
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    return;
}

$stmt->bind_param('ss', $email, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $conn->close();
    echo '<script>alert("Account not found.");</script>';
    echo '<script>window.location = "login.php";</script>';

    return;
}

$user = $result->fetch_assoc();
$stmt->close();

if (!is_array($user) || !password_verify($password, (string) $user['password'])) {
    $conn->close();
    echo '<script>alert("Password is incorrect!");</script>';
    echo '<script>window.location = "login.php";</script>';

    return;
}

$ut = (int) ($user['ut_id'] ?? 0);
// Only Admin (1) and Organization member (3) may use this system.
if (!in_array($ut, [1, 3], true)) {
    $conn->close();
    echo '<script>alert("This account type is not enabled for this portal. Please use an Admin or Organization account.");</script>';
    echo '<script>window.location = "login.php";</script>';

    return;
}

$_SESSION['id'] = (int) $user['user_id'];

echo '<script>alert("Logged in successfully!");</script>';

if ($ut === 1) {
    echo '<script>window.location = "users/admin/html/home.php";</script>';
} else {
    echo '<script>window.location = "users/organization/html/home.php";</script>';
}

$conn->close();
