<?php
require 'koneksi.php';
session_start();

$userId = $_SESSION['user_id'];
header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$user_id = $data['user_id'] ?? null;
$tanggal = $data['tanggal'] ?? null;
$durasi = $data['durasi'] ?? null;
$item_id = $data['item_id'] ?? null;

// Validate inputs (basic check)
if (!$user_id || !$tanggal || !$durasi || !$item_id) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO penyewaan (user_id, tanggal, durasi, item_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isii", $user_id, $tanggal, $durasi, $item_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Data saved']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save data']);
}

$stmt->close();
$conn->close();