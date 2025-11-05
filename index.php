<?php
// lecturer/login.php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id,password,is_blocked,name FROM lecturers WHERE email = ?");
    $stmt->execute([$email]);
    $lec = $stmt->fetch();

    if (!$lec) {
        flash_set('error', 'Invalid credentials.....');
        redirect();
    }

    if ((int)$lec['is_blocked'] === 1) {
        flash_set('error', 'Your account is blocked. Contact admin.');
        redirect();
    }

    if (verify_hash($password, $lec['password'])) {
        $_SESSION['lecturer_id'] = (int)$lec['id'];
        $_SESSION['lecturer_name'] = $lec['name'];

        flash_set('success', 'Welcome back.');
        redirect('lecturer/dashboard.php');
    } else {
        flash_set('error', 'Invalid credentials.');
        redirect('');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SEA-AI Login</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="container">
    <div class="left-panel">
        <img src="./Login-amico.png" alt="" class="illustration">
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
         <?php if($msg = flash_get('success')): ?>
      <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded"><?= e($msg) ?></div>
    <?php elseif($msg = flash_get('error')): ?>
      <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded"><?= e($msg) ?></div>
    <?php endif; ?>
<form method="post" stayle="max-width:420px">

        <input type="email" placeholder="Email" name="email">
        <input type="password" placeholder="Password" name="password">
        <a href="#" class="forgot">forgot password?</a>
        <button>LOGIN</button>
</form>
        <p class="signup">
          New to SEA-AI? <a href="lecturer/signup.php">Sign up</a><br>
          <a href="admin/">Admin login</a>
        </p>
      </div>
    </div>
  </div>
</body>
</html>
