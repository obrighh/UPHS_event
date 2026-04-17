<?php
    session_start();
    require 'conn.php';

    $sql = 
    "SELECT * 
     FROM organizations
    ";

    $stmt = $conn->prepare($sql);
    $stmt ->execute();
    $result = $stmt->get_result();

?>