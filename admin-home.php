<?php
session_start();
require 'config.php';

// Redirect jika tidak login atau bukan admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle ubah password
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_pass = trim($_POST['old_password'] ?? '');
    $new_pass = trim($_POST['new_password'] ?? '');
    $confirm_pass = trim($_POST['confirm_password'] ?? '');

    if ($old_pass === '' || $new_pass === '' || $confirm_pass === '') {
        $msg = "Semua field harus diisi.";
    } elseif ($new_pass !== $confirm_pass) {
        $msg = "Password baru dan konfirmasi tidak cocok.";
    } else {
        $username = $_SESSION['username'];
        $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
        if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

        // Cek password lama
        $sql = "SELECT * FROM users WHERE username = '$username' AND password = md5('$old_pass')";
        $res = mysqli_query($conn, $sql);
        if ($res && mysqli_num_rows($res) > 0) {
            // Update password
            $new_md5 = md5($new_pass);
            $update = "UPDATE users SET password = '$new_md5' WHERE username = '$username'";
            if (mysqli_query($conn, $update)) {
                $msg = "Password berhasil diubah.";
            } else {
                $msg = "Gagal mengubah password.";
            }
        } else {
            $msg = "Password lama salah.";
        }
        mysqli_close($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Home - Nutnessr</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
<header>
    <h1>Dashboard Admin</h1>
    <nav>
        <a href="kelola-mitra.php">Kelola Mitra</a>
        <a href="kelola-riwayat.php">Kelola Riwayat</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<h2>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
<p>Gunakan menu di atas untuk mengelola mitra dan riwayat pengguna.</p>

<section>
    <h3>Ubah Password</h3>
    <?php if ($msg): ?>
        <mark><?= htmlspecialchars($msg) ?></mark>
    <?php endif; ?>
    <form method="POST" action="">
        <label>Password Lama</label>
        <input type="password" name="old_password" required>

        <label>Password Baru</label>
        <input type="password" name="new_password" required>

        <label>Password Baru Konfirmasi</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Ubah Password</button>
    </form>
</section>

</body>
</html>

