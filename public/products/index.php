<?php

require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../helpers/auth.php';

$page_title = 'Produk';

$s = trim($_GET['search'] ?? '');
$cid = (int)($_GET['category_id'] ?? 0);
$sort = $_GET['sort'] ?? 'name';
$dir = strtoupper($_GET['dir'] ?? 'ASC');


$allow = [
    'name' => 'p.name',
    'sku' => 'p.sku',
    'price' => 'p.price',
    'stock' => 'p.stock'
];


if (!isset($allow[$sort])) {
    $sort = 'name';
}


if (!in_array($dir, ['ASC', 'DESC'], true)) {
    $dir = 'ASC';
}


$w = [];
$par = [];


if ($s !== '') {

    $w[] = '(p.name LIKE ? OR p.sku LIKE ?)';

    $par[] = "%$s%";
    $par[] = "%$s%";
}


if ($cid) {

    $w[] = 'p.category_id=?';

    $par[] = $cid;
}


$where = $w
    ? 'WHERE ' . implode(' AND ', $w)
    : '';


$q = $pdo->prepare(
    "SELECT
        p.*,
        c.name category
     FROM products p
     JOIN categories c ON c.id=p.category_id
     $where
     ORDER BY {$allow[$sort]} $dir"
);


$q->execute($par);

$rows = $q->fetchAll();


$cats = $pdo->query(
    'SELECT *
     FROM categories
     ORDER BY name'
)->fetchAll();


require __DIR__.'/../../views/header.php';

?>


<div class="card">

    <a
        class="btn"
        href="form.php"
    >
        + Tambah Produk
    </a>


    <form
        class="toolbar"
        method="get"
    >

        <div>

            <label>
                Cari Nama Produk / SKU
            </label>

            <input
                name="search"
                value="<?=e($s)?>"
            >

        </div>


        <div>

            <label>
                Kategori
            </label>

            <select name="category_id">

                <option value="0">
                    Semua
                </option>


                <?php foreach($cats as $c): ?>

                    <option
                        value="<?=$c['id']?>"
                        <?=$cid == $c['id'] ? 'selected' : ''?>
                    >
                        <?=e($c['name'])?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <div>

            <label>
                Urutkan
            </label>

            <select name="sort">

                <option
                    value="name"
                    <?=$sort === 'name' ? 'selected' : ''?>
                >
                    Nama
                </option>

                <option
                    value="sku"
                    <?=$sort === 'sku' ? 'selected' : ''?>
                >
                    SKU
                </option>

                <option
                    value="price"
                    <?=$sort === 'price' ? 'selected' : ''?>
                >
                    Harga
                </option>

                <option
                    value="stock"
                    <?=$sort === 'stock' ? 'selected' : ''?>
                >
                    Stok
                </option>

            </select>

        </div>


        <div>

            <label>
                Arah
            </label>

            <select name="dir">

                <option
                    value="ASC"
                    <?=$dir === 'ASC' ? 'selected' : ''?>
                >
                    A-Z / Kecil-Besar
                </option>

                <option
                    value="DESC"
                    <?=$dir === 'DESC' ? 'selected' : ''?>
                >
                    Z-A / Besar-Kecil
                </option>

            </select>

        </div>


        <button
            type="submit"
            class="btn"
        >
            Terapkan
        </button>

    </form>

</div>


<div class="card">

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
                Harga
            </th>

            <th
                style="
                    text-align: center;
                    white-space: nowrap;
                "
            >
                Stok
            </th>

            <th
                style="
                    text-align: center;
                    white-space: nowrap;
                "
            >
                Minimum Stok
            </th>

            <th>
                Aksi
            </th>

        </tr>


        <?php foreach($rows as $i=>$p): ?>

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
                    Rp <?=number_format(
                        $p['price'],
                        0,
                        ',',
                        '.'
                    )?>
                </td>


                <td
                    style="
                        text-align: center;
                        white-space: nowrap;
                    "
                >
                    <?=$p['stock']?>
                </td>


                <td
                    style="
                        text-align: center;
                        white-space: nowrap;
                    "
                >
                    <?=$p['minimum_stock']?>
                </td>


                <td
                    style="
                        white-space: nowrap;
                    "
                >

                    <a
                        class="btn"
                        href="form.php?id=<?=$p['id']?>"
                    >
                        Edit
                    </a>


                    <?php if($_SESSION['user']['role'] === 'admin'): ?>

                        <a
                            class="btn red"
                            href="delete.php?id=<?=$p['id']?>"
                            onclick="return confirm('Hapus?')"
                        >
                            Hapus
                        </a>

                    <?php endif; ?>

                </td>

            </tr>

        <?php endforeach; ?>

    </table>

</div>


<?php

require __DIR__.'/../../views/footer.php';

?>