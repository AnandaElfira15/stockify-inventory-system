<?php
require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../helpers/auth.php';

require_login();

$page_title = 'Supplier';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $c = trim($_POST['code']);
    $n = trim($_POST['name']);
    $ph = trim($_POST['phone']);
    $a = trim($_POST['address']);

    if ($c === '' || $n === '') {

        $err = 'Kode dan nama wajib diisi.';

    } else {

        try {

            $q = $pdo->prepare(
                'INSERT INTO suppliers(code,name,phone,address)
                 VALUES(?,?,?,?)'
            );

            $q->execute([$c, $n, $ph, $a]);

            header('Location:index.php');
            exit;

        } catch (PDOException $e) {

            $err = 'Kode supplier sudah ada.';

        }
    }
}

if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    $q = $pdo->prepare(
        'SELECT COUNT(*) FROM stock_movements WHERE supplier_id=?'
    );

    $q->execute([$id]);

    if ($q->fetchColumn() > 0) {

        $err = 'Supplier memiliki riwayat transaksi.';

    } else {

        $pdo->prepare(
            'DELETE FROM product_suppliers WHERE supplier_id=?'
        )->execute([$id]);

        $pdo->prepare(
            'DELETE FROM suppliers WHERE id=?'
        )->execute([$id]);

        header('Location:index.php');
        exit;
    }
}

$rows = $pdo->query(
    'SELECT *
     FROM suppliers
     ORDER BY name ASC'
)->fetchAll();

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
            <label>Kode</label>

            <input
                type="text"
                name="code"
                required
            >
        </div>

        <div>
            <label>Nama</label>

            <input
                type="text"
                name="name"
                required
            >
        </div>

        <div>
            <label>Telepon</label>

            <input
                type="text"
                name="phone"
            >
        </div>

        <div>
            <label>Alamat</label>

            <textarea name="address"></textarea>
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


<div class="card">

    <table>

        <tr>
            <th>NO</th>
            <th>Kode</th>
            <th>Nama</th>
            <th>Telepon</th>
            <th>Alamat</th>
            <th>Aksi</th>
        </tr>

        <?php foreach ($rows as $i => $r): ?>

            <tr>

                <td>
                    <?=$i + 1?>
                </td>

                <td>
                    <?=e($r['code'])?>
                </td>

                <td>
                    <?=e($r['name'])?>
                </td>

                <td>
                    <?=e($r['phone'])?>
                </td>

                <td>
                    <?=e($r['address'])?>
                </td>

                <td>

                    <a
                        class="btn"
                        href="edit.php?id=<?=$r['id']?>"
                    >
                        Edit
                    </a>

                    <a
                        class="btn red"
                        href="?delete=<?=$r['id']?>"
                        onclick="return confirm('Hapus supplier ini?')"
                    >
                        Hapus
                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

    </table>

</div>

<?php
require __DIR__.'/../../views/footer.php';
