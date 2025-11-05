<?php
// includes/functions.php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();

// function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function e($v){ return $v; }


function redirect($url = null){
     if (empty($url)) {
        // Determine the protocol
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        // Build current URL
        $url = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    header("Location: $url");
    exit;
}

function flash_set($key, $msg){
    if (!isset($_SESSION['flash'])) $_SESSION['flash'] = [];
    $_SESSION['flash'][$key] = $msg;
}

function flash_get($key){
    if (!isset($_SESSION['flash'][$key])) return null;
    $v = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $v;
}

/* CSRF token */
function csrf_token(){
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check($token){
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/* Auth guards */
function require_admin(){
    if (empty($_SESSION['admin_id'])) {
        redirect('/admin/index.php');
    }
}
function require_lecturer(){
    if (empty($_SESSION['lecturer_id'])) {
        redirect('/lecturer/login.php');
    }
}

/* Simple mail wrapper - replace headers and settings for production */
function send_mail_simple($to, $subject, $body){
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";
    // NOTE: PHP mail() depends on server config. Replace with SMTP in production.
    return mail($to, $subject, $body, $headers);
}

/* Password helpers */
function make_hash($plain){ return password_hash($plain, PASSWORD_DEFAULT); }
function verify_hash($plain, $hash){ return password_verify($plain, $hash); }
