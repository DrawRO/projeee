<?php
session_start();
include '../db.php';

// Admin oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Toplam kullanıcı ve ürün sayısını al
$userCountStmt = $conn->query("SELECT COUNT(*) FROM users");
$totalUsers = $userCountStmt->fetchColumn();

$productCountStmt = $conn->query("SELECT COUNT(*) FROM products");
$totalProducts = $productCountStmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <title>Yönetim Paneli</title>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #2a2a40;
            color: #e4e4e7;
            padding-top: 20px;
            transition: all 0.3s;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.4);
        }
        .sidebar a {
            padding: 15px;
            text-decoration: none;
            font-size: 18px;
            color: #e4e4e7;
            display: flex;
            align-items: center;
            transition: background 0.3s, transform 0.2s;
        }
        .sidebar a i {
            margin-right: 10px;
            font-size: 18px;
        }
        .sidebar a:hover {
            background-color: #4a4a6a;
            color: white;
            transform: translateX(5px);
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
        }
    </style>
<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<!-- Yan Menü -->
<div class="sidebar">
    <a href="index.php">Dashboard</a>
    <a href="add_admin.php">Admin Ekle</a>
    <a href="add_product.php">Ürün Ekle</a>
    <a href="view_products.php">Ürünleri Görüntüle</a>
	<a href="custom_product_management.php">Özel Ürün Yönetimi</a>
	<a href="custom_product_details.php">Özel Konum Resim Ekleme</a>
    <a href="logout.php">Çıkış Yap</a>
</div>

<!-- Ana İçerik -->
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

            <!-- Ürün Sayısı Kartı -->
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
