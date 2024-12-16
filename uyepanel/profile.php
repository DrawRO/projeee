<?php
// Hata ayıklama modunu aç
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Veritabanı bağlantısını dahil et ve oturumu başlat
include 'db.php';
session_start();

// Kullanıcı giriş yapmamışsa giriş sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Kullanıcı ID'sini al
$user_id = $_SESSION['user_id'];

// Kullanıcı bilgilerini veritabanından çek
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="robots" content="noindex, nofollow">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <title>Üye Paneli</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h1>Üye Paneli</h1>
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">Hoşgeldiniz, <?php echo htmlspecialchars($user['username']); ?>!</h5>
            <p class="card-text"><strong>E-Posta:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p class="card-text"><strong>Bakiyeniz:</strong> $<?php echo number_format($user['balance'], 2); ?></p>
            <a href="logout.php" class="btn btn-danger">Çıkış Yap</a>
        </div>
    </div>
</div>
</body>
</html>
