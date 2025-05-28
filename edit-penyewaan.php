<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['admin', 'admin_mitra'])) {
    echo "Akses ditolak.";
    exit;
}

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan.";
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM penyewaan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['item_name'];
    $price = $_POST['price'];
    $quota = $_POST['quota'];
    $tanggal = $_POST['tanggal'];

    $stmt = $conn->prepare("UPDATE penyewaan SET item_name = ?, price = ?, quota = ?, tanggal = ? WHERE id = ?");
    $stmt->bind_param("ssisi", $item_name, $price, $quota, $tanggal, $id);
    if ($stmt->execute()) {
        header("Location: admin-penyewaan.php?updated=1");
        exit;
    } else {
        echo "Gagal update data.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Penyewaan</title>
</head>
<body>
    <h2>Edit Data Penyewaan</h2>
    <form method="post">
        <label>Item:</label><br>
        <input type="text" name="item_name" value="<?= $data['item_name'] ?>" required><br><br>

        <label>Harga:</label><br>
        <input type="text" name="price" value="<?= $data['price'] ?>" required><br><br>

        <label>Kuota:</label><br>
        <input type="number" name="quota" value="<?= $data['quota'] ?>" required><br><br>

        <label>Tanggal:</label><br>
        <input type="date" name="tanggal" value="<?= $data['tanggal'] ?>" required><br><br>

        <button type="submit">Update</button>
        <a href="admin-penyewaan.php">Batal</a>
    </form>
</body>
</html>
