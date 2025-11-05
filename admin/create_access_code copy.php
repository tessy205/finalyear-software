<?php
// admin/create_access_code.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();
require_once __DIR__ . '/../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash_set('error', 'Invalid email address.');
        redirect();
    }
     $stmt = $pdo->prepare("SELECT COUNT(*) FROM access_codes WHERE email = ?");
    $stmt->execute([$email]);
    $exists = $stmt->fetchColumn() > 0;

    if ($exists) {
        flash_set('error', "{$email} already exists.");
        redirect();
        return;
    }
    // generate 4-digit code, ensure not immediate duplicate for same email
    $code = str_pad((string)random_int(0,9999), 4, '0', STR_PAD_LEFT);
    $stmt = $pdo->prepare("INSERT INTO access_codes (email, code) VALUES (?, ?)");
    $stmt->execute([$email, $code]);

    // send email (simple)
    $subject = "Your Lecturer Signup Access Code";
    $body = "<p>Hello,</p><p>Your signup access code is <strong>{$code}</strong>. Use it to register as a lecturer.</p>";
    $sent = send_mail_simple($email, $subject, $body);

    flash_set('success', $sent ? "Access code generated and email sent to {$email}." : "Access code generated; but mail() failed. Code: {$code}");
    // redirect('/admin/create_access_code.php');
    redirect();
}
?>

<h2>Create Access Code</h2>

<form method="post" style="max-width:480px">
  <label>Lecturer Email<br><input type="email" name="email" required></label><br><br>
  <button>Create Code</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
