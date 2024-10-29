<?php
session_start();
include '../db.php';

// Admin oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Ürünleri veritabanından çek
$stmt = $conn->query("SELECT * FROM products");
$products = $stmt->fetchAll();

// Ürün silme işlemi
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM products WHERE id = $delete_id");
    header('Location: view_products.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <title>Ürünleri Görüntüle</title>
    <style>
        .main-content {
            margin-left: 240px;
            padding: 20px;
        }
    </style>
</head>
<body>

<!-- Yan Menü -->
<?php include 'sidebar.php'; ?> <!-- Sidebar eklendi -->

<!-- Ana İçerik -->
<div class="main-content">
    <h2 class="text-center mb-4">Ürünleri Görüntüle</h2>

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
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['category']; ?></td>
                    <td><?php echo $product['description']; ?></td>
                    <td><?php echo $product['price']; ?> TL</td>
                    <td><?php echo $product['quantity']; ?></td>
                    <td><img src="../assets/images/<?php echo $product['image']; ?>" width="50" alt="Resim"></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-warning btn-sm">Düzenle</a>
                        <a href="view_products.php?delete_id=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Ürünü silmek istediğinizden emin misiniz?')">Sil</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
