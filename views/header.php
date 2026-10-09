<?php
require_once __DIR__.'/../helpers/auth.php';
require_login();

$title = $page_title ?? 'Stockify';
$u = $_SESSION['user'];
?>

<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($title)?> - Stockify</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial;
    background: #F4F6F9;
    color: #111827;
}

.side {
    position: fixed;
    width: 230px;
    top: 0;
    bottom: 0;
    background: #0B1F3A;
    color: #fff;
    padding: 20px;
}

.brand {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 12px;
}

.role {
    font-size: 14px;
    color: #D1D5DB;
    margin-bottom: 15px;
}

.side hr {
    border: 0;
    border-top: 1px solid #36506f;
    margin: 0 0 12px 0;
}

.side a {
    display: block;
    color: #fff;
    text-decoration: none;
    padding: 10px;
    border-radius: 6px;
}

.side a:hover {
    background: #17345d;
}

.main {
    margin-left: 230px;
    padding: 25px;
}

.card {
    background: #fff;
    padding: 18px;
    border-radius: 10px;
    margin-bottom: 18px;
    box-shadow: 0 2px 7px #ddd;
}

.cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
}

.num {
    font-size: 25px;
    font-weight: bold;
    margin-top: 8px;
}

.grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

input,
select,
textarea {
    width: 100%;
    padding: 9px;
    border: 1px solid #ccc;
    border-radius: 6px;
    margin-top: 5px;
}

.btn {
    display: inline-block;
    padding: 8px 12px;
    border: 0;
    border-radius: 6px;
    background: #2563EB;
    color: #fff;
    text-decoration: none;
    cursor: pointer;
}

.red {
    background: #DC2626;
}

.green {
    background: #16A34A;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    padding: 9px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

.toolbar {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr auto;
    gap: 8px;
    align-items: end;
}

.alert {
    padding: 10px;
    background: #FEE2E2;
    color: #991B1B;
    border-radius: 6px;
    margin-bottom: 12px;
}

.ok {
    background: #DCFCE7;
    color: #166534;
}

@media(max-width:800px) {
    .side {
        width: 180px;
    }

    .main {
        margin-left: 180px;
    }

    .cards {
        grid-template-columns: repeat(2, 1fr);
    }

    .toolbar,
    .grid {
        grid-template-columns: 1fr;
    }
}
</style>

</head>

<body>

<aside class="side">

    <div class="brand">
        Stockify
    </div>

    <div class="role">
        <?=e(ucfirst($u['role']))?>
    </div>

    <hr>

    <a href="/stockify/public/dashboard.php">
        Dashboard
    </a>

    <a href="/stockify/public/products/index.php">
        Produk
    </a>

    <a href="/stockify/public/categories/index.php">
        Kategori
    </a>

    <a href="/stockify/public/suppliers/index.php">
        Supplier
    </a>

    <a href="/stockify/public/stock/incoming.php">
        Barang Masuk
    </a>

    <a href="/stockify/public/stock/outgoing.php">
        Barang Keluar
    </a>

    <a href="/stockify/public/transactions/index.php">
        Riwayat Transaksi
    </a>

    <a href="/stockify/public/reports/index.php">
        Laporan
    </a>

    <?php if($u['role'] === 'admin'): ?>

        <a href="/stockify/public/users/index.php">
            Pengguna
        </a>

    <?php endif; ?>

    <a href="/stockify/public/logout.php">
        Logout
    </a>

</aside>

<main class="main">

    <h1>
        <?=e($title)?>
    </h1>