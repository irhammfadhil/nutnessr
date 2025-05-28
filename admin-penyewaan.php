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

// Dummy data item per kategori
$items = [
    'alat' => [
        ["id" => "raket_tenis", "name" => "Raket Tenis", "price" => "Rp 50.000", "quota" => 2],
        ["id" => "sepeda_gunung", "name" => "Sepeda Gunung", "price" => "Rp 80.000", "quota" => 3]
    ],
    'tempat olahraga' => [
        ["id" => "lap_futsal", "name" => "Lapangan Futsal", "price" => "Rp 200.000", "quota" => 1]
    ],
    'coaching' => [
        ["id" => "coach_basket", "name" => "Coaching Basket", "price" => "Rp 150.000", "quota" => 1]
    ],
    'bundling' => [
        ["id" => "paket_kombo", "name" => "Bundling Kombo", "price" => "Rp 250.000", "quota" => 1]
    ]
];

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $item_name = $_POST['item_name'];
    $price = $_POST['price'];
    $quota = $_POST['quota'];
    $tanggal = $_POST['tanggal'];
    $kategori = $_POST['kategori'];
    $user_id = $_SESSION['user_id'];
    $created_at = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("INSERT INTO penyewaan (user_id, tanggal, item_id, item_name, kategori, price, quota, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("isissiis", $user_id, $tanggal, $item_id, $item_name, $kategori, $price, $quota, $created_at);
    if ($stmt->execute()) {
        $message = "<p style='color:green'>✅ Data berhasil ditambahkan.</p>";
    } else {
        $message = "<p style='color:red'>❌ Gagal menyimpan data.</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Penyewaan</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <style>
        select, input[type="text"], input[type="number"] {
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
    <h2>Form Tambah Penyewaan (Admin/Admin Mitra)</h2>
    <?= $message ?>
    <form method="post">
        <div class="form-control">
            <label for="tanggal">Tanggal:</label>
            <input type="date" name="tanggal" required>
        </div>

        <div class="form-control">
            <label for="kategori">Pilih Kategori:</label>
            <select id="kategori" name="kategori" onchange="updateItems()" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($items as $kategori => $list): ?>
                    <option value="<?= $kategori ?>"><?= ucfirst($kategori) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-control">
            <label for="item_id">Pilih Item:</label>
            <select id="item_id" name="item_id" onchange="fillDetails()" required>
                <option value="">-- Pilih Item --</option>
            </select>
        </div>

        <div class="form-control">
            <label>Nama Item:</label>
            <input type="text" name="item_name" required><br>
        </div>

        <div class="form-control">
            <label>Harga:</label>
            <input type="number" name="price" required><br>
        </div>

        <div class="form-control">
            <label>Kuota:</label>
            <input type="number" name="quota" required><br>
        </div>

        <button type="submit">Tambah Penyewaan</button>
    </form>

    <hr>
    <h3>Daftar Data Penyewaan</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Item</th>
                <th>Tanggal</th>
                <th>Harga</th>
                <th>Kuota</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($conn) {
                $result = $conn->query("SELECT * FROM penyewaan ORDER BY tanggal ASC");
                if ($result) {
                    while ($row = $result->fetch_assoc()):   
            ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['user_id'] ?></td>
                        <td><?= $row['item_name'] ?> (<?= $row['item_id'] ?>)</td>
                        <td><?= $row['tanggal'] ?></td>
                        <td><?= $row['price'] ?></td>
                        <td><?= $row['quota'] ?></td>
                        <td>
                            <a href="edit-penyewaan.php?id=<?= $row['id'] ?>">Edit</a> |
                            <a href="delete-penyewaan.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                        </td>
                    </tr>
            <?php
                    endwhile;
                } else {
                    echo "<tr><td colspan='7'>Query gagal: " . $conn->error . "</td></tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Koneksi database tidak tersedia.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</article>

<script>
    const items = <?= json_encode($items) ?>;

    function updateItems() {
        const kategori = document.getElementById("kategori").value;
        const itemSelect = document.getElementById("item_id");
        itemSelect.innerHTML = "<option value=''>-- Pilih Item --</option>";
        if (items[kategori]) {
            items[kategori].forEach(item => {
                const option = document.createElement("option");
                option.value = item.id;
                option.textContent = item.name;
                option.setAttribute("data-name", item.name);
                option.setAttribute("data-price", item.price);
                option.setAttribute("data-quota", item.quota);
                itemSelect.appendChild(option);
            });
        }
    }

    function fillDetails() {
        const select = document.getElementById("item_id");
        const selected = select.options[select.selectedIndex];
        document.getElementById("item_name").value = selected.getAttribute("data-name") || '';
        document.getElementById("price").value = selected.getAttribute("data-price") || '';
        document.getElementById("quota").value = selected.getAttribute("data-quota") || '';
    }
</script>
</body>
</html>
<?php $conn->close(); ?>