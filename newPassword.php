<?php 
  session_start();
  require 'actions/verifyLogin.php';
  require 'actions/registerUser.php';
  require 'actions/loggedInProtocol.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login & Registration</title>
  <link rel="stylesheet" href="css/login.css">
</head>
<body>

<div class="overlay"></div>

<div class="container">
  <!-- Logo -->
  <div class="logo">
    <img src="img/logo_1.png" alt="Logo">
  </div>

  <!-- FORGOT PASSWORD FORM -->
  <form id="forgotForm" action="actions/newPassword.php" method="POST" class="active">
    <h2>Forgot Password</h2>
    <input type="text" name="new_pass" id="#" placeholder="Enter new password" required>
    <input type="password" name="confirm_pass" id="#" placeholder="Confirm Password" required>
    <button name="send_password" type="submit">Comfirm</button>
    <div class="links">
      <a href="login.php">Back to Login</a>
    </div>
  </form>
</div>

</body>
</html>
