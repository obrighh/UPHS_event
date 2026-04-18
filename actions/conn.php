<?php
$host = getenv('MYSQLHOST') ?: $_ENV['MYSQLHOST'] ?? '';
$user = getenv('MYSQLUSER') ?: $_ENV['MYSQLUSER'] ?? '';
$pass = getenv('MYSQLPASSWORD') ?: $_ENV['MYSQLPASSWORD'] ?? '';
$db   = getenv('MYSQLDATABASE') ?: $_ENV['MYSQLDATABASE'] ?? '';
$port = (int)(getenv('MYSQLPORT') ?: $_ENV['MYSQLPORT'] ?? 3306);

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>