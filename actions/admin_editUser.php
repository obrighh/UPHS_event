<?php
   session_start();
   include("conn.php");

   if(isset($_POST['update_user'])){
       $id = $_POST['userId'];
       $sch_id = $_POST['sch_id'];
       $fullname = $_POST['fullname'];
       $name = explode(" ", $fullname);
       $f_name = $name[0];
       $m_name = $name[1];
       $l_name = $name[2];
       $username = $_POST['username'];
       $email = $_POST['email'];
       $password = $_POST['password'];
       $role = (int) $_POST['role'];
       if ($role !== 1 && $role !== 3) {
           echo '<script>alert("Invalid role. Only Admin or Organization member are allowed.");</script>';
           echo '<script>window.location = "../users/admin/html/manageAccounts.php";</script>';
           exit();
       }

       $sql=
       "UPDATE accounts
        SET f_name = '$f_name'
          , m_name = '$m_name'
          , l_name = '$l_name'
          , sch_id = '$sch_id'
          , email = '$email'
          , username = '$username'
          , ut_id = $role
       ";

       if(!empty($password)){
            $password = password_hash($password, PASSWORD_DEFAULT);
            $sql.= ", password = '$password'";
        }


        $sql.= " WHERE user_id = $id";

       if($conn->query($sql)){
            $conn -> close();
            echo '<script>alert("User is updated successfully!");</script>';
            echo '<script>window.location = "../users/admin/html/manageAccounts.php";</script>'; 
       }
   }
?>