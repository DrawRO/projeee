<?php
session_start();
include 'db.php';

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Kullanıcı ID'sini ve ürün ID'sini doğrula
$user_id = $_SESSION['user_id'];
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id == 0) {
    die("Geçersiz ürün.");
}

// Ürün bilgilerini veritabanından çek
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    die("Geçersiz ürün.");
}

// Kullanıcının bakiyesi yeterli mi?
$stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_balance = $stmt->fetchColumn();

if ($user_balance >= $product['price']) {
    // Kullanıcının bakiyesini güncelle
    $new_balance = $user_balance - $product['price'];
    $stmt = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
    $stmt->execute([$new_balance, $user_id]);

    // Siparişi oluştur
    $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, total, order_status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$user_id, $product_id, 1, $product['price'], 'completed']);

    $success_message = "Satın alma başarılı! Kalan bakiye: $" . number_format($new_balance, 2);
} else {
    $error_message = "Yetersiz bakiye.";
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <title>Ürün Satın Al</title>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .btn-back {
            background-color: #0d6efd;
            color: white;
            border-radius: 5px;
            padding: 10px 15px;
            text-decoration: none;
            transition: background-color 0.3s, box-shadow 0.3s;
        }
        .btn-back:hover {
            background-color: #0a58ca;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
    </style>
<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
    <h2 class="text-center">Ürün Satın Al</h2>
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="index.php" class="btn-back">Anasayfaya Dön</a>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
