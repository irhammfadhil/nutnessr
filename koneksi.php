<?php
$host = "localhost";
$user = "nutnessr";
$pass = "6g4390i1"; // atau sesuai dengan password MySQL Anda
$db   = "nutnessr"; // ganti dengan nama database Anda

$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
