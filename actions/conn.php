<?php
echo "HOST: " . getenv('MYSQLHOST') . "<br>";
echo "USER: " . getenv('MYSQLUSER') . "<br>";
echo "DB: " . getenv('MYSQLDATABASE') . "<br>";
echo "PORT: " . getenv('MYSQLPORT') . "<br>";
die();
?>