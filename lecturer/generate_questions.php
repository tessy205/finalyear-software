<?php
// lecturer/generate_questions.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_lecturer();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$lecturer_id = (int)$_SESSION['lecturer_id'];

// Fetch lecturer courses
$stmt = $pdo->prepare("SELECT id, title, course_code FROM courses WHERE lecturer_id = ? ORDER BY title");
$stmt->execute([$lecturer_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle GET param
$selected_course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf'] ?? '';
    if (!csrf_check($csrf)) {
        flash_set('error', 'Invalid CSRF token.');
        redirect('/lecturer/generate_questions.php');
    }

    $course_id = (int)($_POST['course_id'] ?? 0);
    $type = $_POST['type'] ?? 'mcq';
    $questions = $_POST['questions'] ?? [];

    if (!$course_id || !$type || empty($questions)) {
        flash_set('error', 'Select course, type, and add at least one question.');
        redirect('/lecturer/generate_questions.php');
    }

    $pdo->beginTransaction();
    try {
        $qInsert = $pdo->prepare("INSERT INTO questions (course_id, lecturer_id, type, question_text, options, answer)
                                  VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($questions as $q) {
            $text = trim($q['text'] ?? '');
            if ($text === '') continue;
            if ($type === 'mcq') {
                $opts = array_map('trim', $q['options'] ?? []);
                $options_json = json_encode($opts, JSON_UNESCAPED_UNICODE);
                $correct = $q['correct'] ?? null;
                $qInsert->execute([$course_id, $lecturer_id, 'mcq', $text, $options_json, $correct]);
            } else {
                $answer = trim($q['answer'] ?? '');
                $qInsert->execute([$course_id, $lecturer_id, 'theory', $text, null, $answer]);
            }
        }
        $pdo->commit();
        flash_set('success', 'Questions saved successfully.');
        redirect('/lecturer/view_courses.php');
    } catch (Exception $e) {
        $pdo->rollBack();
        flash_set('error', 'Error saving questions: ' . $e->getMessage());
        redirect('/lecturer/generate_questions.php');
    }
}
?>

<div class="flex-1 flex flex-col">
  <?php require_once __DIR__ . '/../includes/topnav.php'; ?>

  <main class="p-6 space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-semibold text-gray-800">Generate Questions</h2>
    </div>

    <?php if($msg = flash_get('success')): ?>
      <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded"><?= e($msg) ?></div>
    <?php elseif($msg = flash_get('error')): ?>
      <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded"><?= e($msg) ?></div>
    <?php endif; ?>

    <div class="bg-white shadow rounded-lg p-6">
      <form method="post" id="mainForm" class="space-y-4">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

        <!-- Course Selector -->
        <div>
          <label class="block text-gray-700 font-medium mb-1">Select Course</label>
          <select name="course_id" required
                  class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-[var(--primary)] focus:border-[var(--primary)]">
            <option value="">-- Select Course --</option>
            <?php foreach($courses as $c): ?>
              <option value="<?= e($c['id']) ?>"
                <?= $selected_course_id == $c['id'] ? 'selected' : '' ?>>
                <?= e($c['title']) ?> [<?= e($c['course_code']) ?>]
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Question Type -->
        <div>
          <label class="block text-gray-700 font-medium mb-1">Question Type</label>
          <select name="type" id="qType"
                  class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-[var(--primary)] focus:border-[var(--primary)]">
            <option value="mcq">Multiple Choice (MCQ)</option>
            <option value="theory">Theory</option>
          </select>
        </div>

        <!-- Dynamic Question Container -->
        <div id="questionsContainer" class="space-y-6"></div>

        <div class="flex justify-between items-center pt-4">
          <button type="button" onclick="addQuestion()"
                  class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-medium">
            + Add Manual Question
          </button>
          <button type="submit"
                  class="bg-[var(--primary)] hover:bg-[#150133] text-white px-6 py-2 rounded-lg font-medium">
            Save Questions
          </button>

           <button type="button" id="aiGenerateBtn"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
      🤖 Generate with AI
    </button>
        </div>
      </form>
    </div>
  </main>
</div>

<script>
let qi = 0;
function addQuestion(){
  const type = document.getElementById('qType').value;
  const cont = document.getElementById('questionsContainer');
  const block = document.createElement('div');
  block.className = "border-t pt-4";
  block.innerHTML = questionBlockHTML(qi, type);
  cont.appendChild(block);
  qi++;
}

function questionBlockHTML(index, type){
  if(type === 'mcq'){
    return `
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="col-span-2">
          <label class="block text-gray-700 font-medium mb-1">Question</label>
          <textarea name="questions[${index}][text]" required
            class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-[var(--primary)] focus:border-[var(--primary)]"
            rows="3"></textarea>
        </div>
        ${['A','B','C','D'].map((opt,i) => `
          <div>
            <label class="block text-gray-700 mb-1">Option ${opt}</label>
            <input type="text" name="questions[${index}][options][${opt.toLowerCase()}]" required
              class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-[var(--primary)] focus:border-[var(--primary)]">
          </div>
        `).join('')}
        <div class="col-span-2">
          <label class="block text-gray-700 font-medium mb-1">Correct Option</label>
          <select name="questions[${index}][correct]"
            class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-[var(--primary)] focus:border-[var(--primary)]">
            <option value="a">A</option>
            <option value="b">B</option>
            <option value="c">C</option>
            <option value="d">D</option>
          </select>
        </div>
      </div>
    `;
  } else {
    return `
      <div>
        <label class="block text-gray-700 font-medium mb-1">Question</label>
        <textarea name="questions[${index}][text]" required
          class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-[var(--primary)] focus:border-[var(--primary)]"
          rows="3"></textarea>
      </div>
      <div>
        <label class="block text-gray-700 font-medium mb-1">Sample Answer</label>
        <textarea name="questions[${index}][answer]"
          class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-[var(--primary)] focus:border-[var(--primary)]"
          rows="3"></textarea>
      </div>
    `;
  }
}
</script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$('#aiGenerateBtn').on('click', function() {
  const courseId = $('select[name="course_id"]').val();
  const type = $('#qType').val();

  if (!courseId) {
    alert('Please select a course first.');
    return;
  }

  const btn = $(this);
  btn.prop('disabled', true).text('Generating... ⏳');

  $.ajax({
    url: 'generate_ai_questions.php',
    type: 'POST',
    data: { course_id: courseId, type: type },
    dataType: 'json',
    success: function(res) {
      if (res.success) {
        alert('_' + res.message);
        location.reload();
      } else {
        alert('❌ ' + res.message);
      }
    },
    error: function(xhr) {
      console.error(xhr.responseText);
      alert('Error generating questions.');
    },
    complete: function() {
      btn.prop('disabled', false).text('🤖 Generate with AI');
    }
  });
});
</script>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
