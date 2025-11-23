<?php

require 'functions.php';

requireLogin();

if (!isAdmin()) {
    echo "<script>return confirm('Anda tidak punya akses ke halaman ini!')</script>";
    header('Location: index.php');
    exit();
}

$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if ($id && $action === 'hapus') {
    $koneksi->query("DELETE FROM tbl_user WHERE id_user = '$id'");
    header('Location: user.php');
}

$data = null;
if ($id) {
    $data = $koneksi->query("SELECT * FROM tbl_user WHERE id_user = '$id'")->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaLengkap = $_POST['nama_lengkap'];
    $username = $_POST['username'];
    $password = $_POST['password'] == null ? null : password_hash($_POST['password'], PASSWORD_DEFAULT);
    $level = $_POST['level'];

    if ($action == 'edit') {
        if ($password) {
            $stmt = $koneksi->prepare('UPDATE tbl_user SET username = ?, nama_lengkap = ?, password = ?, level = ? WHERE id_user = ?');
            $stmt->bind_param('ssssi', $username, $namaLengkap, $password, $level, $id);
        } else {
            $stmt = $koneksi->prepare('UPDATE tbl_user SET username = ?, nama_lengkap = ?, level = ? WHERE id_user = ?');
            $stmt->bind_param('sssi', $username, $namaLengkap, $level, $id);
        }
        $stmt->execute();
        $stmt->close();
    } elseif ($action == 'tambah') {
        $user = $koneksi->query("SELECT * FROM tbl_user WHERE username = '$username'")->fetch_assoc();
        if ($user) {
            echo "<script>alert('Username sudah terdaftar!')</script>";
        } else {
            $stmt = $koneksi->prepare('INSERT INTO tbl_user (username, password, nama_lengkap, level) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssss', $username, $password, $namaLengkap, $level);
            $stmt->execute();
            $stmt->close();
        }
    }

    header('Location: user.php');
}

$users = $koneksi->query('SELECT * from tbl_user');

?>

<!DOCTYPE html>
<html lang="en" data-theme="dracula">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    <title>Inventaris Barang | Data User</title>
</head>

<body>
    <nav class="navbar bg-base-300 shadow-sm">
        <div class="max-w-7xl flex mx-auto container">
            <div class="flex-1">
                <a class="text-xl font-bold">Inventaris barang</a>
            </div>
            <div class="flex-none gap-4">
                <a class="btn btn-ghost" href="./">Dashboard</a>
                <a class="btn btn-ghost" href="barang.php">Data Barang</a>
                <a class="btn btn-ghost" href="transaksi.php">Transaksi Barang</a>
                <?php if (isAdmin()): ?>
                    <a class="btn btn-ghost" href="user.php">Manajemen User</a>
                <?php endif; ?>
                <a class="btn btn-error" href="logout.php" onclick="return confirm('Apakah anda yakin untuk logout?')">Logout</a>
            </div>
        </div>
    </nav>
    <div class="max-w-7xl mx-auto container">
        <h1 class="my-8 text-center font-bold text-2xl">Data User</h1>
        <div class="mb-5 bg-base-200 border-base-300 rounded-box border p-4">
            <h3 class="text-xl font-semibold"><?= ucfirst($action ?? 'tambah') ?> User</h3>
            <form action="user.php?action=<?= $action ?? 'tambah' ?><?= $action === 'edit' ? '&id=' . $data['id_user'] : '' ?>" method="POST">
                <fieldset class="fieldset gap-2 flex w-full">
                    <div class="flex flex-col w-full mb-4">
                        <label for="nama_lengkap" class="label">Nama Lengkap</label>
                        <input class="input" type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Contoh: Udin Mantap" value="<?= $data['nama_lengkap'] ?? '' ?>" required>
                    </div>

                    <div class="flex flex-col w-full mb-4">
                        <label for="username" class="label">Username</label>
                        <input class="input" type="text" id="username" name="username" placeholder="Contoh: udindin" value="<?= $data['username'] ?? '' ?>" required>
                    </div>

                    <div class="flex flex-col w-full mb-4">
                        <label for="password" class="label">Password</label>
                        <?php if (($action ?? 'tambah') == 'tambah'): ?>
                            <input class="input" type="password" id="password" name="password" placeholder="********" required>
                        <?php else: ?>
                            <input class="input" type="password" id="password" name="password" placeholder="********">
                        <?php endif; ?>
                    </div>

                    <div class="flex flex-col w-full mb-4">
                        <label for="level" class="label">Level Akun</label>
                        <select class="select" name="level" id="level">
                            <option value="petugas" <?= isset($data['level']) && $data['level'] == 'petugas' ? 'selected' : '' ?>>Petugas</option>
                            <option value="admin" <?= isset($data['level']) && $data['level'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-neutral mt-4">Simpan</button>
                </fieldset>
            </form>

        </div>
        <div class="bg-base-200 border-base-300 rounded-box border p-4">
            <h3 class="text-xl font-semibold">Daftar User</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Level</th>
                        <th>Aksi Akun</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $index => $user) : ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $user['nama_lengkap'] ?></td>
                            <td><?= $user['username'] ?></td>
                            <td>
                                <div class="badge badge-soft <?= $user['level'] == "admin" ? 'badge-info' : 'badge-error' ?>"><?= ucfirst($user['level']) ?></div>
                            </td>
                            <td>
                                <a class="btn btn-accent" href="user.php?id=<?= $user['id_user'] ?>&action=edit">Edit</a>
                                <?php if (!($_SESSION['id_user'] === $user['id_user'])): ?>
                                    <a class="btn btn-error" href="user.php?id=<?= $user['id_user'] ?>&action=hapus" onclick="return confirm('Apakah kamu yakin ingin menghapus user <?= $user['nama_lengkap'] ?>?')">Hapus</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>