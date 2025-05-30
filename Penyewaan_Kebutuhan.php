<?php
require 'koneksi.php';
session_start();

$userId = $_SESSION['user_id'];

$categories = [];
$sql = "SELECT DISTINCT item_category FROM items";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['item_category'];
    }
}

$sql = "SELECT * FROM items";
$result = $conn->query($sql);

// Fetch all items first to reuse them for each date
$items = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $price = (int) $row['item_price'];
        $durasi = [
            1 => $price * 1,
            2 => $price * 2,
            3 => $price * 3
        ];

        $items[] = [
            "id" => $row['id'],
            "name" => $row['item_name'],
            "price" => $price,
            "img" => 'https://via.placeholder.com/80',
            "durasi" => $durasi
        ];
    }
}

$rentals = [];

// Loop for next 7 days
for ($i = 0; $i < 7; $i++) {
    // Get the date string for day $i
    $dateKey = date('Y-m-d', strtotime("+$i days"));

    // Assign all items to that date
    $rentals[$dateKey] = $items;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Penyewaan Kebutuhan Olahraga</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        .location {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .date-scroll-wrapper {
            display: flex;
            align-items: center;
        }

        .scroll-btn {
            cursor: pointer;
            padding: 10px;
            background: orange;
            border: none;
            color: white;
            border-radius: 5px;
            margin: 0 5px;
        }

        .date-scroll {
            display: flex;
            overflow-x: auto;
            padding: 10px 0;
            white-space: nowrap;
        }

        .date-item {
            flex: 0 0 auto;
            margin-right: 10px;
            padding: 10px;
            background: lightgray;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
        }

        .active {
            background: orange;
            color: white;
        }

        .categories {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .category {
            padding: 10px;
            background: lightgray;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            flex: 1;
            margin: 0 5px;
        }

        .selected {
            background: orange;
            color: white;
        }

        .rental-list {
            margin-top: 20px;
        }

        .rental-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .rental-item img {
            width: 80px;
            height: 80px;
            margin-right: 10px;
            border-radius: 5px;
        }

        .confirm-box {
            display: none;
            padding: 10px;
            border: 2px solid #000;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <p class="location">Lokasi: <span id="user-location">Mendeteksi...</span></p>
        <div class="date-scroll-wrapper">
            <button class="scroll-btn" onclick="scrollDates(-1)">&#9664;</button>
            <div class="date-scroll" id="date-scroll"></div>
            <button class="scroll-btn" onclick="scrollDates(1)">&#9654;</button>
        </div>
        <div class="categories">
            <?php foreach ($categories as $category): ?>
                <div class="category" onclick="selectCategory(this)">
                    <?= ucwords(str_replace('_', ' ', $category)) ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="rental-list" id="rental-list"></div>

        <div class="confirm-box" id="confirm-box">
            <p>Konfirmasi penyewaan:</p>
            <p id="confirm-text"></p>
            <label for="durasi">Durasi (hari):</label>
            <select id="durasi" onchange="updatePrice(selectedRental.prices)">
                <option value="1">1 hari</option>
                <option value="2">2 hari</option>
                <option value="3">3 hari</option>
            </select>
            <p id="price-text"></p>
            <button onclick="confirmRental()">Konfirmasi</button>
            <button onclick="cancelRental()">Batal</button>
        </div>
    </div>

    <script>
        const rentals = <?php echo json_encode($rentals); ?>;
        let selectedRental = {};

        function scrollDates(direction) {
            document.getElementById("date-scroll").scrollBy({ left: direction * 100, behavior: 'smooth' });
        }

        function selectCategory(element) {
            document.querySelectorAll('.category').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');
        }

        const dateScroll = document.getElementById("date-scroll");
        const today = new Date();
        for (let i = 0; i < 7; i++) {
            let date = new Date();
            date.setDate(today.getDate() + i);
            let dateStr = date.toISOString().split('T')[0];
            let btn = document.createElement("div");
            btn.className = "date-item";
            btn.innerText = date.toDateString().slice(0, 10);
            btn.onclick = () => loadRentals(dateStr, btn);
            dateScroll.appendChild(btn);
            if (i === 0) btn.classList.add("active");
        }

        function getSelectedDate() {
            const activeDateElem = document.querySelector('.date-item.active');
            if (!activeDateElem) return null;
            return activeDateElem.getAttribute('data-date');
        }

        function loadRentals(date, btn) {
            document.querySelectorAll('.date-item').forEach(el => el.classList.remove("active"));
            btn.classList.add("active");
            let rentalList = document.getElementById("rental-list");
            rentalList.innerHTML = "";
            if (rentals[date]) {
                rentals[date].forEach(rental => {
                    let div = document.createElement("div");
                    div.className = "rental-item";
                    div.innerHTML = `<img src="${rental.img}" alt="${rental.name}"><div><b>${rental.name}</b> - Rp ${rental.price.toLocaleString()}</div>`;

                    // Add rental ID as a data attribute (optional but useful)
                    div.dataset.rentalId = rental.id;

                    // Pass the id along with other data to confirmBooking
                    div.onclick = () => {
                        console.log('ID:', rental.id);
                        console.log('Name:', rental.name);
                        console.log('Price:', rental.price);
                        console.log('Date:', date);
                        confirmBooking(rental.id, rental.name, rental.price, date);
                    };


                    rentalList.appendChild(div);
                });

            } else {
                rentalList.innerHTML = "<p>Tidak ada penyewaan tersedia.</p>";
            }
        }

        function confirmBooking(id, name, durasiObj, date) {
            const userId = <?= json_encode($userId); ?>;
            selectedRental = {
                id: id,
                user_id: userId,
                name: name,
                date: date,
                durasi: 1,
                prices: durasiObj
            };
            document.getElementById("confirm-text").innerText = `${name} pada ${date}`;
            document.getElementById("durasi").value = "1";
            updatePrice(selectedRental.prices);
            document.getElementById("confirm-box").style.display = "block";
        }

        function updatePrice(prices) {
            const durasi = document.getElementById("durasi").value;
            selectedRental.durasi = parseInt(durasi);
            //const harga = selectedRental.prices[durasi];
            pricesPerItem = prices;
            document.getElementById("price-text").innerText = `Harga total: Rp ` + prices;
            selectedRental.prices = selectedRental.price * selectedRental.durasi;
        }

        function confirmRental() {
            // Prepare data to send
            const dataToSend = {
                user_id: selectedRental.user_id,
                tanggal: selectedRental.date,
                durasi: selectedRental.durasi,
                item_id: selectedRental.id
            };

            fetch('save_rental.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'  // sending JSON
                },
                body: JSON.stringify(dataToSend)
            })
                .then(response => response.json())
                .then(result => {
                    console.log('Server response:', result);
                    if (result.success) {
                        alert(`Penyewaan ${selectedRental.name} selama ${selectedRental.durasi} hari berhasil disimpan!`);
                    } else {
                        alert('Gagal menyimpan data penyewaan.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan data.');
                });

            document.getElementById("confirm-box").style.display = "none";
        }

        function cancelRental() {
            document.getElementById("confirm-box").style.display = "none";
        }

        loadRentals(Object.keys(rentals)[0], document.querySelector(".date-item"));
    </script>

    <script>
        async function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(async (position) => {
                    try {
                        let lat = position.coords.latitude;
                        let lon = position.coords.longitude;
                        let response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
                        let data = await response.json();
                        let location = data.address.city || data.address.town || data.address.village || data.address.state || "Lokasi tidak ditemukan";
                        document.getElementById("user-location").innerText = location;
                    } catch (error) {
                        document.getElementById("user-location").innerText = "Gagal mendeteksi lokasi";
                    }
                }, () => {
                    document.getElementById("user-location").innerText = "Lokasi tidak dapat diakses";
                });
            } else {
                document.getElementById("user-location").innerText = "Geolocation tidak didukung di perangkat ini";
            }
        }

        getLocation();
    </script>
</body>

</html>