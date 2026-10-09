<?php

require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../helpers/auth.php';

require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}


try {

    $pdo->beginTransaction();


    // Mengambil data transaksi yang akan dihapus
    $q = $pdo->prepare(
        'SELECT *
         FROM stock_movements
         WHERE id = ?
         FOR UPDATE'
    );

    $q->execute([$id]);

    $movement = $q->fetch();


    if (!$movement) {
        throw new Exception('Transaksi tidak ditemukan.');
    }


    // Mengunci data produk
    $q = $pdo->prepare(
        'SELECT stock
         FROM products
         WHERE id = ?
         FOR UPDATE'
    );

    $q->execute([
        $movement['product_id']
    ]);

    $product = $q->fetch();


    if (!$product) {
        throw new Exception('Produk tidak ditemukan.');
    }


    /*
     * Jika transaksi barang masuk dihapus,
     * stok harus dikurangi kembali.
     */
    if ($movement['type'] === 'IN') {

        if ($product['stock'] < $movement['quantity']) {

            throw new Exception(
                'Transaksi tidak dapat dihapus karena stok saat ini tidak mencukupi untuk penyesuaian.'
            );

        }

        $q = $pdo->prepare(
            'UPDATE products
             SET stock = stock - ?
             WHERE id = ?'
        );

        $q->execute([
            $movement['quantity'],
            $movement['product_id']
        ]);

    }


    /*
     * Jika transaksi barang keluar dihapus,
     * stok harus dikembalikan.
     */
    else {

        $q = $pdo->prepare(
            'UPDATE products
             SET stock = stock + ?
             WHERE id = ?'
        );

        $q->execute([
            $movement['quantity'],
            $movement['product_id']
        ]);

    }


    // Menghapus transaksi
    $q = $pdo->prepare(
        'DELETE FROM stock_movements
         WHERE id = ?'
    );

    $q->execute([$id]);


    $pdo->commit();


    // Kembali ke halaman Riwayat Transaksi
    header('Location: index.php');
    exit;


} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo '<!doctype html>';
    echo '<html lang="id">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<title>Gagal Menghapus Transaksi</title>';
    echo '</head>';
    echo '<body style="font-family: Arial; padding: 30px;">';

    echo '<h2>Transaksi tidak dapat dihapus</h2>';

    echo '<p>'
        . htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        )
        . '</p>';

    echo '<p>';
    echo '<a href="index.php">Kembali ke Riwayat Transaksi</a>';
    echo '</p>';

    echo '</body>';
    echo '</html>';
}