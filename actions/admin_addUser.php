<?php
session_start();
include("conn.php");

if(isset($_POST['add_user'])){
    $fullname = $_POST['fullname'];
    $name = explode(" ", $fullname);
    $f_name = $name[0];
    $m_name = isset($name[1]) ? $name[1] : '';
    $l_name = isset($name[2]) ? $name[2] : '';
    $sch_id = $_POST['sch_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = (int) $_POST['role'];
    $date_created = date('Y-m-d');

    if ($role !== 1 && $role !== 3) {
        echo '<script>alert("Invalid role. Only Admin or Organization member can be created.");</script>';
        header('Location: ../users/admin/html/manageAccounts.php');
        exit();
    }

    $sql = "INSERT INTO accounts (f_name, m_name, l_name, sch_id, username, email, password, ut_id, user_status, date_created)
            VALUES ('$f_name', '$m_name', '$l_name',$sch_id, '$username',  '$email', '$password', $role, 1, '$date_created')";

    if($conn->query($sql)){
        echo '<script>alert("User created successfully!");</script>';
    } else {
        echo '<script>alert("Error: " '. $conn->error . '");</script>';
    }

    header("Location: ../users/admin/html/manageAccounts.php");
    exit();
}
?>