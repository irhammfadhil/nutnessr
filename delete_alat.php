<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    echo "Akses ditolak.";
    exit;
}

$role = $_SESSION['role'];

if ($role !== 'admin' && $role !== 'admin_mitra') {
    echo "Akses ditolak.";
    exit;
}

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan.";
    exit;
}

$id = $_GET['id'];

$stmt = $conn->prepare("UPDATE items SET deleted_at = NOW() WHERE id = ?"); // setting deleted_at column
$stmt->execute([$id]);

header("Location: manajemen_alat.php");
exit;

?>