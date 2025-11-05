<?php
// admin/create_access_code.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

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
    }

    // generate 4-digit code
    $code = str_pad((string)random_int(0,9999), 4, '0', STR_PAD_LEFT);
    $stmt = $pdo->prepare("INSERT INTO access_codes (email, code) VALUES (?, ?)");
    $stmt->execute([$email, $code]);

    // send email (simple)
    $subject = "Your Lecturer Signup Access Code";
    $body = "<p>Hello,</p><p>Your signup access code is <strong>{$code}</strong>. Use it to register as a lecturer.</p>";
    $sent = send_mail_simple($email, $subject, $body);

    flash_set('success', $sent ? "Access code generated and email sent to {$email}." : "Access code generated; but mail() failed. Code: {$code}");
    redirect();
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col">
  <?php require_once __DIR__ . '/../includes/topnav.php'; ?>

  <main class="p-6 flex justify-center items-start">
    <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-md">
      <h2 class="text-2xl font-semibold text-[var(--primary)] mb-6">Create Access Code</h2>

      <?php if($msg = flash_get('success')): ?>
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-2 rounded">
          <?= e($msg) ?>
        </div>
      <?php elseif($msg = flash_get('error')): ?>
        <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-2 rounded">
          <?= e($msg) ?>
        </div>
      <?php endif; ?>

      <form method="post" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Lecturer Email</label>
          <input type="email" name="email" required
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[var(--primary)] focus:outline-none">
        </div>
        <button type="submit"
          class="w-full bg-[var(--primary)] text-white py-2 rounded-lg hover:bg-purple-900 transition">
          Generate Access Code
        </button>
      </form>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
