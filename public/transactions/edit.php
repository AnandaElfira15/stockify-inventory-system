<?php

require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../helpers/auth.php';

require_login();

$page_title = 'Edit Transaksi';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}


/* Mengambil data transaksi */
$q = $pdo->prepare(
    "SELECT
        sm.*,
        p.sku,
        p.name AS product
     FROM stock_movements sm
     JOIN products p ON p.id = sm.product_id
     WHERE sm.id = ?"
);

$q->execute([$id]);

$movement = $q->fetch();


if (!$movement) {
    header('Location: index.php');
    exit;
}


$err = '';


/* Mengambil daftar produk */
$products = $pdo->query(
    'SELECT
        id,
        sku,
        name,
        stock
     FROM products
     ORDER BY name ASC'
)->fetchAll();


/* Mengambil daftar supplier */
$suppliers = $pdo->query(
    'SELECT
        id,
        code,
        name
     FROM suppliers
     ORDER BY name ASC'
)->fetchAll();


/* Proses menyimpan perubahan */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $product_id = (int)$_POST['product_id'];
    $supplier_id = (int)($_POST['supplier_id'] ?? 0);
    $quantity = (int)$_POST['quantity'];
    $movement_date = $_POST['movement_date'];
    $description = trim($_POST['description']);


    if (
        $product_id <= 0 ||
        $quantity <= 0 ||
        $movement_date === ''
    ) {

        $err = 'Data transaksi tidak valid.';

    } else {

        try {

            $pdo->beginTransaction();


            /*
             * Data transaksi lama
             */
            $oldProductId = (int)$movement['product_id'];
            $oldQuantity = (int)$movement['quantity'];
            $oldType = $movement['type'];


            /*
             * Kunci produk lama
             */
            $q = $pdo->prepare(
                'SELECT stock
                 FROM products
                 WHERE id = ?
                 FOR UPDATE'
            );

            $q->execute([$oldProductId]);

            $oldProduct = $q->fetch();


            if (!$oldProduct) {
                throw new Exception('Produk lama tidak ditemukan.');
            }


            /*
             * Kembalikan stok seperti sebelum
             * transaksi lama dibuat.
             */
            if ($oldType === 'IN') {

                if ($oldProduct['stock'] < $oldQuantity) {

                    throw new Exception(
                        'Stok tidak mencukupi untuk mengubah transaksi barang masuk.'
                    );

                }

                $q = $pdo->prepare(
                    'UPDATE products
                     SET stock = stock - ?
                     WHERE id = ?'
                );

                $q->execute([
                    $oldQuantity,
                    $oldProductId
                ]);

            } else {

                $q = $pdo->prepare(
                    'UPDATE products
                     SET stock = stock + ?
                     WHERE id = ?'
                );

                $q->execute([
                    $oldQuantity,
                    $oldProductId
                ]);

            }


            /*
             * Jika produk baru berbeda dari produk lama,
             * kunci produk baru.
             */
            $q = $pdo->prepare(
                'SELECT stock
                 FROM products
                 WHERE id = ?
                 FOR UPDATE'
            );

            $q->execute([$product_id]);

            $newProduct = $q->fetch();


            if (!$newProduct) {
                throw new Exception('Produk tidak ditemukan.');
            }


            /*
             * Terapkan stok berdasarkan jenis transaksi.
             */
            if ($oldType === 'IN') {

                $q = $pdo->prepare(
                    'UPDATE products
                     SET stock = stock + ?
                     WHERE id = ?'
                );

                $q->execute([
                    $quantity,
                    $product_id
                ]);

            } else {

                if ($quantity > $newProduct['stock']) {

                    throw new Exception(
                        'Jumlah barang keluar melebihi stok yang tersedia.'
                    );

                }

                $q = $pdo->prepare(
                    'UPDATE products
                     SET stock = stock - ?
                     WHERE id = ?'
                );

                $q->execute([
                    $quantity,
                    $product_id
                ]);

            }


            /*
             * Jika supplier tidak dipilih,
             * simpan sebagai NULL.
             */
            $supplierValue = $supplier_id > 0
                ? $supplier_id
                : null;


            /*
             * Update data transaksi.
             */
            $q = $pdo->prepare(
                'UPDATE stock_movements
                 SET
                    product_id = ?,
                    supplier_id = ?,
                    quantity = ?,
                    description = ?,
                    movement_date = ?
                 WHERE id = ?'
            );

            $q->execute([
                $product_id,
                $supplierValue,
                $quantity,
                $description,
                $movement_date,
                $id
            ]);


            $pdo->commit();


            header('Location: index.php');
            exit;


        } catch (Throwable $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $err = $e->getMessage();
        }

    }
}


require __DIR__.'/../../views/header.php';

?>


<div class="card">

    <?php if ($err): ?>

        <div class="alert">
            <?=e($err)?>
        </div>

    <?php endif; ?>


    <form method="post" class="grid">


        <div>

            <label>Jenis Transaksi</label>

            <input
                type="text"
                value="<?=$movement['type'] === 'IN' ? 'Barang Masuk' : 'Barang Keluar'?>"
                disabled
            >

        </div>


        <div>

            <label>Produk</label>

            <select name="product_id" required>

                <?php foreach ($products as $p): ?>

                    <option
                        value="<?=$p['id']?>"
                        <?=$p['id'] == $movement['product_id'] ? 'selected' : ''?>
                    >

                        <?=e($p['sku'].' - '.$p['name'])?>
                        (Stok <?=$p['stock']?>)

                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <div>

            <label>Supplier</label>

            <select name="supplier_id">

                <option value="">
                    Tidak ada supplier
                </option>


                <?php foreach ($suppliers as $s): ?>

                    <option
                        value="<?=$s['id']?>"
                        <?=$s['id'] == $movement['supplier_id'] ? 'selected' : ''?>
                    >

                        <?=e($s['code'].' - '.$s['name'])?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <div>

            <label>Jumlah</label>

            <input
                type="number"
                name="quantity"
                min="1"
                value="<?=e($movement['quantity'])?>"
                required
            >

        </div>


        <div>

            <label>Tanggal</label>

            <input
                type="date"
                name="movement_date"
                value="<?=e($movement['movement_date'])?>"
                required
            >

        </div>


        <div>

            <label>Keterangan</label>

            <textarea name="description"><?=e($movement['description'] ?? '')?></textarea>

        </div>


        <div
            style="
                grid-column: 1 / -1;
                text-align: right;
                margin-top: 5px;
            "
        >

            <button
                type="submit"
                class="btn"
            >
                Simpan Perubahan
            </button>


            <a
                href="index.php"
                class="btn red"
            >
                Batal
            </a>

        </div>


    </form>

</div>


<?php

require __DIR__.'/../../views/footer.php';

?>