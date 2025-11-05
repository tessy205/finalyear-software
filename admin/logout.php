<?php
require_once __DIR__ . '/../includes/functions.php';
session_start();
unset($_SESSION['admin_id']);
flash_set('success','Logged out.');
redirect('index.php');
