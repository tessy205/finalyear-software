<?php
// lecturer/view_courses.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_lecturer();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$lecturer_id = (int)$_SESSION['lecturer_id'];

// Fetch all courses for this lecturer with question counts
$stmt = $pdo->prepare("
    SELECT c.id, c.title, c.course_code, c.level, c.created_at,
           COUNT(q.id) AS total_questions
    FROM courses c
    LEFT JOIN questions q ON q.course_id = c.id
    WHERE c.lecturer_id = ?
    GROUP BY c.id
    ORDER BY c.created_at DESC
");
$stmt->execute([$lecturer_id]);
$courses = $stmt->fetchAll();
?>

<div class="flex-1 flex flex-col">
  <?php require_once __DIR__ . '/../includes/topnav.php'; ?>

  <main class="p-6 space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-semibold text-gray-800">My Courses</h2>
      <a href="add_course.php"
         class="bg-[var(--primary)] hover:bg-[#150133] text-white px-5 py-2 rounded-lg font-medium transition">
        + Add Course
      </a>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full border-collapse">
        <thead class="bg-gray-100 text-gray-600 uppercase text-sm">
          <tr>
            <th class="text-left px-4 py-3 border-b">Title</th>
            <th class="text-left px-4 py-3 border-b">Code</th>
            <th class="text-left px-4 py-3 border-b">Level</th>
            <th class="text-left px-4 py-3 border-b">Questions</th>
            <th class="text-left px-4 py-3 border-b">Created</th>
            <th class="text-left px-4 py-3 border-b">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <?php if ($courses): ?>
            <?php foreach ($courses as $c): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-800 font-medium"><?= e($c['title']) ?></td>
                <td class="px-4 py-3"><?= e($c['course_code']) ?></td>
                <td class="px-4 py-3"><?= e($c['level']) ?></td>
                <td class="px-4 py-3">
                  <span class="bg-[var(--primary)]/10 text-[var(--primary)] px-3 py-1 rounded-full text-sm font-semibold">
                    <?= e($c['total_questions']) ?>
                  </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-sm"><?= e(date('M d, Y', strtotime($c['created_at']))) ?></td>
                <td class="px-4 py-3 space-x-3">
                  <a href="edit_course.php?id=<?= e($c['id']) ?>"
                     class="text-[var(--primary)] hover:underline font-medium">Edit</a>
                  <a href="generate_questions.php?course_id=<?= e($c['id']) ?>"
                     class="text-blue-600 hover:underline font-medium">Generate</a>
                     <?php if ($c['total_questions'] > 0): ?>
    <a href="view_questions.php?course_id=<?= e($c['id']) ?>"
       class="text-green-600 hover:underline font-medium">View Questions</a>
  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center py-6 text-gray-500">No courses found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
