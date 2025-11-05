<?php
// lecturer/delete_question.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_lecturer();

$lecturer_id = (int)$_SESSION['lecturer_id'];
$question_id = (int)($_GET['id'] ?? 0);

// Check if question exists and belongs to a course owned by this lecturer
$stmt = $pdo->prepare("
    SELECT q.id, q.course_id 
    FROM questions q
    INNER JOIN courses c ON q.course_id = c.id
    WHERE q.id = ? AND c.lecturer_id = ?
");
$stmt->execute([$question_id, $lecturer_id]);
$question = $stmt->fetch();

if (!$question) {
    flash_set('error', 'Invalid question or unauthorized access.');
    redirect('/lecturer/view_courses.php');
}

// Proceed to delete
$del = $pdo->prepare("DELETE FROM questions WHERE id = ?");
$del->execute([$question_id]);

flash_set('success', 'Question deleted successfully.');
redirect("view_questions.php?course_id={$question['course_id']}");
