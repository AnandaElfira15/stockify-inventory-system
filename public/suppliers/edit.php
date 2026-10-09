<?php
require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../helpers/auth.php';

require_login();

$page_title = 'Edit Supplier';
$err = '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$q = $pdo->prepare(
    'SELECT *
     FROM suppliers
     WHERE id=?'
);

$q->execute([$id]);

$supplier = $q->fetch();

if (!$supplier) {
    header('Location: index.php');
    exit;
}

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
                'UPDATE suppliers
                 SET code=?, name=?, phone=?, address=?
                 WHERE id=?'
            );

            $q->execute([
                $c,
                $n,
                $ph,
                $a,
                $id
            ]);

            header('Location: index.php');
            exit;

        } catch (PDOException $e) {

            $err = 'Kode supplier sudah digunakan.';

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
            <label>Kode</label>

            <input
                type="text"
                name="code"
                value="<?=e($supplier['code'])?>"
                required
            >
        </div>

        <div>
            <label>Nama</label>

            <input
                type="text"
                name="name"
                value="<?=e($supplier['name'])?>"
                required
            >
        </div>

        <div>
            <label>Telepon</label>

            <input
                type="text"
                name="phone"
                value="<?=e($supplier['phone'])?>"
            >
        </div>

        <div>
            <label>Alamat</label>

            <textarea name="address"><?=e($supplier['address'])?></textarea>
        </div>

        <div style="grid-column: 1 / -1; text-align: center; margin-top: 5px;">

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