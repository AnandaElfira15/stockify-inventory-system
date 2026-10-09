<?php

require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../helpers/auth.php';

require_admin();

$page_title = 'Pengguna';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $n = trim($_POST['name']);
    $u = trim($_POST['username']);
    $p = $_POST['password'];
    $r = $_POST['role'];

    if (
        $n === '' ||
        $u === '' ||
        strlen($p) < 6 ||
        !in_array($r, ['admin', 'staff'], true)
    ) {
        $err = 'Data tidak valid.';

    } else {

        try {

            $q = $pdo->prepare(
                'INSERT INTO users(name,username,password,role)
                 VALUES(?,?,?,?)'
            );

            $q->execute([
                $n,
                $u,
                password_hash($p, PASSWORD_DEFAULT),
                $r
            ]);

            header('Location:index.php');
            exit;

        } catch (PDOException $e) {

            $err = 'Username sudah digunakan.';

        }
    }
}


if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    if ($id != $_SESSION['user']['id']) {

        $pdo->prepare(
            'DELETE FROM users WHERE id=?'
        )->execute([$id]);

        header('Location:index.php');
        exit;

    } else {

        $err = 'Akun aktif tidak dapat dihapus.';

    }
}


$rows = $pdo->query(
    'SELECT id,name,username,role,created_at
     FROM users
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


    <form method="post" class="grid">

        <div>
            <label>Nama</label>

            <input
                name="name"
                required
            >
        </div>


        <div>
            <label>Username</label>

            <input
                name="username"
                required
            >
        </div>


        <div>
            <label>Password</label>

            <input
                type="password"
                name="password"
                minlength="6"
                required
            >
        </div>


        <div>
            <label>Role</label>

            <select name="role">

                <option value="staff">
                    Staff
                </option>

                <option value="admin">
                    Admin
                </option>

            </select>
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
                Simpan
            </button>

        </div>

    </form>

</div>


<div class="card">

    <table>

        <tr>
            <th>NO</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Role</th>
            <th>Aksi</th>
        </tr>


        <?php foreach($rows as $i=>$r): ?>

            <tr>

                <td>
                    <?=$i+1?>
                </td>

                <td>
                    <?=e($r['name'])?>
                </td>

                <td>
                    <?=e($r['username'])?>
                </td>

                <td>
                    <?=e(ucfirst($r['role']))?>
                </td>

                <td>

                    <?php if($r['id'] != $_SESSION['user']['id']): ?>

                        <a
                            class="btn red"
                            href="?delete=<?=$r['id']?>"
                            onclick="return confirm('Hapus?')"
                        >
                            Hapus
                        </a>

                    <?php else: ?>

                        Akun aktif

                    <?php endif; ?>

                </td>

            </tr>

        <?php endforeach; ?>

    </table>

</div>


<?php

require __DIR__.'/../../views/footer.php';

?>