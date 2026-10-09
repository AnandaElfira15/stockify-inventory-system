<?php
$dsn='mysql:host=127.0.0.1;dbname=stockify;charset=utf8mb4';
try{$pdo=new PDO($dsn,'root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);}catch(PDOException $e){die('Koneksi database gagal. Pastikan MySQL aktif dan database stockify sudah dibuat.');}
?>
