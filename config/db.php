<?php

// config/db.php
declare(strict_types=1);
ini_set('display_errors', '1');            // show errors to browser
ini_set('display_startup_errors', '1');    // show startup errors
error_reporting(E_ALL);

$DB_HOST = '127.0.0.1';
$DB_NAME = 'sea_ai';
$DB_USER = 'root';
$DB_PASS = ''; // set your mysql password
$DB_CHAR = 'utf8mb4';

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHAR}";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // In production log and show generic error
    die("DB connection failed: " . $e->getMessage());
}
