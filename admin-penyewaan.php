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

$items = [];

$sql = "SELECT id, item_name, item_price, item_quota, item_category FROM items";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $category = $row['item_category'];

    if (!isset($items[$category])) {
        $items[$category] = [];
    }

    $items[$category][] = [
        'id' => $row['id'],
        'name' => $row['item_name'],
        'price' => $row['item_price'],
        'quota' => (int)$row['item_quota']
    ];
}


$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $tanggal = $_POST['tanggal'];
    $user_id = $_SESSION['user_id'];
    $created_at = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("INSERT INTO penyewaan (user_id, tanggal, item_id, created_at) VALUES (?, ?, ?, ?)");

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("isis", $user_id, $tanggal, $item_id, $created_at);
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
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($conn) {
                $result = $conn->query("SELECT p.id as id_penyewaan, p.user_id as user_id, i.item_name as item_name, p.item_id as item_id, p.tanggal as tanggal, i.item_price as price
                 FROM penyewaan as p join items as i on i.id = p.item_id ORDER BY p.tanggal ASC");
                if ($result) {
                    while ($row = $result->fetch_assoc()):   
            ?>
                    <tr>
                        <td><?= $row['id_penyewaan'] ?></td>
                        <td><?= $row['user_id'] ?></td>
                        <td><?= $row['item_name'] ?> (<?= $row['item_id'] ?>)</td>
                        <td><?= $row['tanggal'] ?></td>
                        <td><?= $row['price'] ?></td>
                        <td>
                            <a href="edit-penyewaan.php?id=<?= $row['id_penyewaan'] ?>">Edit</a> |
                            <a href="delete-penyewaan.php?id=<?= $row['id_penyewaan'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
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