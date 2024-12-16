<?php
include 'db.php'; // Veritabanı bağlantı dosyanızı dahil edin

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Ürün detayları için SQL sorgusu, product_type kontrolü çıkarıldı
    $stmt = $conn->prepare("
        SELECT p.id, p.name, p.description
        FROM products p
        JOIN orders o ON o.product_id = p.id
        WHERE o.id = ?
    ");
    $stmt->execute([$order_id]);
    $details = $stmt->fetch(PDO::FETCH_ASSOC);

    // Detayların HTML olarak döndürülmesi
    if ($details) {
        echo "<p>Ürün Adı: " . htmlspecialchars($details['name']) . "</p>";
        echo "<p>Açıklama: " . htmlspecialchars($details['description']) . "</p>";
    } else {
        echo "Bu sipariş ID'sine ait ürün bulunamadı.";
    }
} else {
    echo "Sipariş ID'si belirtilmemiş.";
}
?>
