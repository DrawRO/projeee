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
<html lang="tr">
<head>
    <meta name="robots" content="noindex, nofollow">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Üye Paneli</title>
    <style>
        body {
            background-image: url('https://r.resimlink.com/M6jwq.jpg');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            color: #333;
        }
        .sidebar {
            background-color: rgba(0, 123, 255, 0.8);
            min-height: 100vh;
            padding: 20px;
            color: white;
            position: fixed;
            width: 250px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .sidebar a:hover {
            background-color: #0056b3;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background-color: rgba(255, 255, 255, 0.9);
        }

        /* Mobil uyumluluk için medya sorguları */
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
            }
            .content {
                margin-left: 0;
                padding: 15px;
            }
            .card {
                margin: 0; /* Kartın dış boşluğunu kaldır */
            }
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-header">Üye Paneli</div>
    <a href="profile.php">Anasayfa</a>
    <a href="profile.php#update_profile">Profilinizi Güncelleyin</a>
    <a href="profile.php#change_password">Şifrenizi Değiştirin</a>
    <a href="orders.php">Sipariş Geçmişi</a>
    <a href="logout.php" class="btn btn-danger mt-3">Çıkış Yap</a>
</div>
<div class="content">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Üye Paneli</h1>
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title">Hoşgeldiniz, <?php echo htmlspecialchars($user['username']); ?>!</h5>
            </div>
            <div class="card-body">
                <p class="welcome-message"><strong>E-Posta:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p class="welcome-message"><strong>Bakiyeniz:</strong> $<?php echo number_format($user['balance'], 2); ?></p>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
