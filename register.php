<?php
require 'config.php';

// Koneksi database
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$success = false;
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if ($username !== '' && $email !== '' && $password !== '') {
        // Escape data
        $username_esc = mysqli_real_escape_string($conn, $username);
        $email_esc = mysqli_real_escape_string($conn, $email);
        $password_md5 = md5($password);
        $role_esc = mysqli_real_escape_string($conn, $role);
        $role = trim($_POST['role'] ?? '');
        $allowed_roles = ['user', 'admin', 'admin_mitra'];

        if (!in_array($role, $allowed_roles)) {
            $error_msg = "Role tidak valid.";
        }

        // Insert ke database
        $sql = "INSERT INTO users (username, email, password, role, created_at, updated_at) 
                VALUES ('$username_esc', '$email_esc', '$password_md5', '$role_esc', NOW(), NOW())";

        if (mysqli_query($conn, $sql)) {
            // ✅ Alert berhasil
            echo "<script>alert('Registrasi berhasil!'); window.location.href='login.php';</script>";
            exit();
        } else {
            $error_msg = "Gagal mendaftar: " . mysqli_error($conn);
        }
    } else {
        $error_msg = "Semua field harus diisi.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Akun Baru</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
<article>
    <h2>Form Registrasi Akun</h2>
    <?php if (isset($message) && $message !== ''): ?>
    	<p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form action="register.php" method="POST">
        <p>
            <label>Username</label>
            <input type="text" name="username" required />
        </p>
        <p>
            <label>Password</label>
            <input type="password" name="password" required />
       	</p>
	<p>
	    <label for="email">Email</label><br>
  	    <input type="email" name="email" required><br><br>
        </p>
	<p>
	    <label>Role:</label>
  	    <select name="role" required>
    	      <option value="user">User</option>
    	      <option value="admin">Admin</option>
              <option value="admin_mitra">Admin Mitra</option
	    </select><br>
	</p>
	<p>
            <input type="submit" value="Daftar" />
        </p>
    </form>
    <p><a href="login.php">Kembali ke Login</a></p>
</article>
</body>
</html>
