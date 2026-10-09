<?php

require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../helpers/auth.php';

require_login();

$page_title = 'Riwayat Transaksi';

$search = trim($_GET['search'] ?? '');


if ($search !== '') {

    $q = $pdo->prepare(
        "SELECT
            sm.*,
            p.sku,
            p.name AS product,
            u.name AS user_name,
            s.name AS supplier
         FROM stock_movements sm
         JOIN products p ON p.id = sm.product_id
         JOIN users u ON u.id = sm.user_id
         LEFT JOIN suppliers s ON s.id = sm.supplier_id
         WHERE p.sku LIKE ?
            OR p.name LIKE ?
            OR sm.description LIKE ?
         ORDER BY sm.id DESC"
    );

    $keyword = '%' . $search . '%';

    $q->execute([
        $keyword,
        $keyword,
        $keyword
    ]);

} else {

    $q = $pdo->prepare(
        "SELECT
            sm.*,
            p.sku,
            p.name AS product,
            u.name AS user_name,
            s.name AS supplier
         FROM stock_movements sm
         JOIN products p ON p.id = sm.product_id
         JOIN users u ON u.id = sm.user_id
         LEFT JOIN suppliers s ON s.id = sm.supplier_id
         ORDER BY sm.id DESC"
    );

    $q->execute();
}


$rows = $q->fetchAll();


require __DIR__.'/../../views/header.php';
?>


<div class="card">

    <form method="get">

        <label>Pencarian</label>

        <input
            type="text"
            name="search"
            value="<?=e($search)?>"
            placeholder="Cari SKU, nama produk, atau keterangan..."
        >

        <div style="text-align: right; margin-top: 10px;">

            <button
                type="submit"
                class="btn"
            >
                Cari
            </button>

            <?php if ($search !== ''): ?>

                <a
                    href="index.php"
                    class="btn red"
                >
                    Reset
                </a>

            <?php endif; ?>

        </div>

    </form>

</div>


<div class="card">

    <table>

        <tr>

            <th style="white-space: nowrap;">
                NO
            </th>

            <th style="white-space: nowrap; width: 110px;">
                Tanggal
            </th>

            <th style="white-space: nowrap;">
                SKU
            </th>

            <th>
                Produk
            </th>

            <th style="white-space: nowrap;">
                Tipe
            </th>

            <th style="white-space: nowrap; text-align: center;">
                Jumlah
            </th>

            <th>
                Supplier
            </th>

            <th>
                User
            </th>

            <th>
                Keterangan
            </th>

            <th style="white-space: nowrap;">
                Aksi
            </th>

        </tr>


        <?php if (count($rows) === 0): ?>

            <tr>

                <td
                    colspan="10"
                    style="text-align: center;"
                >
                    Tidak ada transaksi yang ditemukan.
                </td>

            </tr>

        <?php else: ?>

            <?php foreach ($rows as $i => $r): ?>

                <tr>

                    <td style="white-space: nowrap;">
                        <?=$i + 1?>
                    </td>


                    <td style="white-space: nowrap;">
                        <?=e($r['movement_date'])?>
                    </td>


                    <td style="white-space: nowrap;">
                        <?=e($r['sku'])?>
                    </td>


                    <td>
                        <?=e($r['product'])?>
                    </td>


                    <td style="white-space: nowrap;">
                        <?=$r['type'] === 'IN' ? 'Masuk' : 'Keluar'?>
                    </td>


                    <td style="white-space: nowrap; text-align: center;">
                        <?=$r['quantity']?>
                    </td>


                    <td>
                        <?=e($r['supplier'] ?? '-')?>
                    </td>


                    <td>
                        <?=e($r['user_name'])?>
                    </td>


                    <td>
                        <?=e($r['description'] ?? '-')?>
                    </td>


                    <td style="white-space: nowrap;">

                        <a
                            class="btn"
                            href="edit.php?id=<?=$r['id']?>"
                        >
                            Edit
                        </a>

                        <a
                            class="btn red"
                            href="delete.php?id=<?=$r['id']?>"
                            onclick="return confirm('Hapus transaksi ini? Stok produk juga akan disesuaikan.');"
                        >
                            Hapus
                        </a>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php endif; ?>

    </table>

</div>


<?php

require __DIR__.'/../../views/footer.php';

?>