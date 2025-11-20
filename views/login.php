<?php
session_start();
if (isset($_SESSION['user']['logged_in']) && $_SESSION['user']['logged_in'] === true) {
  header("Location: dashboard.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login | PBD Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/style/login.css">
</head>
<body>
  <div class="login-wrapper">
    <div class="login-box">
      <div class="login-avatar">
        <i class="bi bi-person"></i>
      </div>

      <h3 class="login-title">Login </h3>

      <form action="../scripts/login_post.php" method="POST">
        <div class="form-group">
          <div class="input-icon">
            <i class="bi bi-person-fill"></i>
            <input type="text" name="username" placeholder="Username" required>
          </div>
        </div>

        <div class="form-group">
          <div class="input-icon">
            <i class="bi bi-lock-fill"></i>
            <input type="password" name="password" placeholder="Password" required>
          </div>
        </div>

        <div class="options">
          <label><input type="checkbox"> Remember me</label>
          <a href="#" class="forgot-link">Forgot Password?</a>
        </div>

        <button type="submit" class="btn-login">LOGIN</button>
      </form>
    </div>
  </div>

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>
