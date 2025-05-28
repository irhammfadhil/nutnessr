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

$stmt = $conn->prepare("DELETE FROM penyewaan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: admin-penyewaan.php?deleted=1");
exit;
