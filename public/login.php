<?php

session_start();

require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../helpers/auth.php';


if (!empty($_SESSION['user'])) {

    header('Location:dashboard.php');
    exit;

}


$err = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $s = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';


    $q = $pdo->prepare(
        'SELECT
            id,
            name,
            username,
            password,
            role
         FROM users
         WHERE username=?'
    );

    $q->execute([$s]);

    $u = $q->fetch();


    if ($u && password_verify($p, $u['password'])) {

        session_regenerate_id(true);

        unset($u['password']);

        $_SESSION['user'] = $u;

        header('Location:dashboard.php');
        exit;

    }


    $err = 'Username atau password salah.';

}

?>


<!doctype html>

<html>

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width,initial-scale=1"
    >

    <title>
        Login Stockify
    </title>


    <style>

        body {
            font-family: Arial;
            background: #F4F6F9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }


        .box {
            background: white;
            padding: 30px;
            width: 360px;
            border-radius: 12px;
            box-shadow: 0 3px 15px #ddd;
        }


        input,
        button {
            width: 100%;
            padding: 11px;
            margin: 7px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }


        button {
            background: #2563EB;
            color: #fff;
            border: 0;
        }


        .err {
            background: #fee2e2;
            padding: 10px;
        }

    </style>

</head>


<body>


<div class="box">

    <h1>
        Stockify
    </h1>


    <p>
        Inventory Management System
    </p>


    <?php if($err): ?>

        <div class="err">
            <?=e($err)?>
        </div>

    <?php endif; ?>


    <form method="post">

        <label>
            Username
        </label>

        <input
            name="username"
            required
        >


        <label>
            Password
        </label>

        <input
            type="password"
            name="password"
            required
        >


        <button>
            Masuk
        </button>

    </form>

</div>


</body>

</html>