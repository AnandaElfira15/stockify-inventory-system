<?php
require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../helpers/auth.php';

require_login();

$page_title = 'Barang Keluar';
$err = '';
$ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $p = (int)$_POST['product_id'];
    $q = (int)$_POST['quantity'];
    $date = $_POST['movement_date'];
    $desc = trim($_POST['description']);

    try {

        $pdo->beginTransaction();

        $x = $pdo->prepare(
            'SELECT stock
             FROM products
             WHERE id=?
             FOR UPDATE'
        );

        $x->execute([$p]);

        $row = $x->fetch();

        if (!$row) {
            throw new Exception('Produk tidak ditemukan.');
        }

        if ($q <= 0 || $q > $row['stock']) {
            throw new Exception(
                'Jumlah keluar tidak boleh melebihi stok.'
            );
        }

        $pdo->prepare(
            'UPDATE products
             SET stock=stock-?
             WHERE id=?'
        )->execute([$q, $p]);

        $pdo->prepare(
            "INSERT INTO stock_movements
            (product_id,user_id,type,quantity,description,movement_date)
            VALUES(?,?,'OUT',?,?,?)"
        )->execute([
            $p,
            $_SESSION['user']['id'],
            $q,
            $desc,
            $date
        ]);

        $pdo->commit();

        $ok = 'Barang keluar berhasil.';

    } catch (Throwable $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $err = $e->getMessage();
    }
}

$products = $pdo->query(
    'SELECT id,sku,name,stock
     FROM products
     ORDER BY name'
)->fetchAll();

require __DIR__.'/../../views/header.php';
?>

<div class="card">

    <?php if ($err): ?>

        <div class="alert">
            <?=e($err)?>
        </div>

    <?php endif; ?>

    <?php if ($ok): ?>

        <div class="alert ok">
            <?=e($ok)?>
        </div>

    <?php endif; ?>


    <form method="post" class="grid">

        <div>

            <label>Produk</label>

            <select
                name="product_id"
                required
            >

                <option value="">
                    Pilih
                </option>

                <?php foreach ($products as $p): ?>

                    <option value="<?=$p['id']?>">

                        <?=e($p['sku'].' - '.$p['name'])?>

                        (Stok <?=$p['stock']?>)

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
                required
            >

        </div>


        <div>

            <label>Tanggal</label>

            <input
                type="date"
                name="movement_date"
                value="<?=date('Y-m-d')?>"
                required
            >

        </div>


        <div>

            <label>Keterangan</label>

            <textarea name="description"></textarea>

        </div>


        <div style="grid-column: 1 / -1; text-align: right; margin-top: 5px;">

            <button
                type="submit"
                class="btn"
            >
                Simpan
            </button>

        </div>

    </form>

</div>

<?php
require __DIR__.'/../../views/footer.php';
?>