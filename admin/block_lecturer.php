<?php
// admin/block_lecturer.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect();

$csrf = $_POST['csrf'] ?? '';
if (!csrf_check($csrf)) {
    flash_set('error', 'Invalid CSRF token.');
    header("Location: lecturers.php");
}

$id = (int)($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($id <= 0 || !in_array($action, ['block','unblock'])) {
    flash_set('error', 'Invalid request.');
  header("Location: lecturers.php");
}

$is_blocked = $action === 'block' ? 1 : 0;
$stmt = $pdo->prepare("UPDATE lecturers SET is_blocked = ? WHERE id = ?");
$stmt->execute([$is_blocked, $id]);

flash_set('success', $action === 'block' ? 'Lecturer blocked.' : 'Lecturer unblocked.');
header("Location: lecturers.php");
