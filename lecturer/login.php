<?php
// lecturer/login.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id,password,is_blocked FROM lecturers WHERE email = ?");
    $stmt->execute([$email]);
    $lec = $stmt->fetch();

    if (!$lec) {
        flash_set('error', 'Invalid credentials.');
        redirect('/lecturer/login.php');
    }

    if ((int)$lec['is_blocked'] === 1) {
        flash_set('error', 'Your account is blocked. Contact admin.');
        redirect('/lecturer/login.php');
    }

    if (verify_hash($password, $lec['password'])) {
        $_SESSION['lecturer_id'] = (int)$lec['id'];
        flash_set('success', 'Welcome back.');
        redirect('/lecturer/dashboard.php');
    } else {
        flash_set('error', 'Invalid credentials.');
        redirect('/lecturer/login.php');
    }
}
?>

<h2>Lecturer Login</h2>
<form method="post" style="max-width:420px">
  <label>Email<br><input type="email" name="email" required></label><br><br>
  <label>Password<br><input type="password" name="password" required></label><br><br>
  <button>Login</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
