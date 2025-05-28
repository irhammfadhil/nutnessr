<?php 
session_start();

session_destroy();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - Nutnessr</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <article>
        <h3>Anda telah logout</h3>
        <p>Terima kasih telah menggunakan Nutnessr.</p>
        <a href="login.php">Kembali ke Login</a>
    </article>
</body>
</html>

