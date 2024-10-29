<?php
// Hata ayıklama modunu aç
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';
session_start();

// Kullanıcı oturumu kontrol et
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Kullanıcı ID'sini al ve ürün ID'sini doğrula
$user_id = $_SESSION['user_id'];
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ürün bilgilerini veritabanından çek
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if ($product) {
    // Kullanıcının bakiyesi yeterli mi?
    $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_balance = $stmt->fetchColumn();

    if ($user_balance >= $product['price']) {
        // Kullanıcının bakiyesini güncelle
        $new_balance = $user_balance - $product['price'];
        $stmt = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $stmt->execute([$new_balance, $user_id]);

        echo "Satın alma başarılı! Kalan bakiye: $" . number_format($new_balance, 2);
    } else {
        echo "Yetersiz bakiye.";
    }
} else {
    echo "Geçersiz ürün.";
}
?>
