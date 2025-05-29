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


?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Mitra</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        select,
        input[type="text"],
        input[type="number"] {
            width: 100%;
        }

        table {
            width: 100%;
        }

        .form-control {
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <article>
        <h2>Dashboard Mitra</h2>
        <div class="row">
            <div class="col-lg-6 mb-4">
                <a href="admin-penyewaan.php" class="text-decoration-none">
                    <div class="border rounded p-4 text-center shadow-sm h-100">
                        <h3 class="text-primary">Manajemen Penyewaan</h3>
                    </div>
                </a>
            </div>
            <div class="col-lg-6 mb-4">
                <a href="manajemen_alat.php" class="text-decoration-none">
                    <div class="border rounded p-4 text-center shadow-sm h-100">
                        <h3 class="text-success">Manajemen Alat Olahraga</h3>
                    </div>
                </a>
            </div>
        </div>
    </article>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
    <script>

    </script>
</body>

</html>
<?php $conn->close(); ?>