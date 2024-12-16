<?php
session_start();
include '../db.php';

// Admin oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Ürün ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO custom_products (product_name, price) VALUES (?, ?)");
    $stmt->execute([$product_name, $price]);

    $success_message = "Ürün başarıyla eklendi.";
}

// Ürün silme işlemi
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM custom_products WHERE id = ?");
    $stmt->execute([$product_id]);

    $success_message = "Ürün başarıyla silindi.";
}

// Mevcut ürünleri çekme
$stmt = $conn->query("SELECT * FROM custom_products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <title>Özel Ürün Yönetimi</title>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
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
        .admin-content {
            margin-left: 240px;
            padding: 20px;
        }
        .admin-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .form-title {
            font-size: 26px;
            font-weight: bold;
            color: #343a40;
            text-align: center;
            margin-bottom: 25px;
        }
        .btn-submit {
            background-color: #0d6efd;
            color: white;
            font-weight: bold;
            border-radius: 8px;
            transition: background-color 0.3s, transform 0.2s;
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.15);
        }
        .btn-submit:hover {
            background-color: #0a58ca;
            transform: scale(1.02);
        }
        .alert {
            text-align: center;
            margin-top: 10px;
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
        <a class="navbar-brand" href="#">Özel Ürün Yönetimi</a>
    </nav>

    <div class="container mt-5">
        <h1 class="form-title">Özel Ürün Yönetimi</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <!-- Ürün Ekleme Formu -->
        <form method="POST" action="">
            <div class="form-group mb-3">
                <label for="product_name" class="form-label">Ürün Adı</label>
                <input type="text" class="form-control" id="product_name" name="product_name" required>
            </div>
            <div class="form-group mb-3">
                <label for="price" class="form-label">Fiyat</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
            </div>
            <button type="submit" name="add_product" class="btn btn-submit">Ürün Ekle</button>
        </form>

        <!-- Ürün Listesi -->
        <h4 class="mt-5">Mevcut Ürünler</h4>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ürün Adı</th>
                    <th>Fiyat</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <a href="?delete=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bu ürünü silmek istediğinize emin misiniz?');">Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
