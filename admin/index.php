<?php
session_start();
include '../db.php';

// Hata ayıklama modunu aç
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Admin oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php'); // Admin giriş sayfasına yönlendir
    exit;
}

// Toplam kullanıcı sayısı
$userCountStmt = $conn->query("SELECT COUNT(*) FROM users");
$totalUsers = $userCountStmt->fetchColumn();

// Mevcut ürün sayısı
$productCountStmt = $conn->query("SELECT COUNT(*) FROM products");
$totalProducts = $productCountStmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <title>Yönetim Paneli</title>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .admin-content {
            margin-left: 240px;
            padding: 20px;
        }
        .admin-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            background-color: #fff;
            transition: box-shadow 0.3s;
        }
        .admin-card:hover {
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
        }
        .admin-card h4 {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<!-- Ortak Yan Menü Dahil Edilir -->
<?php include 'sidebar.php'; ?>

<div class="admin-content">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Yönetim Paneli</a>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Yönetim Paneli</h1>
        <div class="row">
            <!-- Üye Sayısı Kartı -->
            <div class="col-md-6">
                <div class="admin-card">
                    <h4>Toplam Üye Sayısı</h4>
                    <p class="display-4 text-primary"><?php echo $totalUsers; ?></p>
                </div>
            </div>

            <!-- Mevcut Ürün Sayısı Kartı -->
            <div class="col-md-6">
                <div class="admin-card">
                    <h4>Mevcut Ürün Sayısı</h4>
                    <p class="display-4 text-info"><?php echo $totalProducts; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
