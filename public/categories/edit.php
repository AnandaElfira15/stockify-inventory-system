<?php
require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../helpers/auth.php';

require_login();

$page_title = 'Edit Kategori';
$err = '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$q = $pdo->prepare('SELECT * FROM categories WHERE id=?');
$q->execute([$id]);

$category = $q->fetch();

if (!$category) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);

    if ($name === '') {

        $err = 'Nama kategori wajib diisi.';

    } else {

        try {

            $q = $pdo->prepare(
                'UPDATE categories SET name=? WHERE id=?'
            );

            $q->execute([$name, $id]);

            header('Location: index.php');
            exit;

        } catch (PDOException $e) {

            $err = 'Kategori sudah ada.';

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

    <form method="post">

        <label>Nama Kategori</label>

        <input
            type="text"
            name="name"
            value="<?=e($category['name'])?>"
            required
        >

        <br><br>

        <button class="btn">
            Simpan Perubahan
        </button>

        <a
            href="index.php"
            class="btn red"
        >
            Batal
        </a>

    </form>

</div>

<?php require __DIR__.'/../../views/footer.php'; ?>