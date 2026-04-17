<?php 
    session_start();

    if(isset($_POST["verify_otp"])){
        $otp = $_SESSION['otp']; 
        $otp_code = $_POST['verifyOtp'];

        if($otp != $otp_code){
            ?>
           <script>
               alert("Invalid OTP code");
           </script>
           <?php
        }else{
           // mysqli_query($connect, "UPDATE user_details SET status = 1 WHERE email = '$email'");
            ?>
             <script>
                 alert("OTP verified, you may now change your password");
                   window.location.replace("../newPassword.php");
             </script>
             <?php
        }
    }
?>