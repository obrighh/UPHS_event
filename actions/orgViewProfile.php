<?php
session_start();
require 'conn.php';

// Check if user is logged in
if(!isset($_SESSION['id'])){
    header("Location: ../login.php");
    exit();
}

$u_id = $_SESSION['id'];

// Fetch user data
$sql = "SELECT user_id, username, email, f_name, m_name, l_name, sch_id, profile_picture, user_status 
        FROM accounts 
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $u_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
    
    $user_id = $user_data['user_id'];
    $username = $user_data['username'];
    $email = $user_data['email'];
    $f_name = $user_data['f_name'];
    $m_name = $user_data['m_name'];
    $l_name = $user_data['l_name'];
    $sch_id = $user_data['sch_id'];
    $profile_picture = $user_data['profile_picture'];
    $user_status = $user_data['user_status'];
    
    // Set profile picture path
    $profile_pic_path = '../assets/img/avatars/5.png'; // default
    if(!empty($profile_picture)) {
        $profile_pic_path = '../../../uploads/profiles/' . $profile_picture;
    }
} else {
    // User not found, redirect to login
    session_destroy();
    header("Location: ../login.php");
    exit();
}

$stmt->close();
?>