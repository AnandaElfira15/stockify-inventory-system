<?php
if(session_status()!==PHP_SESSION_ACTIVE)session_start();
function require_login(){if(empty($_SESSION['user'])){header('Location:/stockify/public/login.php');exit;}}
function require_admin(){require_login();if($_SESSION['user']['role']!=='admin'){http_response_code(403);die('403 Forbidden - Khusus Admin.');}}
function e($v){return htmlspecialchars((string)($v??''),ENT_QUOTES,'UTF-8');}
?>
