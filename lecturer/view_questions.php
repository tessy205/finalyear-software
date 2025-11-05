<?php
// lecturer/view_questions.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_lecturer();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$lecturer_id = (int)$_SESSION['lecturer_id'];
$course_id = (int)($_GET['course_id'] ?? 0);

// Validate course ownership
$stmt = $pdo->prepare("SELECT title, course_code FROM courses WHERE id = ? AND lecturer_id = ?");
$stmt->execute([$course_id, $lecturer_id]);
$course = $stmt->fetch();

if (!$course) {
    flash_set('error', 'Invalid course or unauthorized access.');
    redirect('/lecturer/view_courses.php');
}

// Fetch all questions for this course
$qstmt = $pdo->prepare("
    SELECT id, type, question_text, options, answer, created_at
    FROM questions
    WHERE course_id = ?
    ORDER BY created_at DESC
");
$qstmt->execute([$course_id]);
$questions = $qstmt->fetchAll();
?>

<div class="flex-1 flex flex-col overflow-y-auto">
  <?php require_once __DIR__ . '/../includes/topnav.php'; ?>

  <main class="p-6 space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-semibold text-gray-800">
        Questions for <?= e($course['title']) ?> (<?= e($course['course_code']) ?>)
      </h2>
      <a href="generate_questions.php?course_id=<?= e($course_id) ?>"
         class="bg-[var(--primary)] hover:bg-[#150133] text-white px-4 py-2 rounded-lg font-medium shadow">
        + Add Question
      </a>
    </div>
    <?php if($msg = flash_get('success')): ?>
      <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded"><?= e($msg) ?></div>
    <?php elseif($msg = flash_get('error')): ?>
      <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded"><?= e($msg) ?></div>
    <?php endif; ?>

    <div class="bg-white shadow-md rounded-lg p-4 overflow-x-auto">
      <table id="questionsTable" class="min-w-full border-collapse">
        <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
          <tr>
            <th class="px-4 py-3 border-b text-left">#</th>
            <th class="px-4 py-3 border-b text-left">Action</th>
            <th class="px-4 py-3 border-b text-left">Question</th>
            <th class="px-4 py-3 border-b text-left">A</th>
            <th class="px-4 py-3 border-b text-left">B</th>
            <th class="px-4 py-3 border-b text-left">C</th>
            <th class="px-4 py-3 border-b text-left">D</th>
            <th class="px-4 py-3 border-b text-left">Answer</th>
            <th class="px-4 py-3 border-b text-left">Type</th>
            <th class="px-4 py-3 border-b text-left">Created</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($questions): ?>
            <?php foreach ($questions as $i => $q): 
              $opts = $q['type'] === 'mcq' ? json_decode($q['options'], true) : [];
            ?>
              <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3"><?= $i + 1 ?></td>
                <td class="px-4 py-3 flex items-center gap-2">
                  <a href="edit_question.php?id=<?= e($q['id']) ?>&course_id=<?= e($course_id) ?>"
                     class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-sm">Edit</a>
                  <button onclick="confirmDelete(<?= e($q['id']) ?>)"
                     class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-sm">Delete</button>
                </td>
                <td class="px-4 py-3"><?= nl2br(e($q['question_text'])) ?></td>
                <td class="px-4 py-3"><?= e($opts['a'] ?? '-') ?></td>
                <td class="px-4 py-3"><?= e($opts['b'] ?? '-') ?></td>
                <td class="px-4 py-3"><?= e($opts['c'] ?? '-') ?></td>
                <td class="px-4 py-3"><?= e($opts['d'] ?? '-') ?></td>
                <td class="px-4 py-3 font-semibold text-[var(--primary)]"><?= e($q['answer'] ?? '') ?></td>
                <td class="px-4 py-3"><?= ucfirst(e($q['type'])) ?></td>
                <td class="px-4 py-3 text-sm text-gray-500"><?= e(date('M d, Y', strtotime($q['created_at']))) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="10" class="text-center py-6 text-gray-500">No questions found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<!-- ✅ DataTables and Export -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.tailwindcss.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
  $('#questionsTable').DataTable({
    dom: 'Bfrtip',
    buttons: [
      { extend: 'copy', className: 'bg-gray-600 text-white rounded px-3 py-1 mx-1' },
      { extend: 'csv', className: 'bg-green-600 text-white rounded px-3 py-1 mx-1' },
      { extend: 'excel', className: 'bg-blue-600 text-white rounded px-3 py-1 mx-1' },
      { extend: 'pdf', className: 'bg-red-600 text-white rounded px-3 py-1 mx-1' },
      { extend: 'print', className: 'bg-yellow-500 text-white rounded px-3 py-1 mx-1' }
    ],
    pageLength: 10,
    responsive: true
  });
});

function confirmDelete(id) {
  if (confirm('Are you sure you want to delete this question? This action cannot be undone.')) {
    window.location.href = 'delete_question.php?id=' + id;
  }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
