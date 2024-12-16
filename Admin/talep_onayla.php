<?php
include '../db.php';

$id = $_GET['id'];

// Talebi onaylayıp kullanıcının bakiyesini güncelleme işlemi
$talep = $conn->query("SELECT * FROM payment_requests WHERE id = $id AND status = 'pending'")->fetch();

if ($talep) {
    $conn->query("UPDATE payment_requests SET status = 'completed', completed_at = NOW() WHERE id = $id");
    $conn->query("UPDATE users SET balance = balance + {$talep['amount']} WHERE id = {$talep['user_id']}");
    echo "Talep onaylandı ve bakiye yüklendi.";
} else {
    echo "Talep zaten işlenmiş veya bulunamadı.";
}
?>
