<?php
include 'auth.php';
include 'db.php';

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if ($order_id) {
    // Detayları `custom_product_details` tablosundan alıyoruz
    $stmt = $conn->prepare("SELECT product_details, detail_created_at FROM custom_product_details WHERE request_id = ?");
    $stmt->execute([$order_id]);
    $details = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($details) {
        echo "<p><strong>Detaylar:</strong> " . htmlspecialchars($details['product_details']) . "</p>";
        echo "<p><strong>Detay Tarihi:</strong> " . htmlspecialchars($details['detail_created_at']) . "</p>";
    } else {
        echo "<p>Detay bilgisi yok.</p>";
    }
} else {
    echo "<p>Geçersiz talep.</p>";
}
?>
