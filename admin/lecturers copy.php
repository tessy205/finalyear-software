<?php
// admin/lecturers.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();
require_once __DIR__ . '/../includes/header.php';

// query lecturers with course and question counts (single query using subselects)
$sql = "
SELECT l.id, l.name, l.email, l.is_blocked,
  (SELECT COUNT(*) FROM courses c WHERE c.lecturer_id = l.id) AS courses_count,
  (SELECT COUNT(*) FROM questions q WHERE q.lecturer_id = l.id) AS questions_count,
  l.created_at
FROM lecturers l
ORDER BY l.created_at DESC
";
$stmt = $pdo->query($sql);
$lecturers = $stmt->fetchAll();
?>

<h2>Lecturers</h2>

<table>
  <thead><tr><th>Name</th><th>Email</th><th>Courses</th><th>Questions</th><th>Blocked</th><th>Action</th></tr></thead>
  <tbody>
    <?php foreach($lecturers as $l): ?>
      <tr>
        <td><?=e($l['name'])?></td>
        <td><?=e($l['email'])?></td>
        <td><?= (int)$l['courses_count'] ?></td>
        <td><?= (int)$l['questions_count'] ?></td>
        <td><?= $l['is_blocked'] ? 'Yes' : 'No' ?></td>
        <td>
          <form method="post" action="/admin/block_lecturer.php" style="display:inline">
            <input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
            <input type="hidden" name="id" value="<?=e($l['id'])?>">
            <?php if($l['is_blocked']): ?>
              <input type="hidden" name="action" value="unblock">
              <button type="submit">Unblock</button>
            <?php else: ?>
              <input type="hidden" name="action" value="block">
              <button type="submit">Block</button>
            <?php endif;?>
          </form>
        </td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
