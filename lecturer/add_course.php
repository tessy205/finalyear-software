<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_lecturer();

$lecturer_id = (int)$_SESSION['lecturer_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $code = trim($_POST['course_code'] ?? '');
    $level = trim($_POST['level'] ?? '');
    $info = trim($_POST['info'] ?? '');
    $csrf = $_POST['csrf'] ?? '';

    if (!csrf_check($csrf)) {
        flash_set('error','Invalid CSRF token.');
        redirect('add_course.php');
    }

    if (!$title || !$code || !$level) {
        flash_set('error','Title, Course Code and Level are required.');
        redirect('add_course.php');
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO courses (lecturer_id, title, course_code, level, info) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$lecturer_id, $title, $code, $level, $info]);
        flash_set('success','Course added successfully.');
        redirect('view_courses.php');
    } catch (PDOException $e) {
        flash_set('error','Database error: possibly duplicate course code.');
        redirect('add_course.php');
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col">
  <?php require_once __DIR__ . '/../includes/topnav.php'; ?>

  <main class="p-6 space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-semibold text-[var(--primary)]">Add New Course</h2>
      <a href="view_courses.php" class="text-sm text-[var(--primary)] hover:underline">View All Courses</a>
    </div>

    <!-- Flash messages -->
    <?php if($msg = flash_get('success')): ?>
      <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded"><?= e($msg) ?></div>
    <?php elseif($msg = flash_get('error')): ?>
      <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded"><?= e($msg) ?></div>
    <?php endif; ?>

    <!-- Form -->
   <div class="bg-white shadow rounded-lg p-8 max-w-4xl mx-auto">
  <!-- <h2 class="text-2xl font-semibold text-gray-800 mb-6">Add New Course</h2> -->

  <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <!-- Course Title -->
    <div>
      <label class="block text-gray-700 font-medium mb-2">Course Title</label>
      <input type="text" name="title" required
             class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-[var(--primary)] focus:border-[var(--primary)] focus:outline-none">
    </div>

    <!-- Course Code -->
    <div>
      <label class="block text-gray-700 font-medium mb-2">Course Code</label>
      <input type="text" name="course_code" required
             class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-[var(--primary)] focus:border-[var(--primary)] focus:outline-none">
    </div>

    <!-- Level -->
    <div>
      <label class="block text-gray-700 font-medium mb-2">Level</label>
      <select name="level" required
              class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white focus:ring-[var(--primary)] focus:border-[var(--primary)] focus:outline-none">
        <option value="">-- Select Level --</option>
        <option value="100">100 Level</option>
        <option value="200">200 Level</option>
        <option value="300">300 Level</option>
        <option value="400">400 Level</option>
        <option value="500">500 Level</option>
      </select>
    </div>

    <!-- Info -->
    <div class="md:col-span-2">
      <label class="block text-gray-700 font-medium mb-2">Course Info</label>
      <textarea name="info" rows="4"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-[var(--primary)] focus:border-[var(--primary)] focus:outline-none"></textarea>
    </div>

    <!-- Submit -->
    <div class="md:col-span-2 flex justify-end pt-4">
      <button type="submit"
              class="bg-[var(--primary)] hover:bg-[#150133] text-white px-8 py-2.5 rounded-lg font-medium transition">
        Add Course
      </button>
    </div>
  </form>
</div>

  </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
