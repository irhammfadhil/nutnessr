<?php
session_start();
require 'config.php';

// Cek login & role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

$msg = '';

// Tambah mitra
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_mitra'])) {
    $id = intval($_POST['id_mitra']);
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_mitra']));

    // Cek ID atau nama mitra sudah ada
    $cek = mysqli_query($conn, "SELECT * FROM mitra WHERE id_mitra = $id OR nama_mitra = '$nama'");
    if (mysqli_num_rows($cek) > 0) {
        $msg = "ID atau Nama Mitra sudah digunakan.";
    } else {
        $sql = "INSERT INTO mitra (id_mitra, nama_mitra) VALUES ($id, '$nama')";
        if (mysqli_query($conn, $sql)) {
            $msg = "Mitra berhasil ditambahkan.";
        } else {
            $msg = "Gagal menambahkan mitra: " . mysqli_error($conn);
        }
    }
}

// Hapus mitra
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM mitra WHERE id_mitra = $id");
    header("Location: kelola-mitra.php");
    exit();
}

// Ambil daftar mitra
$mitra = mysqli_query($conn, "SELECT * FROM mitra ORDER BY id_mitra ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Mitra</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
<header>
    <h1>Kelola Mitra</h1>
    <nav>
        <a href="admin-home.php">Kembali</a>
    </nav>
</header>

<?php if ($msg): ?>
    <p><mark><?= htmlspecialchars($msg) ?></mark></p>
<?php endif; ?>

<h2>Daftar Mitra</h2>
<table>
    <tr>
        <th>ID Mitra</th>
        <th>Nama Mitra</th>
        <th>Aksi</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($mitra)): ?>
        <tr>
            <td><?= $row['id_mitra'] ?></td>
            <td><?= htmlspecialchars($row['nama_mitra']) ?></td>
            <td>
                <a href="profil-mitra.php?id=<?= $row['id_mitra'] ?>"><button>Edit</button></a>
                <a href="kelola-mitra.php?hapus=<?= $row['id_mitra'] ?>" onclick="return confirm('Yakin ingin menghapus mitra ini?')">
                    <button>Hapus</button>
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<h3>Tambah Mitra</h3>
<form method="POST">
    <label for="id_mitra">ID Mitra</label>
    <input type="number" name="id_mitra" required>

    <label for="nama_mitra">Nama Mitra</label>
    <input type="text" name="nama_mitra" required>

    <button type="submit" name="add_mitra">Tambah Mitra</button>
</form>

</body>
</html>
