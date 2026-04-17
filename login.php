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

  <!-- LOGIN FORM -->
  <form id="loginForm" class="active" method="post" action="login.php">
    <h2>Login</h2>
    <input type="text" id="loginEmail" name="email" placeholder="Email or username" required>
    <input type="password" id="loginPassword" name="password" placeholder="Password" required>
    <button name="login_submit" type="submit">Login</button>
    <div class="links">
      <a href="forgotPassword.php" id="#">Forgot Password?</a>
    </div>
  </form>

  <!-- FORGOT PASSWORD FORM -->
  <form id="forgotForm">
    <h2>Forgot Password</h2>
    <input type="email" id="forgotEmail" placeholder="Enter your email" required>
    <button type="submit">Reset Password</button>
    <div class="links">
      <a href="#" id="toLoginFromForgot">Back to Login</a>
    </div>
  </form>
</div>

<script>
  // Get form references
  const loginForm = document.getElementById("loginForm");
  const registerForm = document.getElementById("registerForm");
  const forgotForm = document.getElementById("forgotForm");

  // Navigation links
  document.getElementById("toRegister").addEventListener("click", () => {
    loginForm.classList.remove("active");
    registerForm.classList.add("active");
    forgotForm.classList.remove("active");
  });

  document.getElementById("toLoginFromRegister").addEventListener("click", () => {
    registerForm.classList.remove("active");
    loginForm.classList.add("active");
  });

  document.getElementById("toForgot").addEventListener("click", () => {
    loginForm.classList.remove("active");
    forgotForm.classList.add("active");
  });

  document.getElementById("toLoginFromForgot").addEventListener("click", () => {
    forgotForm.classList.remove("active");
    loginForm.classList.add("active");
  });
</script>

</body>
</html>
