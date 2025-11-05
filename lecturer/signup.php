<?php
// lecturer/signup.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $access_code = trim($_POST['access_code'] ?? '');
    $csrf = $_POST['csrf'] ?? '';

    if (!csrf_check($csrf)) {
        flash_set('error', 'Invalid CSRF token.');
        redirect('signup.php');
    }

    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$password || !$access_code) {
        flash_set('error', 'All fields are required.');
        redirect('signup.php');
    }

    if ($password !== $confirm) {
        flash_set('error', 'Passwords do not match.');
         redirect('signup.php');
    }

    // verify access code
    $stmt = $pdo->prepare("SELECT id,is_used FROM access_codes WHERE email = ? AND code = ? LIMIT 1");
    $stmt->execute([$email, $access_code]);
    $ac = $stmt->fetch();

    if (!$ac || (int)$ac['is_used'] === 1) {
        flash_set('error', 'Invalid or used access code.');
         redirect('signup.php');
    }

    // create lecturer
    $hash = make_hash($password);
    try {
        $stmt = $pdo->prepare("INSERT INTO lecturers (name,email,password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hash]);
        $lecturerId = (int)$pdo->lastInsertId();

        // mark code used
        $pdo->prepare("UPDATE access_codes SET is_used = 1 WHERE id = ?")->execute([(int)$ac['id']]);

        $_SESSION['lecturer_id'] = $lecturerId;
        $_SESSION['lecturer_name'] = $name;
        flash_set('success', 'Signup successful. Welcome!');
        redirect('dashboard.php');
    } catch (PDOException $e) {
        flash_set('error', 'Email already registered or DB error.');
         redirect('signup.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SEA-AI Sign Up</title>
  <link rel="stylesheet" href="../signup.css">
</head>
<body>
  <div class="container">
    <div class="left-panel">
      <img src="../Sign up-amico.png" alt="Sign up illustration" class="illustration">
      <h2>SEA – AI</h2>
      <h4>(SMART EXAM ASSISTANT)</h4>
      <p>
        Welcome to your smart and efficient <br>
        exam assistant. This platform empowers <br>
        academic staff to quickly and accurately <br>
        generate examination questions using AI.
      </p>
    </div>

    <div class="right-panel">
      <div class="signup-box">
        <h2>SIGN UP</h2>

        <!-- Flash messages -->
        <?php if ($msg = flash_get('error')): ?>
          <div class="alert error"><?=e($msg)?></div>
        <?php elseif ($msg = flash_get('success')): ?>
          <div class="alert success"><?=e($msg)?></div>
        <?php endif; ?>

        <form method="post" clasas="signup-form">
          <input type="hidden" name="csrf" value="<?=e(csrf_token())?>">

          <input type="text" name="name" placeholder="Full Name" required>

          <input type="email" name="email" placeholder="Email" required>

          <div class="input-group">
            <input type="password" name="password" placeholder="Password" required id="password">
            <span class="eye-icon" onclick="togglePassword('password')">👁️</span>
          </div>

          <div class="input-group">
            <input type="password" name="confirm" placeholder="Confirm Password" required id="confirm">
            <span class="eye-icon" onclick="togglePassword('confirm')">👁️</span>
          </div>

          <div class="code-boxes">
            <?php for ($i = 0; $i < 4; $i++): ?>
              <input type="text" class="code-box" maxlength="1" required
                     oninput="moveNext(this, <?=$i?>)" name="access_code_digits[]">
            <?php endfor; ?>
          </div>
          <input type="hidden" name="access_code" id="access_code">

          <button type="submit">SIGN UP</button>
        </form>

        <p class="login-link">
          Already have an account? <a href="../index.php">Login</a>
        </p>
      </div>
    </div>
  </div>

  <script>
    // combine 4 code boxes into hidden field
    const codeBoxes = document.querySelectorAll('.code-box');
    function updateAccessCode() {
      document.getElementById('access_code').value =
        Array.from(codeBoxes).map(b => b.value).join('');
    }
    codeBoxes.forEach(b => b.addEventListener('input', updateAccessCode));

    // move focus automatically
    function moveNext(el, index) {
      if (el.value && index < codeBoxes.length - 1)
        codeBoxes[index + 1].focus();
      updateAccessCode();
    }

    // show/hide password
    function togglePassword(id) {
      const field = document.getElementById(id);
      field.type = field.type === 'password' ? 'text' : 'password';
    }
  </script>
</body>
</html>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
