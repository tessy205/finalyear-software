<?php
// lecturer/dashboard.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_lecturer();
// require_once __DIR__ . '/../includes/header.php';

$lecturer_id = (int)$_SESSION['lecturer_id'];

// stats for this lecturer
$stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE lecturer_id = ?");
$stmt->execute([$lecturer_id]);
$myCourses = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE lecturer_id = ?");
$stmt->execute([$lecturer_id]);
$myQuestions = (int)$stmt->fetchColumn();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<h2>Lecturer Dashboard</h2>
<ul>
  <li>Your courses: <?=e($myCourses)?></li>
  <li>Your questions: <?=e($myQuestions)?></li>
</ul>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
