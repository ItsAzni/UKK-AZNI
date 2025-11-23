<?php

require 'functions.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = $koneksi->query("SELECT * FROM tbl_user WHERE username = '$username'")->fetch_assoc();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['level'] = $user['level'];
        $_SESSION['status_login'] = true;
        header('Location: index.php');
        exit();
    } else {
        $error = 'Username atau password salah!';
    }
}

?>

<!DOCTYPE html>
<html lang="en" data-theme="dracula">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    <title>Inventaris Barang | Login</title>
</head>

<body class="bg-base-200 flex items-center justify-center min-h-screen">
    <div class="max-w-sm w-full p-6 bg-base-100 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
        <?php if (isset($error)): ?>
            <p class="text-red-500 text-center mb-4"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-4">
                <label for="username" class="label">Username</label>
                <input type="text" id="username" name="username" class="input input-bordered w-full" required placeholder="Masukkan Username">
            </div>
            <div class="mb-6">
                <label for="password" class="label">Password</label>
                <input type="password" id="password" name="password" class="input input-bordered w-full" required placeholder="Masukkan Password">
            </div>
            <button type="submit" class="btn btn-neutral w-full">Login</button>
        </form>
    </div>
</body>

</html>