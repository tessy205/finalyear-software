<?php
// admin/access_codes.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();
// require_once __DIR__ . '/../includes/header.php';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    // verify_csrf();
    $id = (int)$_POST['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM access_codes WHERE id = ?");
    $stmt->execute([$id]);
    flash_set('success', 'Access code deleted.');
    redirect();
}

// Fetch all access codes
$stmt = $pdo->query("SELECT id, email, code, created_at FROM access_codes ORDER BY created_at DESC");
$codes = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>


<div class="flex-1 flex flex-col">
  <?php require_once __DIR__ . '/../includes/topnav.php'; ?>

  <main class="p-6 space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-semibold text-[var(--primary)]">Access Codes</h2>
      <span class="text-sm text-gray-500"><?= count($codes) ?> total</span>
    </div>

    <?php if ($msg = flash_get('success')): ?>
      <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded"><?= e($msg) ?></div>
    <?php elseif ($msg = flash_get('error')): ?>
      <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded"><?= e($msg) ?></div>
    <?php endif; ?>

    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="w-full border-collapse">
        <thead class="bg-gray-100 text-gray-700 text-sm uppercase">
          <tr>
            <th class="border-b px-4 py-3 text-left">#</th>
            <th class="border-b px-4 py-3 text-left">Email</th>
            <th class="border-b px-4 py-3 text-center">Access Code</th>
            <th class="border-b px-4 py-3 text-center">Created At</th>
            <th class="border-b px-4 py-3 text-center">Action</th>
          </tr>
        </thead>
        <tbody class="text-sm">
          <?php if (empty($codes)): ?>
            <tr>
              <td colspan="5" class="text-center py-6 text-gray-500">No access codes generated yet.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($codes as $i => $c): ?>
              <tr class="hover:bg-gray-50">
                <td class="border-b px-4 py-2"><?= $i + 1 ?></td>
                <td class="border-b px-4 py-2"><?= e($c['email']) ?></td>
                <td class="border-b px-4 py-2 text-center font-semibold text-[var(--primary)]"><?= e($c['code']) ?></td>
                <td class="border-b px-4 py-2 text-center"><?= e(date('Y-m-d H:i', strtotime($c['created_at']))) ?></td>
                <td class="border-b px-4 py-2 text-center">
                  <form method="post" class="inline" onsubmit="return confirm('Delete this code?');">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="delete_id" value="<?= e($c['id']) ?>">
                    <button type="submit"
                      class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">
                      Delete
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
