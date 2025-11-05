<?php
$admin = !empty($_SESSION['admin_id']);
$lecturer = !empty($_SESSION['lecturer_id']);
?>
<!-- Sidebar -->
<aside class="w-64 bg-[var(--primary)] text-white flex flex-col">
  <div class="p-4 text-xl font-bold border-b border-gray-700">SEA-AI</div>
  <nav class="flex-1 p-4 space-y-2">
    <?php if ($admin): ?>
      <a href="dashboard.php" class="block px-3 py-2 rounded hover:bg-gray-700">Dashboard</a>
      <a href="lecturers.php" class="block px-3 py-2 rounded hover:bg-gray-700">Lecturers</a>
      <a href="create_access_code.php" class="block px-3 py-2 rounded hover:bg-gray-700">Create Access Code</a>
      <a href="access_codes.php" class="block px-3 py-2 rounded hover:bg-gray-700">All Access Codes</a>
    <?php elseif ($lecturer): ?>
      <a href="dashboard.php" class="block px-3 py-2 rounded hover:bg-gray-700">Dashboard</a>
      <a href="add_course.php" class="block px-3 py-2 rounded hover:bg-gray-700">Add Course</a>
      <a href="view_courses.php" class="block px-3 py-2 rounded hover:bg-gray-700">My Courses</a>
      <a href="generate_questions.php" class="block px-3 py-2 rounded hover:bg-gray-700">Generate Questions</a>
    <?php else: ?>
      <!-- <a href="index.php" class="block px-3 py-2 rounded hover:bg-gray-700">Admin Login</a>
      <a href="/lecturer/login.php" class="block px-3 py-2 rounded hover:bg-gray-700">Lecturer Login</a>
      <a href="/lecturer/signup.php" class="block px-3 py-2 rounded hover:bg-gray-700">Lecturer Signup</a> -->
    <?php endif; ?>
  </nav>
  <div class="p-4 border-t border-gray-700">
    <?php if ($admin || $lecturer): ?>
      <a href="logout.php" class="block px-3 py-2 text-center rounded bg-red-600 hover:bg-red-700">Logout</a>
    <?php endif; ?>
  </div>
</aside>
