<?php
session_start();
include 'db.php';
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $result   = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user     = mysqli_fetch_assoc($result);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php"); exit();
    } else { $error = "Invalid email or password!"; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Login — TrackWise</title>
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
      <h2>Welcome<br><span class="ice-text">Back.</span></h2>
      <p>Your financial dashboard is waiting. Crystal-clear insights into every rupee you spend.</p>
      <div class="hero-stats">
        <div class="hero-stat">
          <div class="s-num">Smart</div>
          <div class="s-lbl">Charts</div>
        </div>
        <div class="hero-stat">
          <div class="s-num">Easy</div>
          <div class="s-lbl">Tracking</div>
        </div>
        <div class="hero-stat">
          <div class="s-num">Clear</div>
          <div class="s-lbl">Reports</div>
        </div>
      </div>
    </div>
  </div>

  <!-- FORM -->
  <div class="auth-panel">
    <div class="form-box">
      <h2>Sign <span>In</span></h2>
      <p class="subtitle">Enter your credentials to continue</p>

      <?php if($error): ?>
        <div class="error-msg">⚠️ <?= $error ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="input-group">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="your@email.com" required>
        </div>
        <div class="input-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="Your password" required>
        </div>
        <button type="submit" class="btn btn-full">Sign In →</button>
      </form>
      <p class="auth-link">No account yet? <a href="register.php">Register Free</a></p>
    </div>
  </div>
</div>
</body>
</html>