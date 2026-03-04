<?php
include 'db.php';
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username','$email','$password')";
    if (mysqli_query($conn, $sql)) { header("Location: login.php"); exit(); }
    else { $error = "Username or email already exists!"; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Register — TrackWise</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="bg-scene"></div>
<div class="bg-crystals">
  <div class="crystal-shape"></div>
  <div class="crystal-shape"></div>
  <div class="crystal-shape"></div>
  <div class="crystal-shape"></div>
  <div class="crystal-shape"></div>
  <div class="crystal-shape"></div>
</div>
<div class="snow"></div>

<div class="auth-wrapper">
  <!-- HERO -->
  <div class="auth-hero">
    <div class="auth-hero-bg"></div>
    <div class="hero-content">
      <div class="brand">
        <div class="brand-icon">💰</div>
        <div class="brand-name">TRACKWISE</div>
      </div>
      <h2>Track Smarter.<br><span class="ice-text">Spend Wiser.</span></h2>
      <p>The crystal-clear way to understand your finances. Beautiful analytics, effortless tracking.</p>
      <div class="hero-stats">
        <div class="hero-stat">
          <div class="s-num">100%</div>
          <div class="s-lbl">Free</div>
        </div>
        <div class="hero-stat">
          <div class="s-num">Live</div>
          <div class="s-lbl">Analytics</div>
        </div>
        <div class="hero-stat">
          <div class="s-num">Safe</div>
          <div class="s-lbl">& Secure</div>
        </div>
      </div>
    </div>
  </div>

  <!-- FORM -->
  <div class="auth-panel">
    <div class="form-box">
      <h2>Create <span>Account</span></h2>
      <p class="subtitle">Start your financial clarity journey</p>

      <?php if($error): ?>
        <div class="error-msg">⚠️ <?= $error ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="input-group">
          <label>Username</label>
          <input type="text" name="username" placeholder="Choose your username" required>
        </div>
        <div class="input-group">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="your@email.com" required>
        </div>
        <div class="input-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="Create a strong password" required>
        </div>
        <button type="submit" class="btn btn-full">Create Account →</button>
      </form>
      <p class="auth-link">Already have an account? <a href="login.php">Sign In</a></p>
    </div>
  </div>
</div>
</body>
</html>