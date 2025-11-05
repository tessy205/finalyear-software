<?php
// admin/dashboard.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();
require_once __DIR__ . '/../includes/header.php';

// totals
$totLecturers = (int)$pdo->query("SELECT COUNT(*) FROM lecturers")->fetchColumn();
$totCourses = (int)$pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$totQuestions = (int)$pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn();

// recent lecturers (last 10)
$stmt = $pdo->query("SELECT id,name,email,is_blocked,created_at FROM lecturers ORDER BY created_at DESC LIMIT 10");
$recentLecturers = $stmt->fetchAll();

?>

<h2>Admin Dashboard</h2>

<ul>
  <li>Total Lecturers: <?=e($totLecturers)?></li>
  <li>Total Courses: <?=e($totCourses)?></li>
  <li>Total Questions: <?=e($totQuestions)?></li>
</ul>

<h3>Recent Lecturers</h3>
<table>
  <thead><tr><th>Name</th><th>Email</th><th>Blocked</th><th>Created</th></tr></thead>
  <tbody>
    <?php foreach($recentLecturers as $l): ?>
      <tr>
        <td><?=e($l['name'])?></td>
        <td><?=e($l['email'])?></td>
        <td><?= $l['is_blocked'] ? 'Yes' : 'No' ?></td>
        <td><?=e($l['created_at'])?></td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
