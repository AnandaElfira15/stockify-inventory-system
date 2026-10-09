<?php

require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../helpers/auth.php';

require_login();

$page_title = 'Laporan';


$s = $pdo->query(
    'SELECT
        COUNT(*) products,
        COALESCE(SUM(stock),0) stock,
        COALESCE(SUM(stock*price),0) value
     FROM products'
)->fetch();


$m = $pdo->query(
    "SELECT
        COALESCE(SUM(CASE WHEN type='IN' THEN quantity ELSE 0 END),0) inc,
        COALESCE(SUM(CASE WHEN type='OUT' THEN quantity ELSE 0 END),0) outq
     FROM stock_movements"
)->fetch();


$rows = $pdo->query(
    "SELECT
        p.sku,
        p.name,
        c.name category,
        p.stock,
        p.minimum_stock,
        COALESCE(
            SUM(
                CASE
                    WHEN sm.type='IN' THEN sm.quantity
                    ELSE 0
                END
            ),0
        ) inc,
        COALESCE(
            SUM(
                CASE
                    WHEN sm.type='OUT' THEN sm.quantity
                    ELSE 0
                END
            ),0
        ) outq
     FROM products p
     JOIN categories c ON c.id=p.category_id
     LEFT JOIN stock_movements sm ON sm.product_id=p.id
     GROUP BY p.id
     ORDER BY p.name"
)->fetchAll();


require __DIR__.'/../../views/header.php';
?>


<div class="cards">

    <div class="card">
        Produk

        <div class="num">
            <?=$s['products']?>
        </div>
    </div>


    <div class="card">
        Stok

        <div class="num">
            <?=$s['stock']?>
        </div>
    </div>


    <div class="card">
        Masuk

        <div class="num">
            <?=$m['inc']?>
        </div>
    </div>


    <div class="card">
        Keluar

        <div class="num">
            <?=$m['outq']?>
        </div>
    </div>

</div>


<div class="card">

    <p>
        Nilai persediaan:
        <b>
            Rp <?=number_format($s['value'],0,',','.')?>
        </b>
    </p>


    <table>

        <tr>

            <th style="white-space: nowrap;">
                NO
            </th>

            <th style="white-space: nowrap;">
                SKU
            </th>

            <th>
                Produk
            </th>

            <th>
                Kategori
            </th>

            <th style="white-space: nowrap; text-align: center;">
                Stok
            </th>

            <th style="white-space: nowrap; text-align: center;">
                Minimum Stok
            </th>

            <th style="white-space: nowrap; text-align: center;">
                Masuk
            </th>

            <th style="white-space: nowrap; text-align: center;">
                Keluar
            </th>

        </tr>


        <?php foreach($rows as $i=>$r): ?>

            <tr>

                <td style="white-space: nowrap;">
                    <?=$i+1?>
                </td>


                <td style="white-space: nowrap;">
                    <?=e($r['sku'])?>
                </td>


                <td>
                    <?=e($r['name'])?>
                </td>


                <td>
                    <?=e($r['category'])?>
                </td>


                <td style="white-space: nowrap; text-align: center;">
                    <?=$r['stock']?>
                </td>


                <td style="white-space: nowrap; text-align: center;">
                    <?=$r['minimum_stock']?>
                </td>


                <td style="white-space: nowrap; text-align: center;">
                    <?=$r['inc']?>
                </td>


                <td style="white-space: nowrap; text-align: center;">
                    <?=$r['outq']?>
                </td>

            </tr>

        <?php endforeach; ?>

    </table>

</div>


<?php

require __DIR__.'/../../views/footer.php';

?>