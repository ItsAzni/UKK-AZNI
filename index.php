<?php

require 'functions.php';

requireLogin();

$items = $koneksi->query('SELECT * from tbl_barang')->num_rows;
$users = $koneksi->query('SELECT * FROM tbl_user')->num_rows;

?>

<!DOCTYPE html>
<html lang="en" data-theme="dracula">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    <title>Inventaris Barang | Dashboard</title>
</head>

<body>
    <nav class="navbar bg-base-100 shadow-sm">
        <div class="max-w-7xl w-full flex mx-auto">
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
    <div class="max-w-7xl mx-auto container mt-8 p-4">
        <h1 class="text-center font-bold text-2xl mb-6">Dashboard</h1>

        <?php if (isset($_SESSION['nama_lengkap'])): ?>
            <div class="text-center mb-6">
                <h2 class="text-xl">Welcome, <?= htmlspecialchars($_SESSION['nama_lengkap']); ?>!</h2>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-base-200 border-base-300 rounded-box p-4 border-l-5 border-blue-500">
                <h3 class="text-lg font-semibold">Total Barang</h3>
                <p class="text-xl"><?= $items ?></p>
            </div>
            <div class="bg-base-200 border-base-300 rounded-box p-4 border-l-5 border-green-500">
                <h3 class="text-lg font-semibold">Total User Terdaftar</h3>
                <p class="text-xl"><?= $users ?></p>
            </div>
        </div>
    </div>
</body>

</html>