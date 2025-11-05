<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Exam App</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/css/site.css">
  <style>
    body{font-family: Arial, sans-serif; padding:20px; max-width:1100px;margin:auto;}
    .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
    nav a{margin-right:10px}
    table{width:100%;border-collapse:collapse}
    table th, table td{border:1px solid #ddd;padding:8px}
    .flash{padding:10px;background:#efe;color:#005; margin-bottom:12px}
    .err{padding:10px;background:#fee;color:#900;margin-bottom:12px}
  </style>
</head>
<body>
<div class="topbar">
  <div><a href="/index.php"><strong>ExamApp</strong></a></div>
  <nav>
    <?php if(!empty($_SESSION['admin_id'])): ?>
      <a href="dashboard.php">Admin Dashboard</a>
      <a href="lecturers.php">Lecturers</a>
      <a href="create_access_code.php">Create Access Code</a>
      <a href="logout.php">Logout</a>
    <?php elseif(!empty($_SESSION['lecturer_id'])): ?>
      <a href="dashboard.php">Lecturer Dashboard</a>
      <a href="add_course.php">Add Course</a>
      <a href="view_courses.php">My Courses</a>
      <a href="/lecturer/generate_questions.php">Generate Questions</a>
      <a href="/lecturer/logout.php">Logout</a>
    <?php else: ?>
      <a href="/admin/index.php">Admin</a>
      <a href="/lecturer/login.php">Lecturer Login</a>
      <a href="../lecturer/signup.php">Lecturer Signup</a>
    <?php endif; ?>
  </nav>
</div>

<?php if($m = flash_get('success')): ?><div class="flash"><?=e($m)?></div><?php endif; ?>
<?php if($m = flash_get('error')): ?><div class="err"><?=e($m)?></div><?php endif; ?>
