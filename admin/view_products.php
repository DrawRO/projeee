<?php
session_start();
include '../db.php';

// Admin oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Ürün silme işlemi
if (isset($_GET['delete_id'])) {
    $product_id = (int)$_GET['delete_id'];

    // Ürünü silinmiş olarak işaretle
    $stmt = $conn->prepare("UPDATE products SET is_deleted = 1 WHERE id = ?");
    $stmt->execute([$product_id]);

    header("Location: view_products.php");
    exit;
}

// Ürünü yeniden aktif etme işlemi
if (isset($_GET['activate_id'])) {
    $product_id = (int)$_GET['activate_id'];

    // Ürünü aktif hale getir
    $stmt = $conn->prepare("UPDATE products SET is_deleted = 0 WHERE id = ?");
    $stmt->execute([$product_id]);

    header("Location: view_products.php");
    exit;
}

// Aktif ve pasif ürünleri veritabanından çek
$stmt_active = $conn->prepare("SELECT * FROM products WHERE is_deleted = 0");
$stmt_active->execute();
$active_products = $stmt_active->fetchAll();

$stmt_inactive = $conn->prepare("SELECT * FROM products WHERE is_deleted = 1");
$stmt_inactive->execute();
$inactive_products = $stmt_inactive->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <title>Ürünleri Görüntüle</title>
    <style>
        .sidebar {
            height: 100vh;
            width: 240px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            padding: 15px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
        }
        .sidebar a:hover {
            background-color: #007bff;
        }
        .main-content {
            margin-left: 240px;
            padding: 20px;
        }
    </style>
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
<div class="main-content">
    <h2 class="text-center mb-4">Aktif Ürünler</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ürün Adı</th>
                <th>Kategori</th>
                <th>Açıklama</th>
                <th>Fiyat</th>
                <th>Adet</th>
                <th>Resim</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($active_products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                    <td><?php echo number_format($product['price'], 2); ?> TL</td>
                    <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                    <td><img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" width="50" alt="Resim"></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-warning btn-sm">Düzenle</a>
                        <a href="view_products.php?delete_id=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Ürünü pasif hale getirmek istediğinizden emin misiniz?')">Pasif Yap</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2 class="text-center mb-4">Pasif Ürünler</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ürün Adı</th>
                <th>Kategori</th>
                <th>Açıklama</th>
                <th>Fiyat</th>
                <th>Adet</th>
                <th>Resim</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inactive_products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                    <td><?php echo number_format($product['price'], 2); ?> TL</td>
                    <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                    <td><img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" width="50" alt="Resim"></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-warning btn-sm">Düzenle</a>
                        <a href="view_products.php?activate_id=<?php echo $product['id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Ürünü yeniden aktif hale getirmek istediğinizden emin misiniz?')">Aktif Yap</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
