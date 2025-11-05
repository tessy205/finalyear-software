<?php
// lecturer/edit_question.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_lecturer();

$id = (int)($_GET['id'] ?? 0);
$course_id = (int)($_GET['course_id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$q = $stmt->fetch();

if (!$q) {
    flash_set('error', 'Question not found.');
    redirect("view_questions.php?course_id={$course_id}");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $question_text = trim($_POST['question_text']);
    $answer = trim($_POST['answer']);

    if ($type === 'mcq') {
        $options = [
            'a' => $_POST['option_a'],
            'b' => $_POST['option_b'],
            'c' => $_POST['option_c'],
            'd' => $_POST['option_d']
        ];
        $options_json = json_encode($options);
    } else {
        $options_json = null;
    }

    $update = $pdo->prepare("UPDATE questions SET type=?, question_text=?, options=?, answer=? WHERE id=?");
    $update->execute([$type, $question_text, $options_json, $answer, $id]);

    flash_set('success', 'Question updated successfully.');
    redirect("view_questions.php?course_id={$course_id}");
}
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>
<div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-6 mt-6">
  <h2 class="text-xl font-semibold mb-4 text-gray-700">Edit Question</h2>

  <form method="post" class="space-y-4">
    <div>
      <label class="block font-medium mb-1">Question Type</label>
      <select name="type" class="border w-full px-3 py-2 rounded" id="typeSelect">
        <option value="mcq" <?= $q['type'] === 'mcq' ? 'selected' : '' ?>>Multiple Choice</option>
        <option value="theory" <?= $q['type'] === 'theory' ? 'selected' : '' ?>>Theory</option>
      </select>
    </div>

    <div>
      <label class="block font-medium mb-1">Question Text</label>
      <textarea name="question_text" class="border w-full px-3 py-2 rounded" rows="5"><?= e($q['question_text']) ?></textarea>
    </div>

    <div id="mcqFields" style="display: <?= $q['type'] === 'mcq' ? 'block' : 'none' ?>;">
      <?php $opts = json_decode($q['options'], true); ?>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label>Option A</label>
          <input type="text" name="option_a" value="<?= e($opts['a'] ?? '') ?>" class="border w-full px-3 py-2 rounded">
        </div>
        <div>
          <label>Option B</label>
          <input type="text" name="option_b" value="<?= e($opts['b'] ?? '') ?>" class="border w-full px-3 py-2 rounded">
        </div>
        <div>
          <label>Option C</label>
          <input type="text" name="option_c" value="<?= e($opts['c'] ?? '') ?>" class="border w-full px-3 py-2 rounded">
        </div>
        <div>
          <label>Option D</label>
          <input type="text" name="option_d" value="<?= e($opts['d'] ?? '') ?>" class="border w-full px-3 py-2 rounded">
        </div>
      </div>
    </div>

    <div>
      <label class="block font-medium mb-1">Correct Answer</label>
      <input type="text" name="answer" value="<?= e($q['answer']) ?>" class="border w-full px-3 py-2 rounded">
    </div>

    <button class="bg-[var(--primary)] hover:bg-[#150133] text-white px-4 py-2 rounded-lg font-medium">
      Update Question
    </button>
    <a href="view_questions.php?course_id=<?= e($course_id) ?>" class="ml-3 text-gray-600 hover:underline">Cancel</a>
  </form>
</div>

<script>
document.getElementById('typeSelect').addEventListener('change', function() {
  document.getElementById('mcqFields').style.display = this.value === 'mcq' ? 'block' : 'none';
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
