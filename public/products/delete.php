<?php

require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../helpers/auth.php';

require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}


try {

    $pdo->beginTransaction();


    // Cek produk
    $q = $pdo->prepare(
        'SELECT *
         FROM products
         WHERE id = ?
         FOR UPDATE'
    );

    $q->execute([$id]);

    $product = $q->fetch();


    if (!$product) {
        throw new Exception('Produk tidak ditemukan.');
    }


    /*
     * Produk yang sudah memiliki riwayat transaksi
     * tidak boleh langsung dihapus.
     */
    $q = $pdo->prepare(
        'SELECT COUNT(*)
         FROM stock_movements
         WHERE product_id = ?'
    );

    $q->execute([$id]);

    $transactionCount = $q->fetchColumn();


    if ($transactionCount > 0) {
        throw new Exception(
            'Produk tidak dapat dihapus karena sudah memiliki riwayat transaksi.'
        );
    }


    // Hapus relasi produk dengan supplier
    $q = $pdo->prepare(
        'DELETE FROM product_suppliers
         WHERE product_id = ?'
    );

    $q->execute([$id]);


    // Hapus produk
    $q = $pdo->prepare(
        'DELETE FROM products
         WHERE id = ?'
    );

    $q->execute([$id]);


    $pdo->commit();


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

    echo '<title>Produk Tidak Dapat Dihapus</title>';

    echo '</head>';


    echo '<body style="font-family: Arial; padding: 30px;">';

    echo '<h2>Produk tidak dapat dihapus</h2>';

    echo '<p>'
        . htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        )
        . '</p>';

    echo '<p>';

    echo '<a href="index.php">
            Kembali ke Produk
        </a>';

    echo '</p>';

    echo '</body>';

    echo '</html>';
}