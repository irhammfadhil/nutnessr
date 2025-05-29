<?php
session_start();
require 'config.php';
require_once 'vendor/autoload.php';

// Koneksi database
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// --- LOGIN MANUAL ---
$status_login = null;
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username !== '' && $password !== '') {
        $username_esc = mysqli_real_escape_string($conn, $username);
        $password_esc = mysqli_real_escape_string($conn, $password);

        $sql = "SELECT * FROM users 
                WHERE username = '$username_esc' 
                AND password = md5('$password_esc')";

        $res = mysqli_query($conn, $sql);
        if (!$res) {
            die("Query error: " . mysqli_error($conn));
        }

        $row = mysqli_fetch_assoc($res);
        if ($row) {
            $_SESSION['user_id'] = $row['id']; 
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $status_login = true;

            if ($row['role'] === 'admin') {
                header("Location: admin-home.php");
            } elseif ($row['role'] === 'admin_mitra') {
                header("Location: dashboard_mitra.php");
            } else {
                header("Location: home.php");
            }
            exit();
        } else {
            $status_login = false;
            $error_msg = "Username atau password salah.";
        }
    } else {
        $status_login = false;
        $error_msg = "Silakan isi username dan password.";
    }
}

// --- LOGIN GOOGLE SETUP ---
$client = new Google_Client();
$client->setClientId('1064289474152-ib4894m85ecj36u2tsrcork6qtlep8cb.apps.googleusercontent.com'); 
$client->setClientSecret('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'); 
$client->setRedirectUri('https://sites.its.ac.id/inovasidigital/nutnessr/login-callback.php');
$client->addScope('email');
$client->addScope('profile');
$authUrl = $client->createAuthUrl();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nutnessr - Login</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <article>
        <h1>Login Nutnessr</h1>
        
        <?php if($status_login === false): ?>
            <mark><?= htmlspecialchars($error_msg) ?></mark>
        <?php endif; ?> 

        <!-- Form login manual -->
        <form action="login.php" method="POST">
            <p>
                <label>Username</label>
                <input type="text" name="username" required />
            </p>
            <p>
                <label>Password</label>
                <input type="password" name="password" required />
            </p>
            <p>
                <input type="submit" value="Login" />
            </p>
        </form>

        <p><a href="register.php">Belum punya akun? Daftar di sini</a></p>
        <p><a href="forgot_password.php">Lupa Password?</a></p>

        <hr>

        <!-- Tombol Login dengan Google -->
        <p>
            <a href="<?= htmlspecialchars($authUrl) ?>">
                <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png" 
                     alt="Login with Google">
            </a>
        </p>
    </article>
</body>
</html>

<?php mysqli_close($conn); ?>
