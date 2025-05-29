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

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];

// Dummy data item per kategori
$items = ['A', 'B', 'C'];

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed.");
    }
    $item_name = $_POST['item_name'];
    $price = $_POST['price'];
    $quota = $_POST['quota'];
    $kategori = $_POST['kategori'];
    $user_id = $_SESSION['user_id'];
    $created_at = date("Y-m-d H:i:s");
    $updated_at = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("INSERT INTO items (user_id, item_name, item_category, item_price, item_quota, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ississs", $user_id, $item_name, $kategori, $price, $quota, $created_at, $updated_at);
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
    <title>Manajemen Peralatan</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
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
        <h2>Form Tambah Peralatan (Admin/Admin Mitra)</h2>
        <?= $message ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div class="form-control">
                <label for="kategori">Pilih Kategori:</label>
                <select id="kategori" name="kategori" onchange="updateItems()" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($items as $kategori): ?>
                        <option value="<?= $kategori ?>"><?= ucfirst($kategori) ?></option>
                    <?php endforeach; ?>
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

            <button type="submit">Tambah Item</button>
        </form>

        <hr>
        <h3>Daftar Data Peralatan</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kategori</th>
                    <th>Item</th>
                    <th>Harga</th>
                    <th>Kuota</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($conn) {
                    $result = $conn->query("SELECT * FROM items where deleted_at is null");
                    if ($result) {
                        while ($row = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['item_category'] ?></td>
                                <td><?= $row['item_name'] ?></td>
                                <td><?= $row['item_price'] ?></td>
                                <td><?= $row['item_quota'] ?></td>
                                <td>
                                    <a href="edit_alat.php?id=<?= $row['id'] ?>">Edit</a> |
                                    <a href="delete_alat.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
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