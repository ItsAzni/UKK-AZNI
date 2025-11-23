<?php

require 'functions.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idBarang = $_POST['id_barang'];
    $idUser = $_SESSION['id_user'];
    $jenisTransaksi = $_POST['jenis_transaksi'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];

    $stmt = $koneksi->prepare('SELECT stok FROM tbl_barang WHERE id_barang = ?');
    $stmt->bind_param('i', $idBarang);
    $stmt->execute();
    $stmt->bind_result($stok);
    $stmt->fetch();
    $stmt->close();

    if ($jenisTransaksi == 'KELUAR' && $stok < $jumlah) {
        echo "<script>alert('Stok tidak cukup!');</script>";
    } else {
        $stmt = $koneksi->prepare('INSERT INTO tbl_transaksi (id_barang, id_user, jenis_transaksi, jumlah, keterangan) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('iisis', $idBarang, $idUser, $jenisTransaksi, $jumlah, $keterangan);
        $stmt->execute();
        $stmt->close();

        if ($jenisTransaksi == 'MASUK') {
            $stmt = $koneksi->prepare('UPDATE tbl_barang SET stok = stok + ? WHERE id_barang = ?');
            $stmt->bind_param('ii', $jumlah, $idBarang);
            $stmt->execute();
            $stmt->close();
        } elseif ($jenisTransaksi == 'KELUAR') {
            $stmt = $koneksi->prepare('UPDATE tbl_barang SET stok = stok - ? WHERE id_barang = ?');
            $stmt->bind_param('ii', $jumlah, $idBarang);
            $stmt->execute();
            $stmt->close();
        }

        header('Location: transaksi.php');
    }
}

$items = $koneksi->query('SELECT * from tbl_barang');
$transactions = $koneksi->query('SELECT t.*, b.nama_barang, u.nama_lengkap FROM tbl_transaksi t INNER JOIN tbl_barang b ON b.id_barang = t.id_barang INNER JOIN tbl_user u ON u.id_user = t.id_user ORDER BY tgl_transaksi DESC');

?>

<!DOCTYPE html>
<html lang="en" data-theme="dracula">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    <title>Inventaris Barang | Transaksi Barang</title>
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
        <h1 class="my-8 text-center font-bold text-2xl">Transaksi Barang</h1>
        <div class="mb-5 bg-base-200 border-base-300 rounded-box border p-4">
            <h3 class="text-xl font-semibold">Tambah Transaksi</h3>
            <form method="POST">
                <fieldset class="fieldset gap-2 flex w-full">
                    <div class="flex flex-col mb-4 w-full">
                        <label for="id_barang" class="label">Pilih Barang</label>
                        <select name="id_barang" id="id_barang" class="select">
                            <?php foreach ($items as $item): ?>
                                <option value="<?= $item['id_barang'] ?>"><?= $item['nama_barang'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="flex flex-col mb-4 w-full">
                        <label for="jenis_transaksi" class="label">Jenis Transaksi</label>
                        <select name="jenis_transaksi" id="jenis_transaksi" class="select">
                            <option value="MASUK">Masuk</option>
                            <option value="KELUAR">Keluar</option>
                        </select>
                    </div>

                    <div class="flex flex-col mb-4 w-full">
                        <label for="jumlah" class="label">Jumlah</label>
                        <input type="number" id="jumlah" name="jumlah" class="input" placeholder="Contoh: 10" required>
                    </div>

                    <div class="flex flex-col mb-4 w-full">
                        <label for="keterangan" class="label">Keterangan (Opsional)</label>
                        <textarea name="keterangan" id="keterangan" class="textarea" placeholder="Keterangan tambahan (opsional)"></textarea>
                    </div>

                    <button type="submit" class="btn btn-neutral mt-4">Simpan</button>
                </fieldset>
            </form>
        </div>

        <div class="bg-base-200 border-base-300 rounded-box border p-4">
            <h3 class="text-xl font-semibold mb-4">Riwayat Transaksi</h3>
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Petugas</th>
                        <th>Keterangan</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx) : ?>
                        <tr>
                            <td><?= $tx['nama_barang'] ?></td>
                            <td>
                                <div class="badge badge-soft <?= $tx['jenis_transaksi'] == "MASUK" ? 'badge-success' : 'badge-error' ?>"><?= $tx['jenis_transaksi'] ?></div>
                            </td>
                            <td><?= $tx['jumlah'] ?></td>
                            <td><?= $tx['nama_lengkap'] ?></td>
                            <td><?= $tx['keterangan'] ?></td>
                            <td><?= $tx['tgl_transaksi'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>