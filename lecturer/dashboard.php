<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_lecturer();

$lecturer_id = (int)$_SESSION['lecturer_id'];

// Fetch lecturer stats
$stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE lecturer_id = ?");
$stmt->execute([$lecturer_id]);
$myCourses = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE lecturer_id = ?");
$stmt->execute([$lecturer_id]);
$myQuestions = (int)$stmt->fetchColumn();

// Fetch recent courses for this lecturer
$stmt = $pdo->prepare("SELECT id, title, course_code, level, created_at FROM courses WHERE lecturer_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$lecturer_id]);
$recentCourses = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col">
  <?php require_once __DIR__ . '/../includes/topnav.php'; ?>

  <main class="p-6 space-y-6">
    <!-- Dashboard Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div class="bg-white shadow rounded p-4">
        <h2 class="text-gray-500 text-sm">My Courses</h2>
        <p class="text-2xl font-bold text-[var(--primary)]"><?= $myCourses ?></p>
      </div>
      <div class="bg-white shadow rounded p-4">
        <h2 class="text-gray-500 text-sm">My Questions</h2>
        <p class="text-2xl font-bold text-[var(--primary)]"><?= $myQuestions ?></p>
      </div>
      <div class="bg-white shadow rounded p-4">
        <h2 class="text-gray-500 text-sm">Last Login</h2>
        <p class="text-lg text-gray-700 font-medium">
          <?= e($_SESSION['lecturer_last_login'] ?? date('Y-m-d H:i')) ?>
        </p>
      </div>
    </div>

    <!-- Recent Courses -->
    <section class="bg-white shadow rounded p-4">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Recent Courses</h3>
        <a href="view_courses.php"
           class="text-sm text-[var(--primary)] hover:underline">View All</a>
      </div>

      <table class="w-full border-collapse">
        <thead class="bg-gray-100 text-gray-700 text-sm uppercase">
          <tr>
            <th class="border-b px-4 py-3 text-left">Title</th>
            <th class="border-b px-4 py-3 text-left">Course Code</th>
            <th class="border-b px-4 py-3 text-center">Level</th>
            <th class="border-b px-4 py-3 text-center">Created</th>
          </tr>
        </thead>
        <tbody class="text-sm">
          <?php if (empty($recentCourses)): ?>
            <tr>
              <td colspan="4" class="text-center py-6 text-gray-500">
                You haven’t added any courses yet.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($recentCourses as $c): ?>
              <tr class="hover:bg-gray-50">
                <td class="border-b px-4 py-2"><?= e($c['title']) ?></td>
                <td class="border-b px-4 py-2"><?= e($c['course_code']) ?></td>
                <td class="border-b px-4 py-2 text-center"><?= e($c['level']) ?></td>
                <td class="border-b px-4 py-2 text-center">
                  <?= e(date('Y-m-d', strtotime($c['created_at']))) ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
