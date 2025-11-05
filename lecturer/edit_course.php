<?php
// lecturer/edit_course.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_lecturer();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$lecturer_id = (int)$_SESSION['lecturer_id'];
$id = (int)($_GET['id'] ?? 0);
if (!$id) { flash_set('error','Invalid course.'); redirect('/lecturer/view_courses.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $code = trim($_POST['course_code'] ?? '');
    $level = trim($_POST['level'] ?? '');
    $info = trim($_POST['info'] ?? '');
    $csrf = $_POST['csrf'] ?? '';
    if (!csrf_check($csrf)) { flash_set('error','Invalid CSRF'); redirect("/lecturer/edit_course.php?id={$id}"); }
    $stmt = $pdo->prepare("UPDATE courses SET title=?, course_code=?, level=?, info=?, updated_at=NOW() WHERE id=? AND lecturer_id=?");
    $stmt->execute([$title, $code, $level, $info, $id, $lecturer_id]);
    flash_set('success','Course updated successfully.');
    redirect('view_courses.php');
}

// fetch course
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id=? AND lecturer_id=?");
$stmt->execute([$id, $lecturer_id]);
$course = $stmt->fetch();
if (!$course) { flash_set('error','Course not found.'); redirect('/lecturer/view_courses.php'); }
?>

<div class="flex-1 flex flex-col">
  <?php require_once __DIR__ . '/../includes/topnav.php'; ?>

  <main class="p-6 space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-semibold text-gray-800">Edit Course</h2>
      <a href="view_courses.php" class="text-[var(--primary)] hover:underline">← Back to Courses</a>
    </div>

    <?php if($msg = flash_get('success')): ?>
      <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded"><?= e($msg) ?></div>
    <?php elseif($msg = flash_get('error')): ?>
      <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded"><?= e($msg) ?></div>
    <?php endif; ?>

    <div class="bg-whaite shadow-md rounded-lg p-6 max-w-2xl">
      <form method="post" class="space-y-5">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

        <div>
          <label class="block text-gray-700 font-medium mb-1">Course Title</label>
          <input type="text" name="title" value="<?= e($course['title']) ?>" required
                 class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-[var(--primary)] focus:border-[var(--primary)]">
        </div>

        <div>
          <label class="block text-gray-700 font-medium mb-1">Course Code</label>
          <input type="text" name="course_code" value="<?= e($course['course_code']) ?>" required
                 class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-[var(--primary)] focus:border-[var(--primary)]">
        </div>

        <div>
          <label class="block text-gray-700 font-medium mb-1">Level</label>
          <input type="text" name="level" value="<?= e($course['level']) ?>" required
                 class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-[var(--primary)] focus:border-[var(--primary)]">
        </div>

        <div>
          <label class="block text-gray-700 font-medium mb-1">Course Info / Description</label>
          <textarea name="info" rows="4"
                    class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-[var(--primary)] focus:border-[var(--primary)]"><?= e($course['info']) ?></textarea>
        </div>

        <div class="pt-4 flex justify-end space-x-3">
          <a href="view_courses.php"
             class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 rounded-lg font-medium transition">
            Cancel
          </a>
          <button type="submit"
                  class="bg-[var(--primary)] hover:bg-[#150133] text-white px-6 py-2 rounded-lg font-medium transition">
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
