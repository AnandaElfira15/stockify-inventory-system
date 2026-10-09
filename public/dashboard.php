<?php

require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../helpers/auth.php';

$page_title = 'Dashboard';


$a = $pdo->query(
    'SELECT COUNT(*) total
     FROM products'
)->fetch()['total'];


$b = $pdo->query(
    'SELECT COALESCE(SUM(stock),0) total
     FROM products'
)->fetch()['total'];


$c = $pdo->query(
    'SELECT COUNT(*) total
     FROM products
     WHERE stock <= minimum_stock'
)->fetch()['total'];


$d = $pdo->query(
    'SELECT COUNT(*) total
     FROM suppliers'
)->fetch()['total'];


$low = $pdo->query(
    'SELECT
        p.*,
        c.name category
     FROM products p
     JOIN categories c ON c.id = p.category_id
     WHERE p.stock <= p.minimum_stock
     ORDER BY p.stock, p.name
     LIMIT 10'
)->fetchAll();


require __DIR__.'/../views/header.php';

?>


<div class="cards">

    <div class="card">

        Total Produk

        <div class="num">
            <?=$a?>
        </div>

    </div>


    <div class="card">

        Total Stok

        <div class="num">
            <?=$b?>
        </div>

    </div>


    <div class="card">

        Stok Rendah

        <div class="num">
            <?=$c?>
        </div>

    </div>


    <div class="card">

        Supplier

        <div class="num">
            <?=$d?>
        </div>

    </div>

</div>


<div class="card">

    <h3 style="color: #2563EB;">
        Produk Stok Rendah
    </h3>


    <table>

        <tr>

            <th>
                NO
            </th>

            <th>
                SKU
            </th>

            <th>
                Nama
            </th>

            <th>
                Kategori
            </th>

            <th>
                Stok
            </th>

            <th>
                Minimum
            </th>

        </tr>


        <?php foreach($low as $i=>$p): ?>

            <tr>

                <td>
                    <?=$i+1?>
                </td>

                <td>
                    <?=e($p['sku'])?>
                </td>

                <td>
                    <?=e($p['name'])?>
                </td>

                <td>
                    <?=e($p['category'])?>
                </td>

                <td>
                    <?=$p['stock']?>
                </td>

                <td>
                    <?=$p['minimum_stock']?>
                </td>

            </tr>

        <?php endforeach; ?>

    </table>

</div>


<?php

require __DIR__.'/../views/footer.php';

?>