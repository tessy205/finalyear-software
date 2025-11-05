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
        redirect('/dashboard.php');
    } else {
        flash_set('error', 'Invalid admin credentials.');
        redirect('/admin/index.php');
    }
}

// Helper: if there is no admin in DB, show instructions to create one via create_admin.php (below)
$stmt = $pdo->query("SELECT COUNT(*) as c FROM admins");
$count = (int)$stmt->fetchColumn();

?>

<h2>Admin Login</h2>

<?php if ($count === 0): ?>
  <div class="err">No admin user exists. Run <code>/admin/create_admin.php</code> once to create default admin.</div>
<?php endif; ?>

<form method="post" style="max-width:420px">
  <label>Email<br><input type="email" name="email" required></label><br><br>
  <label>Password<br><input type="password" name="password" required></label><br><br>
  <button>Login</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
