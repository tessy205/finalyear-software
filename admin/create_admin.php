<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';


$adminEmail = 'admin@example.com';
$adminPassPlain = 'Admin@1234';


$stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
$stmt->execute([$adminEmail]);
if ($stmt->fetch()) {
    echo "Admin already exists for $adminEmail\n";
    exit;
}

$hash = make_hash($adminPassPlain);
$pdo->prepare("INSERT INTO admins (email, password) VALUES (?, ?)")->execute([$adminEmail, $hash]);
echo "Admin created. Email: {$adminEmail} Password: {$adminPassPlain}\n";
echo "Delete this file after use for safety.\n";
