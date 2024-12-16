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
    <meta name="robots" content="noindex, nofollow">
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
        
        <!-- Ağırlık Seçimi -->
        <div class="form-group">
            <label for="weight">Ağırlık Seçimi (gram):</label>
            <select id="weight" class="form-control" onchange="updatePrice()">
                <option value="100">5g</option>
                <option value="250">10g</option>
            </select>
        </div>

        <!-- Fiyat -->
        <p class="text-muted">Fiyat: $<span id="price"><?php echo number_format($product['price'], 2); ?></span></p>
        
        <!-- Satın Al Butonu -->
        <div class="text-center mt-4">
            <a href="buy_product.php?id=<?php echo $product['id']; ?>" class="btn btn-success btn-lg">Satın Al</a>
        </div>
    </div>
</div>

<script>
    // Ürün taban fiyatı (örneğin, 100g fiyatı olarak kabul ediliyor)
    const basePrice = <?php echo $product['price']; ?>;

    function updatePrice() {
        const weight = document.getElementById('weight').value;
        const newPrice = (basePrice * weight) / 100; // Ağırlığa göre fiyatı hesapla
        document.getElementById('price').textContent = newPrice.toFixed(2);
    }
</script>

<script src="assets/js/bootstrap.bundle.min.js"></scrip
