<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['admin', 'admin_mitra'])) {
    echo "Akses ditolak.";
    exit;
}

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan.";
    exit;
}

$data_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Load items grouped by category
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
        'quota' => (int) $row['item_quota']
    ];
}

// Load existing penyewaan data
$penyewaan = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM penyewaan JOIN items ON items.id = penyewaan.item_id WHERE penyewaan.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $penyewaan = $stmt->get_result()->fetch_assoc();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $user_id = $_SESSION['user_id'];
    $tanggal = $_POST['tanggal'];
    $item_id = $_POST['item_id'];
    $updated_at = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("UPDATE penyewaan SET user_id = ?, tanggal = ?, item_id = ?, updated_at = ? WHERE id = ?");
    $stmt->bind_param("isisi", $user_id, $tanggal, $item_id, $updated_at, $id);

    if ($stmt->execute()) {
        header("Location: admin-penyewaan.php?updated=1");
        exit;
    } else {
        echo "Gagal update data: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Penyewaan</title>
</head>

<body>
    <h2>Edit Data Penyewaan</h2>
    <form method="post">
        <input type="hidden" name="id" value="<?= $data_id ?? '' ?>">

        <div class="form-control">
            <label for="tanggal">Tanggal:</label>
            <input type="date" name="tanggal" required value="<?= $penyewaan['tanggal'] ?? '' ?>">
        </div>

        <div class="form-control">
            <label for="kategori">Pilih Kategori:</label>
            <select id="kategori" name="kategori" onchange="updateItems()" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($items as $kategori => $list): ?>
                    <option value="<?= $kategori ?>" <?= ($kategori == $penyewaan['item_category']) ? 'selected' : '' ?>>
                        <?= ucfirst($kategori) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-control">
            <label for="item_id">Pilih Item:</label>
            <select id="item_id" name="item_id" required>
                <option value="">-- Pilih Item --</option>
                <?php
                $selectedCategory = $penyewaan['item_category'] ?? '';
                if ($selectedCategory && isset($items[$selectedCategory])) {
                    foreach ($items[$selectedCategory] as $item) {
                        $selected = ($item['id'] == $penyewaan['item_id']) ? 'selected' : '';
                        echo "<option value='{$item['id']}' $selected>{$item['name']}</option>";
                    }
                }
                ?>
            </select>
        </div>

        <button type="submit">Update Penyewaan</button>
    </form>

    <script>
        const items = <?= json_encode($items) ?>;

        function updateItems() {
            const kategori = document.getElementById("kategori").value;
            const itemSelect = document.getElementById("item_id");

            itemSelect.innerHTML = '<option value="">-- Pilih Item --</option>';
            if (items[kategori]) {
                items[kategori].forEach(item => {
                    let option = document.createElement("option");
                    option.value = item.id;
                    option.textContent = item.name;
                    itemSelect.appendChild(option);
                });
            }
        }
    </script>


</body>

</html>