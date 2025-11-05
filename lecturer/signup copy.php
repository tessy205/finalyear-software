<?php
// lecturer/signup.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
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
        flash_set('success', 'Signup successful. Welcome!');
        redirect('dashboard.php');
    } catch (PDOException $e) {
        flash_set('error', 'Email already registered or DB error.');
        redirect('signup.php');
    }
}
?>

<h2>Lecturer Signup</h2>
<form method="post" style="max-width:560px">
  <input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
  <label>Name<br><input name="name" required></label><br><br>
  <label>Email<br><input name="email" type="email" required></label><br><br>
  <label>Password<br><input name="password" type="password" required></label><br><br>
  <label>Access Code (4 digits)<br><input name="access_code" maxlength="4" required></label><br><br>
  <button>Sign Up</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
