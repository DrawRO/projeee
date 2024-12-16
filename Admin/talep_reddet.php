<?php
include '../db.php';

$id = $_GET['id'];

// Talebi failed olarak güncelleme işlemi
$stmt = $conn->prepare("UPDATE payment_requests SET status = 'failed', completed_at = NOW() WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo "Talep başarısız olarak güncellendi.";
} else {
    echo "Talep güncellenemedi.";
}
?>
