<?php
include 'auth.php';
include 'db.php';

// Kullanıcının mevcut bakiyesi
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_balance = $stmt->fetchColumn();

// Ürünleri veritabanından çek
$productStmt = $conn->query("SELECT * FROM custom_products");
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $region = $_POST['region'];
    $product_id = $_POST['product_id'];

    // Seçilen ürün bilgilerini al
    $stmt = $conn->prepare("SELECT * FROM custom_products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {
        $payment_amount = $product['price'];

        if ($user_balance >= $payment_amount) {
            $new_balance = $user_balance - $payment_amount;
            $stmt = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmt->execute([$new_balance, $user_id]);

            // Talebi kaydet
            $stmt = $conn->prepare("INSERT INTO request (user_id, region, product_name, payment_amount, payment_status) VALUES (?, ?, ?, ?, 'completed')");
            $stmt->execute([$user_id, $region, $product['product_name'], $payment_amount]);

            // Satın alma başarılıysa yönlendirme
            header("Location: orders.php");
            exit();
        } else {
            $error_message = "Bakiyeniz yetersiz. Lütfen bakiye yükleyin.";
        }
    } else {
        $error_message = "Geçersiz ürün seçimi.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <title>İstek Bölge Ürün</title>
    <style>
        body {
            background-color: #f9fafb;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin-top: 40px;
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-title {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
        }
        .info-text {
            font-size: 14px;
            color: #5a6268;
            text-align: center;
            margin-bottom: 15px;
        }
        .btn-submit {
            width: 100%;
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border-radius: 8px;
            transition: background-color 0.3s;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        .balance-info {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #28a745;
            margin-top: 10px;
        }
        .alert {
            text-align: center;
            margin-top: 10px;
        }
        #map {
            height: 250px;
            border-radius: 8px;
            margin-top: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        @media (max-width: 768px) {
            .container {
                margin: 20px;
            }
            #map {
                height: 200px;
            }
            .form-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-title">İstek Bölge Ürün</div>
    <h2 class="text-center text-danger">2 Saat İçinde Konumunuzda!</h2> <!-- Yeni eklenen başlık -->
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="info-text">Bakiyeniz: $<?php echo number_format($user_balance, 2); ?></div>
        
        <div class="form-group mb-3">
            <label for="product_id" class="form-label">Ürün Seçin</label>
            <select class="form-control" id="product_id" name="product_id" required onchange="updatePrice()">
                <option value="">Ürün Seçin...</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>">
                        <?php echo htmlspecialchars($product['product_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Fiyat</label>
            <input type="text" class="form-control" id="product_price" readonly>
        </div>

        <div class="form-group mb-3">
            <label for="region" class="form-label">Bölge</label>
            <input type="text" class="form-control" id="region" name="region" required placeholder="Haritadan bölge seçin">
        </div>

        <div id="map"></div>

        <button type="submit" class="btn btn-submit mt-3">Talep Oluştur</button>
    </form>
</div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([41.015137, 28.979530], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var marker;
    map.on('click', function(e) {
        document.getElementById('region').value = e.latlng.lat + ", " + e.latlng.lng;

        if (marker) {
            map.removeLayer(marker);
        }

        marker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map);
    });

    function updatePrice() {
        var productSelect = document.getElementById('product_id');
        var selectedOption = productSelect.options[productSelect.selectedIndex];
        var price = selectedOption.getAttribute('data-price');
        document.getElementById('product_price').value = price ? '$' + parseFloat(price).toFixed(2) : '';
    }
</script>

</body>
</html>
