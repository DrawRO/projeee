<?php
include 'auth.php'; // Oturum kontrolü
include 'db.php'; // Veritabanı bağlantısı

// Ürün ID'sini al
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ürün bilgilerini veritabanından çek
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Geçersiz ürün.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <title><?php echo htmlspecialchars($product['name']); ?> - Ürün Detayı</title>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .product-detail-container {
            margin-top: 50px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 50px auto;
        }
        .product-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="product-detail-container">
        <h2 class="text-center"><?php echo htmlspecialchars($product['name']); ?></h2>
        <img src="assets/images/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
        <p class="mt-3"><?php echo htmlspecialchars($product['description']); ?></p>
        <p class="text-muted">Fiyat: $<?php echo number_format($product['price'], 2); ?></p>
        
        <!-- Satın Al Butonu -->
        <div class="text-center mt-4">
            <a href="buy_product.php?id=<?php echo $product['id']; ?>" class="btn btn-success btn-lg">Satın Al</a>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
