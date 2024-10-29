<?php
include 'auth.php'; // Oturum kontrolü
include 'db.php'; // Veritabanı bağlantısı

// Seçilen kategoriye göre filtreleme
$selected_category = isset($_GET['category']) ? $_GET['category'] : 'Esrar';

// Sayfalama ayarları
$limit = 50; // Her sayfada gösterilecek maksimum ürün sayısı
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Geçerli sayfa numarası
$offset = ($page - 1) * $limit; // Veritabanı sorgusu için başlangıç noktası

// Seçilen kategoriye göre ürünleri çek
$query = "SELECT * FROM products WHERE category = :category LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($query);
$stmt->bindParam(':category', $selected_category, PDO::PARAM_STR);
$stmt->execute();
$products = $stmt->fetchAll();

// Toplam ürün sayısını al
$totalProductsStmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category = :category");
$totalProductsStmt->bindParam(':category', $selected_category, PDO::PARAM_STR);
$totalProductsStmt->execute();
$totalProducts = $totalProductsStmt->fetchColumn();

// Toplam sayfa sayısını hesapla
$totalPages = ceil($totalProducts / $limit);

// Kullanıcı bilgilerini çek
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <title>Anasayfa - <?php echo $selected_category; ?></title>
    <style>
        body {
            background-image: url('https://img.pikbest.com/wp/202345/indoor-plant-cannabis-plants-growing-in-the-dark_9604120.jpg!w700wp');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .navbar {
            width: 100%;
            z-index: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0.5rem 1rem;
            background-color: rgba(255, 255, 255, 0.8);
        }
        .navbar-brand {
            font-size: 2rem;
            font-weight: bold;
            color: #000;
        }
        .user-controls {
            position: absolute;
            right: 20px;
        }
        .products-container {
            width: 90%;
            max-width: 1200px;
            background-color: rgba(255, 255, 255, 0.9);
            border: 2px solid black;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            height: 100%;
            transition: box-shadow 0.3s;
        }
        .product-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a {
            margin: 0 5px;
            padding: 8px 12px;
            border: 1px solid black;
            border-radius: 5px;
            text-decoration: none;
            color: black;
            transition: background-color 0.3s;
        }
        .pagination a:hover, .pagination a.active {
            background-color: black;
            color: white;
        }
    </style>
    <!-- jQuery ve Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="#">PRİZRAKOV</a>
    <div class="user-controls">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="#">Merhaba, <?php echo htmlspecialchars($user['username']); ?></a></li>
            <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="logout.php">Çıkış Yap</a></li>
        </ul>
    </div>
</nav>

<div class="products-container mt-5">
    <h2 class="text-center mb-4"><?php echo $selected_category; ?></h2>
    <!-- Kategori Seçimi -->
    <div class="text-center mb-4">
        <a href="index.php?category=Esrar" class="btn btn-outline-dark">Esrar</a>
        <a href="index.php?category=Kokain" class="btn btn-outline-dark">Kokain</a>
    </div>

    <div class="row">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                    <div class="product-card">
                        <img src="assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        <h5 class="text-center"><?php echo $product['name']; ?></h5>
                        <p class="text-center text-muted">$<?php echo number_format($product['price'], 2); ?></p>
                        <div class="text-center">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#productModal<?php echo $product['id']; ?>">
                                Satın Al
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Ürün Detay Modal -->
                <div class="modal fade" id="productModal<?php echo $product['id']; ?>" tabindex="-1" aria-labelledby="productModalLabel<?php echo $product['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="productModalLabel<?php echo $product['id']; ?>"><?php echo $product['name']; ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                            </div>
                            <div class="modal-body">
                                <img src="assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="img-fluid mb-3">
                                <p><?php echo $product['description']; ?></p>
                                <p class="text-muted">Fiyat: $<?php echo number_format($product['price'], 2); ?></p>
                            </div>
                            <div class="modal-footer">
                                <a href="buy_product.php?id=<?php echo $product['id']; ?>" class="btn btn-success">Satın Al</a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">Henüz ürün yok.</p>
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
