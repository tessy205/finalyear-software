<?php
// admin/index.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id, password FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && verify_hash($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        flash_set('success', 'Welcome back, admin.');
        redirect('dashboard.php');
    } else {
        flash_set('error', 'Invalid admin credentials.');
        redirect('index.php');
    }
}

// Helper: if there is no admin in DB, show instructions to create one via create_admin.php (below)
$stmt = $pdo->query("SELECT COUNT(*) as c FROM admins");
$count = (int)$stmt->fetchColumn();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SEA-AI Login</title>
  <link rel="stylesheet" href="../admin-login.css">
</head>
<body>
  <div class="container">
    <div class="left-panel">
        <img src="../Login-amico.png" alt="" class="illustration">
      <h2>SEA – AI</h2>
      <h4>(SMART EXAM ASSISTANT)</h4>
      <p>
        Welcome to your smart and efficient <br>exam assistant. This platform empowers <br>
        academic staff to quickly and <br>accurately generate examination <br>questions using AI.
      </p>
    </div>

    <div class="right-panel">
      <div class="login-box">
        <h2>LOGIN</h2>
        <form method="post" stayle="max-width:420px">
        <input type="email" placeholder="Email" name="email">
        <input type="password" placeholder="Password" name="password" >
        <a href="./forgot-password.html" class="forgot">forgot password?</a>
        <button>LOGIN</button>
        <p class="signup">
          <a href="../index.php">Lecturer login</a>
        </p>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
