<?php

require 'functions.php';

requireLogin();

$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if ($id && $action === 'hapus') {
    $koneksi->query("DELETE FROM tbl_barang WHERE id_barang = '$id'");
    header('Location: barang.php');
}

$data = null;
if ($id) {
    $data = $koneksi->query("SELECT * FROM tbl_barang WHERE id_barang = '$id'")->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kodeBarang = $_POST['kode_barang'];
    $namaBarang = $_POST['nama_barang'];
    $stok = $_POST['stok'];
    $satuan = $_POST['satuan'];

    if ($action == 'edit') {
        $stmt = $koneksi->prepare('UPDATE tbl_barang SET kode_barang = ?, nama_barang = ?, stok = ?, satuan = ? WHERE id_barang = ?');
        $stmt->bind_param('ssisi', $kodeBarang, $namaBarang, $stok, $satuan, $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action == 'tambah') {
        $stmt = $koneksi->prepare('INSERT INTO tbl_barang (kode_barang, nama_barang, stok, satuan) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssis', $kodeBarang, $namaBarang, $stok, $satuan);
        $stmt->execute();
        $stmt->close();
    }

    header('Location: barang.php');
}

$items = $koneksi->query('SELECT * from tbl_barang');

?>

<!DOCTYPE html>
<html lang="en" data-theme="dracula">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    <title>Inventaris Barang | Data Barang</title>
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
        <h1 class="my-8 text-center font-bold text-2xl">Data Barang</h1>
        <div class="mb-5 bg-base-200 border-base-300 rounded-box border p-4">
            <h3 class="text-xl font-semibold"><?= ucfirst($action ?? 'tambah') ?> Barang</h3>
            <form action="barang.php?action=<?= $action ?? 'tambah' ?><?= $action === 'edit' ? '&id=' . $data['id_barang'] : '' ?>" method="POST">
                <fieldset class="fieldset flex gap-2 w-full">
                    <div class="flex flex-col w-full mb-4">
                        <label for="kode_barang" class="label">Kode Barang</label>
                        <input class="input" type="text" id="kode_barang" name="kode_barang" placeholder="Contoh: BRG-HUS1" value="<?= $data['kode_barang'] ?? '' ?>" required>
                    </div>

                    <div class="flex flex-col w-full mb-4">
                        <label for="nama_barang" class="label">Nama Barang</label>
                        <input class="input" type="text" id="nama_barang" name="nama_barang" placeholder="Contoh: Kursi" value="<?= $data['nama_barang'] ?? '' ?>" required>
                    </div>

                    <div class="flex flex-col w-full mb-4">
                        <label for="stok" class="label">Stok Barang</label>
                        <input class="input" type="number" id="stok" name="stok" placeholder="Contoh: 10" value="<?= $data['stok'] ?? '' ?>" required>
                    </div>

                    <div class="flex flex-col w-full mb-4">
                        <label for="satuan" class="label">Satuan</label>
                        <input class="input" type="text" id="satuan" name="satuan" placeholder="Contoh: Unit, Box" value="<?= $data['satuan'] ?? '' ?>" required>
                    </div>

                    <button type="submit" class="btn btn-neutral mt-4">Simpan</button>
                </fieldset>
            </form>


        </div>
        <div class="bg-base-200 border-base-300 rounded-box border p-4">
            <h3 class="text-xl font-semibold">Daftar Barang</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Stok Saat Ini</th>
                        <th>Satuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $index => $item) : ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $item['kode_barang'] ?></td>
                            <td><?= $item['nama_barang'] ?></td>
                            <td><?= $item['stok'] ?></td>
                            <td><?= $item['satuan'] ?></td>
                            <td>
                                <a href="barang.php?id=<?= $item['id_barang'] ?>&action=edit">Edit</a>
                                <a href="barang.php?id=<?= $item['id_barang'] ?>&action=hapus" onclick="return confirm('Apakah kamu yakin ingin menghapus barang <?= $item['nama_barang'] ?>?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>