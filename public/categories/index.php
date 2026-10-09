<?php

require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../helpers/auth.php';

require_login();

$page_title = 'Kategori';
$err = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $n = trim($_POST['name']);

    if ($n === '') {

        $err = 'Nama wajib diisi.';

    } else {

        try {

            $q = $pdo->prepare(
                'INSERT INTO categories(name)
                 VALUES(?)'
            );

            $q->execute([$n]);

            header('Location: index.php');
            exit;

        } catch (PDOException $e) {

            $err = 'Kategori sudah ada.';

        }
    }
}


if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    $q = $pdo->prepare(
        'SELECT COUNT(*)
         FROM products
         WHERE category_id=?'
    );

    $q->execute([$id]);


    if ($q->fetchColumn() > 0) {

        $err = 'Kategori masih memiliki produk.';

    } else {

        $pdo->prepare(
            'DELETE FROM categories
             WHERE id=?'
        )->execute([$id]);

        header('Location: index.php');
        exit;

    }
}


$rows = $pdo->query(
    'SELECT
        c.id,
        c.name,
        COUNT(p.id) product_count
     FROM categories c
     LEFT JOIN products p ON p.category_id=c.id
     GROUP BY c.id,c.name
     ORDER BY c.name ASC'
)->fetchAll();


require __DIR__.'/../../views/header.php';

?>


<div class="card">

    <?php if ($err): ?>

        <div class="alert">
            <?=e($err)?>
        </div>

    <?php endif; ?>


    <form method="post">

        <label>
            Nama Kategori
        </label>

        <input
            type="text"
            name="name"
            required
        >


        <div
            style="
                text-align: right;
                margin-top: 10px;
            "
        >

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

            <th>
                NO
            </th>

            <th>
                Nama Kategori
            </th>

            <th style="text-align: center; white-space: nowrap;">
                Jumlah Produk
            </th>

            <th>
                Aksi
            </th>

        </tr>


        <?php foreach ($rows as $i => $r): ?>

            <tr>

                <td>
                    <?=$i + 1?>
                </td>


                <td>
                    <?=e($r['name'])?>
                </td>


                <td
                    style="
                        text-align: center;
                        white-space: nowrap;
                    "
                >
                    <?=$r['product_count']?>
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
                        onclick="return confirm('Hapus kategori ini?')"
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

?>