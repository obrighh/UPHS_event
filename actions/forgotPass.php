<?php
    session_start();
    require 'conn.php';
    require('../includes/PHPMailer.php'); 
    require('../includes/SMTP.php'); 
    require('../includes/Exception.php'); 

    //define name spaces
    use PHPMailer\PHPMailer\PHPMailer; 
    use PHPMailer\PHPMailer\SMTP; 
    use PHPMailer\PHPMailer\Exception;

    if(isset($_POST['reset_pass'])){
        $getUser = $_POST['getUser'];

        $sql = 
        "SELECT 
            email
          , username
         FROM 
            accounts
         WHERE username = ? OR email = ?
        ";

        $stmt = $conn->prepare($sql);
        $stmt -> bind_param("ss", $getUser, $getUser);
        $stmt -> execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if($row){
            $_SESSION['user'] = $row['email'];
            $_SESSION['to'] = $getUser; // Store email in session
            
            // Generate OTP
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            
            try {
                //create a instance phpmailer
                $mail = new PHPMailer(true); // Enable exceptions

                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                    ];
                
                //set mailer to use smtp
                $mail->isSMTP();
                //define smtp host
                $mail->Host = "smtp.gmail.com";
                //enable smtp authentication
                $mail->SMTPAuth = true;
                //set type of encryption (ssl/tls)
                $mail->SMTPSecure = 'ssl';
                //set port to connect smtp
                $mail->Port = 465;
                //set gmail user
                $mail->Username = 'msgoloyugo@gmail.com'; // Use full email
                //set gmail password (App Password)
                $mail->Password = 'znzywlxexmlfkmph';
                
                //Recipients
                $mail->setFrom('msgoloyugo@gmail.com', 'OTP Verification');
                $mail->addAddress($getUser);
                
                //Content
                $mail->isHTML(false); // Set to plain text
                $mail->Subject = 'Your verification code';
                $mail->Body = "Dear user, we have received a password reset request. Here is your OTP: $otp";
                
                // Send email
                if($mail->send()) {
                    echo '<script>
                        alert("OTP has been sent to your email");
                        window.location="../verifyCode.php";
                    </script>';
                }
                
            } catch (Exception $e) {
                echo '<script>
                    alert("Email could not be sent. Error: ' . $mail->ErrorInfo . '");
                    window.location="login.php";
                </script>';
            }
        }else{
            echo '
                <script>
                    alert("Username or email not found!");
                    window.location="login.php";
                </script>';
        }
    }
?>