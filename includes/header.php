<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ExamApp</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root {
      --primary: #1c0144;
    }
  </style>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="flex h-screen overflow-hidden">
