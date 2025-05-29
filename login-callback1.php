<?php
session_start();
require 'config.php';
require_once 'vendor/autoload.php';

// Koneksi database
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Setup Google Client
$client = new Google_Client();
$client->setClientId('1064289474152-ib4894m85ecj36u2tsrcork6qtlep8cb.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://sites.its.ac.id/inovasidigital/nutnessr/login-callback.php');
$client->addScope('email');
$client->addScope('profile');

// Validasi kode
if (!isset($_GET['code'])) {
    header("Location: login.php?error=invalid_google_code");
    exit;
}

// Ambil token dari kode
$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

// Tangani error dari Google
if (isset($token['error'])) {
    $error = htmlspecialchars($token['error_description'] ?? 'Login dengan Google gagal.');
    header("Location: login.php?error=" . urlencode($error));
    exit;
}

$client->setAccessToken($token['access_token']);

// Ambil info user dari Google
$oauth = new Google_Service_Oauth2($client);
$googleUser = $oauth->userinfo->get();

$email = $googleUser->email;
$name = $googleUser->name;

// Cek apakah email ada di database
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user) {
    // Email ditemukan, login berhasil
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] === 'admin') {
        header("Location: admin-home.php");
    } else {
        header("Location: home.php");
    }
    exit;
} else {
    // Email tidak ditemukan di DB — login ditolak
    $msg = "Akun email tidak terdaftar di aplikasi.";
    header("Location: login.php?error=" . urlencode($msg));
    exit;
}
?>
