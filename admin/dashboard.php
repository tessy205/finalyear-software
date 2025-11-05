<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

// Fetch data
$totLecturers = (int)$pdo->query("SELECT COUNT(*) FROM lecturers")->fetchColumn();
$totCourses = (int)$pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$totQuestions = (int)$pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn();
$recentLecturers = $pdo->query("SELECT name,email,is_blocked,created_at FROM lecturers ORDER BY created_at DESC LIMIT 10")->fetchAll();

// Layout includes
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col">
  <?php require_once __DIR__ . '/../includes/topnav.php'; ?>
  
  <main class="p-6 space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white shadow rounded p-4">
        <h2 class="text-gray-500 text-sm">Total Lecturers</h2>
        <p class="text-2xl font-bold text-[var(--primary)]"><?= $totLecturers ?></p>
      </div>
      <div class="bg-white shadow rounded p-4">
        <h2 class="text-gray-500 text-sm">Total Courses</h2>
        <p class="text-2xl font-bold text-[var(--primary)]"><?= $totCourses ?></p>
      </div>
      <div class="bg-white shadow rounded p-4">
        <h2 class="text-gray-500 text-sm">Total Questions</h2>
        <p class="text-2xl font-bold text-[var(--primary)]"><?= $totQuestions ?></p>
      </div>
    </div>

    <section class="bg-white shadow rounded p-4">
      <h3 class="text-lg font-semibold mb-4">Recent Lecturers</h3>
      <table class="w-full border-collapse">
        <thead class="bg-gray-100">
          <tr>
            <th class="border p-2 text-left">Name</th>
            <th class="border p-2 text-left">Email</th>
            <th class="border p-2 text-left">Blocked</th>
            <th class="border p-2 text-left">Created</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($recentLecturers as $l): ?>
            <tr class="hover:bg-gray-50">
              <td class="border p-2"><?=e($l['name'])?></td>
              <td class="border p-2"><?=e($l['email'])?></td>
              <td class="border p-2"><?= $l['is_blocked'] ? '<span class="text-red-600 font-medium">Yes</span>' : '<span class="text-green-600 font-medium">No</span>' ?></td>
              <td class="border p-2"><?=e($l['created_at'])?></td>
            </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </section>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
