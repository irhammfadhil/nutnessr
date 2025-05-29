<?php
session_start();
require 'koneksi.php';

// Sanitize and get the ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    die("Invalid ID");
}

$sql = $conn->prepare("SELECT * FROM items WHERE id = ?");
$sql->bind_param("i", $id);
$sql->execute();
$result = $sql->get_result();
$data = $result->fetch_assoc();



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
    $item_id = $_POST['id'];
    $item_name = $_POST['item_name'];
    $price = $_POST['price'];
    $quota = $_POST['quota'];
    $kategori = $_POST['kategori'];
    $user_id = $_SESSION['user_id'];
    $updated_at = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("UPDATE items 
    SET user_id = ?, item_name = ?, item_category = ?, item_price = ?, item_quota = ?, updated_at = ?
    WHERE id = ?");

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ississi", $user_id, $item_name, $kategori, $price, $quota, $updated_at, $item_id);

    if ($stmt->execute()) {
        header("Location: manajemen_alat.php");
        exit;
    } else {
        $message = "<p style='color:red'>❌ Gagal memperbarui data.</p>";
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
        <h2>Edit Peralatan (Admin/Admin Mitra)</h2>
        <?= $message ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="id" value="<?= $data['id'] ?>">
            <div class="form-control">
                <label for="kategori">Pilih Kategori:</label>
                <select id="kategori" name="kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($items as $kategori): ?>
                        <option value="<?= $kategori ?>" <?= $data['item_category'] === $kategori ? 'selected' : '' ?>>
                            <?= ucfirst($kategori) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-control">
                <label>Nama Item:</label>
                <input type="text" name="item_name" value="<?= htmlspecialchars($data['item_name']) ?>" required>
            </div>

            <div class="form-control">
                <label>Harga:</label>
                <input type="number" name="price" value="<?= $data['item_price'] ?>" required>
            </div>

            <div class="form-control">
                <label>Kuota:</label>
                <input type="number" name="quota" value="<?= $data['item_quota'] ?>" required>
            </div>

            <button type="submit">Submit</button>
        </form>

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