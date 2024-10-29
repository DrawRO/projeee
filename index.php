<?php
include 'auth.php'; // Oturum kontrolü
include 'db.php'; // Veritabanı bağlantısı

$selected_category = isset($_GET['category']) ? $_GET['category'] : 'Esrar';
$limit = 50;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Ürünleri çek
$query = "SELECT * FROM products WHERE category = :category LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($query);
$stmt->bindParam(':category', $selected_category, PDO::PARAM_STR);
$stmt->execute();
$products = $stmt->fetchAll();

// Toplam ürün sayısını çek
$totalProductsStmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category = :category");
$totalProductsStmt->bindParam(':category', $selected_category, PDO::PARAM_STR);
$totalProductsStmt->execute();
$totalProducts = $totalProductsStmt->fetchColumn();
$totalPages = ($totalProducts > 0) ? ceil($totalProducts / $limit) : 1;

// Kullanıcı bilgilerini çek
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Duyuruları çek
$announcementsStmt = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");
$announcements = $announcementsStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>PRİZRAKOV Anasayfa</title>
    <style>
        body {
            background: url('https://r.resimlink.com/HWqVhKf.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: rgba(0, 0, 0, 0.9);
            padding: 10px 20px;
        }
        .navbar-brand img {
            max-width: 150px;
        }
        .announcement-container {
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            color: #fff;
        }
        .announcement {
            background-color: rgba(0, 0, 0, 0.7);
            margin: 10px 0;
            padding: 15px;
            border-radius: 8px;
        }
        .products-container {
            margin-top: 20px;
        }
        .product-card {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            margin: 15px;
            border-radius: 10px;
            text-align: center;
        }
        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .product-card h5, .product-card p {
            color: black;
        }
        .category-buttons .btn {
            color: black;
            font-weight: bold;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin: 30px 0;
        }
        .pagination a {
            margin: 0 5px;
            padding: 8px 12px;
            background: #004d00;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }
        .pagination a.active, .pagination a:hover {
            background-color: #00ff00;
            color: #000;
        }
    </style>
</head>
<body>

<!-- Logo Üstü Resim -->
<div class="text-center">
    <img src="https://r.resimlink.com/D9XwtYK6HfBo.png" alt="Logo Resmi" style="width: 100%; height: auto;">
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="ml-auto">
        <a href="member_panel.php" class="btn btn-info mx-2">Üye Paneli</a>
        <a href="logout.php" class="btn btn-danger">Çıkış Yap</a>
    </div>
</nav>

<!-- Duyurular Bölümü -->
<div class="container announcement-container mt-4">
    <h2 class="text-center">Duyurular</h2>
    <?php if (!empty($announcements)): ?>
        <?php foreach ($announcements as $announcement): ?>
            <div class="announcement">
                <h5><?php echo $announcement['title']; ?></h5>
                <p><?php echo $announcement['content']; ?></p>
                <small><?php echo date('d/m/Y', strtotime($announcement['created_at'])); ?></small>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center">Henüz duyuru yok.</p>
    <?php endif; ?>
</div>

<!-- Kategori Seçimi -->
<div class="category-buttons text-center">
    <a href="index.php?category=Esrar" class="btn btn-outline-light">Esrar</a>
    <a href="index.php?category=Kokain" class="btn btn-outline-light">Kokain</a>
</div>

<!-- Ürün Listesi -->
<div class="container products-container">
    <div class="row">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="product-card">
                        <img src="assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        <h5><?php echo $product['name']; ?></h5>
                        <p>$<?php echo number_format($product['price'], 2); ?></p>
                        <a href="buy_product.php?id=<?php echo $product['id']; ?>" class="btn btn-success btn-block">Satın Al</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">Henüz ürün bulunamadı.</p>
        <?php endif; ?>
    </div>

    <!-- Sayfalama -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&category=<?php echo $selected_category; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</div>

</body>
</html>
