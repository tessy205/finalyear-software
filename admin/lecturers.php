<?php
// admin/lecturers.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$sql = "
SELECT l.id, l.name, l.email, l.is_blocked,
  (SELECT COUNT(*) FROM courses c WHERE c.lecturer_id = l.id) AS courses_count,
  (SELECT COUNT(*) FROM questions q WHERE q.lecturer_id = l.id) AS questions_count,
  l.created_at
FROM lecturers l
ORDER BY l.created_at DESC
";
$stmt = $pdo->query($sql);
$lecturers = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col">
  <?php require_once __DIR__ . '/../includes/topnav.php'; ?>

  <main class="p-6 space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-semibold text-[var(--primary)]">All Lecturers</h2>
      <span class="text-sm text-gray-500"><?= count($lecturers) ?> total</span>
    </div>

    <?php if($msg = flash_get('success')): ?>
      <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded"><?= e($msg) ?></div>
    <?php elseif($msg = flash_get('error')): ?>
      <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded"><?= e($msg) ?></div>
    <?php endif; ?>

    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="w-full border-collapse">
        <thead class="bg-gray-100 text-gray-700 text-sm uppercase">
          <tr>
            <th class="border-b px-4 py-3 text-left">Name</th>
            <th class="border-b px-4 py-3 text-left">Email</th>
            <th class="border-b px-4 py-3 text-center">Courses</th>
            <th class="border-b px-4 py-3 text-center">Questions</th>
            <th class="border-b px-4 py-3 text-center">Status</th>
            <th class="border-b px-4 py-3 text-center">Action</th>
          </tr>
        </thead>
        <tbody class="text-sm">
          <?php foreach($lecturers as $l): ?>
            <tr class="hover:bg-gray-50">
              <td class="border-b px-4 py-2"><?= e($l['name']) ?></td>
              <td class="border-b px-4 py-2"><?= e($l['email']) ?></td>
              <td class="border-b px-4 py-2 text-center"><?= (int)$l['courses_count'] ?></td>
              <td class="border-b px-4 py-2 text-center"><?= (int)$l['questions_count'] ?></td>
              <td class="border-b px-4 py-2 text-center">
                <?php if ($l['is_blocked']): ?>
                  <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-medium">Blocked</span>
                <?php else: ?>
                  <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-medium">Active</span>
                <?php endif; ?>
              </td>
              <td class="border-b px-4 py-2 text-center">
                <form method="post" action="block_lecturer.php" class="inline">
                  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="id" value="<?= e($l['id']) ?>">
                  <?php if ($l['is_blocked']): ?>
                    <input type="hidden" name="action" value="unblock">
                    <button type="submit"
                      class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs">
                      Unblock
                    </button>
                  <?php else: ?>
                    <input type="hidden" name="action" value="block">
                    <button type="submit"
                      class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">
                      Block
                    </button>
                  <?php endif; ?>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
