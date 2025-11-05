<?php
// lecturer/generate_ai_questions.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_lecturer();

header('Content-Type: application/json');

$lecturer_id = (int)$_SESSION['lecturer_id'];
$course_id = (int)($_POST['course_id'] ?? 0);
$type = $_POST['type'] ?? 'mcq';

if (!$course_id) {
    echo json_encode(['success' => false, 'message' => 'Missing course ID']);
    exit;
}
if ($type != 'mcq') {
    echo json_encode(['success' => false, 'message' => 'Multiple Choice is currently allowed for auto generation']);
    exit;
}

// Verify lecturer owns this course
$stmt = $pdo->prepare("SELECT title, course_code, info FROM courses WHERE id = ? AND lecturer_id = ?");
$stmt->execute([$course_id, $lecturer_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$course) {
    echo json_encode(['success' => false, 'message' => 'Invalid course or unauthorized.']);
    exit;
}

// Build AI prompt
$prompt = "Generate 2 high-quality " . strtoupper($type) . " questions for a university-level course.\n" .
          "Course Title: {$course['title']}\n" .
          "Course Code: {$course['course_code']}\n" .
          "If MCQ, provide options A, B, C, D and indicate the correct answer clearly.\n" .
          "If Theory, provide only question and model answer.\n" .
          "Return JSON in the format:\n" .
          "[ { \"question\": \"...\", \"options\": {\"a\": \"...\", \"b\": \"...\", \"c\": \"...\", \"d\": \"...\"}, \"answer\": \"a\" } ] for MCQ,\n" .
          "or [ { \"question\": \"...\", \"answer\": \"...\" } ] for theory.";

try {
    // Send to Gemini API
    $api_key = 'AIzaSyB0vhbx2NSQ_65uBeUUur3bU-ZjmVbLjd0'; // 🔒 Ideally load from env
    $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$api_key";

    $payload = json_encode([
        "contents" => [
            [
                "parts" => [["text" => $prompt]]
            ]
        ]
    ]);

    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
       CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "GEMINI_API_KEY: $api_key"  // 🔹 Required header (matches your Postman setup)
    ],
        CURLOPT_POSTFIELDS => $payload
    ]);
    $response = curl_exec($ch);
    if (curl_errno($ch)) throw new Exception(curl_error($ch));
    curl_close($ch);

    $data = json_decode($response, true);
    $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
// var_dump($rawText);
// var_dump($prompt);
    // Attempt to extract JSON prompt
    if (preg_match('/\[[\s\S]+\]/', $rawText, $matches)) {
        $jsonData = json_decode($matches[0], true);
    } else {
        throw new Exception('AI returned unstructured response.');
    }

    if (!$jsonData || !is_array($jsonData)) {
        throw new Exception('Invalid JSON structure.');
    }

    // Save to DB
    $insert = $pdo->prepare("INSERT INTO questions (course_id, lecturer_id, type, question_text, options, answer)
                             VALUES (?, ?, ?, ?, ?, ?)");
    $count = 0;
    foreach ($jsonData as $q) {
        $text = trim($q['question'] ?? '');
        if ($type === 'mcq') {
            $opts = json_encode($q['options'] ?? [], JSON_UNESCAPED_UNICODE);
            $ans = $q['answer'] ?? '';
            $insert->execute([$course_id, $lecturer_id, 'mcq', $text, $opts, $ans]);
        } else {
            $ans = trim($q['answer'] ?? '');
            $insert->execute([$course_id, $lecturer_id, 'theory', $text, null, $ans]);
        }
        $count++;
    }

    echo json_encode(['success' => true, 'message' => "$count AI questions generated and saved."]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
