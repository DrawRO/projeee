<?php
include 'auth.php'; // Oturum kontrolü
include 'db.php'; // Veritabanı bağlantısı

// Ana kategoriler ve alt kategoriler sabit olarak tanımlandı
$categories = [
    'Esrar' => ['Şişli', 'Beşiktaş', 'Nişantaşı', 'Bomonti', 'Kurtuluş', 'Maçka', 'Fulya', 'Gayrettepe', 'Zincirli kuyu', '4.levent', 'Sarıyer', 'Ortaköy', 'Bebek', 'Etiler', 'İstinye', 'çayırbaşı', 'Arnavutköy', 'Aşiyan', 'Hisarüstü', 'Maslak', 'Talatpaşa', 'Gülbağ', 'Beyoğlu', 'Taksim', 'Galata', 'Cihangir', 'Tophane', 'Karaköy', 'Kabataş', 'Eminönü', 'Sirkeci', 'Kağıthane', 'Nurtepe', '5.levent', 'Vadi istanbul', 'Kemerburgaz'],
    'Kokain' => ['Şişli', 'Beşiktaş', 'Nişantaşı', 'Bomonti', 'Kurtuluş', 'Maçka', 'Fulya', 'Gayrettepe', 'Zincirli kuyu', '4.levent', 'Sarıyer', 'Ortaköy', 'Bebek', 'Etiler', 'İstinye', 'çayırbaşı', 'Arnavutköy', 'Aşiyan', 'Hisarüstü', 'Maslak', 'Talatpaşa', 'Gülbağ', 'Beyoğlu', 'Taksim', 'Galata', 'Cihangir', 'Tophane', 'Karaköy', 'Kabataş', 'Eminönü', 'Sirkeci', 'Kağıthane', 'Nurtepe', '5.levent', 'Vadi istanbul', 'Kemerburgaz'],
];

// Seçilen kategori ve alt kategoriye göre filtreleme
$selected_category = isset($_GET['category']) ? $_GET['category'] : 'Esrar';
$selected_subcategory = isset($_GET['subcategory']) ? $_GET['subcategory'] : null;

// Sayfalama ayarları
$limit = 50; // Her sayfada gösterilecek maksimum ürün sayısı
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Geçerli sayfa numarası
$offset = ($page - 1) * $limit; // Veritabanı sorgusu için başlangıç noktası

// Seçilen kategori ve alt kategoriye göre ürünleri çek
if ($selected_subcategory) {
    $query = "SELECT * FROM products WHERE category = :category AND subcategory = :subcategory AND is_deleted = 0 LIMIT $limit OFFSET $offset";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':category', $selected_category, PDO::PARAM_STR);
    $stmt->bindParam(':subcategory', $selected_subcategory, PDO::PARAM_STR);
} else {
    $query = "SELECT * FROM products WHERE category = :category AND is_deleted = 0 LIMIT $limit OFFSET $offset";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':category', $selected_category, PDO::PARAM_STR);
}

$stmt->execute();
$products = $stmt->fetchAll();

// Toplam ürün sayısını al
if ($selected_subcategory) {
    $totalProductsStmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category = :category AND subcategory = :subcategory AND is_deleted = 0");
    $totalProductsStmt->bindParam(':category', $selected_category, PDO::PARAM_STR);
    $totalProductsStmt->bindParam(':subcategory', $selected_subcategory, PDO::PARAM_STR);
} else {
    $totalProductsStmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category = :category AND is_deleted = 0");
    $totalProductsStmt->bindParam(':category', $selected_category, PDO::PARAM_STR);
}

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
    <meta name="robots" content="noindex, nofollow">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <title>Anasayfa - <?php echo $selected_category; ?></title>
    <style>
/* Ana kategori buton stili */
.btn-category {
    background-color: #007bff;
    color: white;
    border-radius: 10px;
    padding: 15px 30px;
    font-weight: bold;
    font-size: 18px;
    margin: 0 15px;
    transition: background-color 0.3s, box-shadow 0.3s;
    box-shadow: 0 4px 6px rgba(0, 123, 255, 0.3);
    border: none;
}
.btn-category:hover {
    background-color: #0056b3;
    box-shadow: 0 6px 12px rgba(0, 123, 255, 0.5);
}

/* Alt kategori butonları */
.btn-subcategory {
    background-color: #6c757d;
    color: white;
    border-radius: 6px;
    padding: 8px 16px;
    font-size: 14px;
    transition: background-color 0.3s, box-shadow 0.3s;
    box-shadow: 0 3px 5px rgba(108, 117, 125, 0.3);
    border: none;
    display: inline-block;
    margin: 5px;
}
.btn-subcategory:hover {
    background-color: #5a6268;
    box-shadow: 0 4px 8px rgba(108, 117, 125, 0.5);
}

/* Ana kategori butonlarını yukarı taşımak için */
.category-buttons {
    margin-top: -30px;
}

/* Alt kategorilerin hizalanmasını sağlamak için */
.category-buttons .mt-4 {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
}

/* Genel gövde ayarları */
body {
    background-image: url('https://r.resimlink.com/M6jwq.jpg');
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
.header-logo {
    text-align: center;
    margin-bottom: 20px;
}
.header-logo img {
    height: 190px;
    width: auto;
}

/* Navbar Genel Ayarlar */
.navbar {
    min-height: 70px;
    padding: 0.5rem 1rem;
    background-color: rgba(255, 255, 255, 0.95);
    display: flex;
    align-items: center;
    width: 100%;
    box-sizing: border-box;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.navbar-brand {
    font-size: 1.75rem;
    font-weight: bold;
    color: #007bff;
}

/* Bilgi Kutuları */
.info-box {
    padding: 5px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f8f9fa;
    font-size: 16px;
    color: #333;
}

/* Bakiye Yükle Butonu */
.btn-balance {
    background-color: #ffc107;
    color: #fff;
    font-weight: 600;
    padding: 8px 15px;
    border-radius: 8px;
    text-decoration: none;
    transition: background-color 0.3s;
}
.btn-balance:hover {
    background-color: #e0a800;
    color: #fff;
}

/* Çıkış Yap Butonu */
.btn-logout {
    color: #dc3545;
    font-weight: 600;
    padding: 8px 15px;
    border: 1px solid #dc3545;
    border-radius: 8px;
    background-color: transparent;
    transition: all 0.3s;
    text-decoration: none;
}
.btn-logout:hover {
    background-color: #dc3545;
    color: #fff;
}

/* Genel Boşluk Ayarı */
.user-controls > * {
    margin-left: 15px;
}

.products-container {
    width: 90%;
    max-width: 1000px;
    background-color: rgba(255, 255, 255, 0.7);
    border: 2px solid black;
    border-radius: 40px;
    padding: 30px;
    margin-top: 20px;
}
.product-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    background-color: rgba(255, 255, 255, 0.8);
    height: 250px;
    transition: box-shadow 0.3s;
    width: 150px;
}
.product-card img {
    width: 100%;
    height: 120px;
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
.chat-container {
    width: 240px;
    position: fixed;
    right: 10px;
    bottom: 10px;
    background: rgba(0, 0, 0, 0.8);
    border-radius: 10px;
    padding: 10px;
    z-index: 10;
}
.chat-header {
    text-align: center;
    margin-bottom: 10px;
    color: white;
}
.chat-box {
    height: 200px;
    overflow-y: scroll;
    border: 1px solid #fff;
    padding: 5px;
    margin-bottom: 10px;
    color: white;
}
.chat-input {
    display: flex;
}
.chat-input input {
    flex: 1;
    padding: 5px;
    border: 1px solid #fff;
    border-radius: 5px;
}
.chat-input button {
    padding: 5px 10px;
    background: #28a745;
    color: #fff;
    border: none;
    border-radius: 5px;
    margin-left: 5px;
}
body, html {
    height: 100%;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}
.announcement-container {
    width: 240px;
    position: fixed;
    left: 10px;
    bottom: 10px;
    background: rgba(0, 0, 0, 0.8);
    color: #fff;
    border-radius: 10px;
    padding: 15px;
    z-index: 1000;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    font-family: 'Arial', sans-serif;
    max-height: 1500px;
}
.announcement-header {
    text-align: center;
    margin-bottom: 10px;
}
.announcement-header h4 {
    margin: 0;
    color: #ffc107;
}
.announcement-content {
    max-height: 150px;
    overflow-y: auto;
}
.announcement-box {
    background: rgba(255, 255, 255, 0.1);
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 5px;
}
.announcement-date {
    font-size: 0.8rem;
    color: #ffc107;
    margin-bottom: 5px;
}

/* --- Navbar ve Butonları için Mobil Düzenlemeler --- */
@media (max-width: 768px) {
    /* Navbar'ı dikey hizala */
    .navbar {
        flex-direction: column;
        align-items: center;
    }

    /* Navbar butonları dikey dizilim */
    .user-controls {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px; /* Butonlar arasındaki boşluk */
        margin-top: 10px;
    }

    /* Navbar içindeki bilgi kutuları ve butonlar */
    .info-box, .btn-balance, .btn-logout {
        width: 100%;
        text-align: center;
        margin: 5px 0;
    }
    .user-controls {
        flex-direction: column;
        align-items: center;
    }
    .user-controls a, .user-controls div {
        margin: 5px 0;
    }



    /* Ürün Kartları */
    .products-container {
        width: 100%;
    }
    .product-card {
        width: 90%;
    }

    /* Sohbet ve Duyuru Kutuları */
    .chat-container, .announcement-container {
        width: 100%;
        position: relative;
        margin: 10px auto;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Sohbet ve Duyuru Kutusu İçeriği */
    .chat-box, .announcement-content {
        height: auto;
        max-height: 150px;
    }

    /* Sayfalama */
    .pagination a {
        font-size: 12px;
        padding: 5px 8px;
    }

    /* Logo */
    .header-logo img {
        height: 120px;
    }
}


/* Mobilde açılır kapanır menüye beyaz arka plan ekleme */
@media (max-width: 992px) {
    .navbar-collapse {
        background-color: white;
        border-radius: 8px;
        padding: 10px;
    }
    
    /* Menü içeriği için daha iyi görünüm */
    .user-controls a, .user-controls div {
        color: black;
        margin: 5px 0;
    }
    
    .user-controls .btn-balance, .user-controls .btn-logout {
        width: 100%;
    }
}
.navbar-toggler {
    border: none;
    outline: none;
}

.navbar-toggler-icon {
    background-image: url('https://r.resimlink.com/Bg3UHJ0Vjx.png');
    background-size: cover; /* Resmi buton alanına uyacak şekilde boyutlandırır */
    background-position: center;
    width: 30px; /* Buton boyutlarını ihtiyacınıza göre ayarlayabilirsiniz */
    height: 30px;
}
.menu-icon {
    width: 24px; /* İkon genişliği */
    height: 24px; /* İkon yüksekliği */
    object-fit: cover; /* İkonun orantılı şekilde görünmesini sağlar */
}
/* Mobil için açılır alt kategori menü stili */
.alt-category-container {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-top: 20px;
    display: flex;
    flex-wrap: wrap;
    justify-content: center; /* Butonları ortalar */
    gap: 15px; /* Butonlar arasındaki boşluk */
}

/* Mobilde alt kategori açılır kapanır düğmeleri */
.btn-primary {
    width: auto;
    padding: 10px 20px;
    display: inline-block;
    text-align: center;
    margin-top: 20px; /* Butonu aşağı kaydırmak için boşluk ekledik */
}
/* Mobil için ürün kartı boyutunu küçültme */
@media (max-width: 768px) {
    .product-card {
        width: 120px; /* Kart genişliğini daraltıyoruz */
        height: auto; /* Otomatik yüksekliğe geçiş yapıyoruz */
        padding: 8px; /* Kart içindeki boşluğu azaltıyoruz */
    }

    .product-card img {
        height: 80px; /* Görsel yüksekliğini küçültüyoruz */
    }

    .product-card h5 {
        font-size: 14px; /* Başlık boyutunu küçültüyoruz */
        margin-top: 5px; /* Başlık ile üst kısım arasındaki boşluğu azaltıyoruz */
    }

    .product-card p {
        font-size: 12px; /* Fiyat ve açıklama yazısını küçültüyoruz */
        margin-top: 4px;
    }
}

/* Tablet ekranlar için (isteğe bağlı olarak biraz daha büyük) */
@media (min-width: 769px) and (max-width: 992px) {
    .product-card {
        width: 140px;
        padding: 12px;
    }

    .product-card img {
        height: 100px;
    }

    .product-card h5 {
        font-size: 16px;
    }

    .product-card p {
        font-size: 13px;
    }



    </style>
    <!-- jQuery ve Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class="header-logo">
  <a href="index.php">
    <img src="https://r.resimlink.com/uy2SMjeU7w.png" alt="Logo" class="logo">
  </a>
</div>




<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="index.php">Ana Sayfa</a>
    
    <!-- Menü Toggler (Sadece Mobil için) -->
    <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
        <!-- İkonu burada <img> olarak ekliyoruz -->
        <img src="https://r.resimlink.com/Bg3UHJ0Vjx.png" alt="Menu Icon" class="menu-icon">
    </button>

    <!-- Navbar İçeriği (Açılır Kapanır Menü) -->
    <div class="collapse navbar-collapse" id="navbarContent">
        <div class="ms-auto d-flex align-items-center user-controls flex-column flex-lg-row">
            <!-- Siparişler Butonu -->
            <a href="orders.php" class="info-box me-3 mb-2 mb-lg-0">Siparişler</a>

            <!-- İstek Bölge Ürün Butonu -->
            <a href="request_product.php" class="info-box me-3 mb-2 mb-lg-0">İstek Bölge Ürün</a>

            <?php
            // Kullanıcı bakiyesini veritabanından çekme
            $user_id = $_SESSION['user_id'];
            $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user_balance = $stmt->fetchColumn();
            ?>

            <!-- Kullanıcı Adı -->
            <div class="info-box me-3 mb-2 mb-lg-0">
                <span>Merhaba, <?php echo htmlspecialchars($user['username']); ?></span>
            </div>

            <!-- Bakiye Bilgisi -->
            <div class="info-box me-3 mb-2 mb-lg-0">
                <span>Bakiyeniz: $<?php echo number_format($user_balance, 2); ?></span>
            </div>

            <!-- Bakiye Yükle Butonu -->
            <a href="load_balance.php" class="btn btn-balance me-3 mb-2 mb-lg-0">Bakiye Yükle</a>

            <!-- Çıkış Yap Butonu -->
            <a class="btn btn-logout mb-2 mb-lg-0" href="logout.php">Çıkış Yap</a>
        </div>
    </div>
</nav>





<div class="products-container mt-5">
    <h2 class="text-center mb-4"></h2>

<!-- Kategori ve Alt Kategori Seçimi -->
<div class="text-center mb-4 category-buttons">
    <!-- Ana Kategori Butonları -->
    <a href="index.php?category=Esrar" class="btn-category">Esrar</a>
    <a href="index.php?category=Kokain" class="btn-category">Kokain</a>

    <!-- Esrar Alt Kategorileri Göster Butonu (Sadece Mobilde) -->
    <button class="btn btn-primary d-lg-none mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#esrarSubcategories" aria-expanded="false" aria-controls="esrarSubcategories">
        Esrar Bölgeleri
    </button>

    <!-- Esrar Alt Kategoriler (Mobilde Gösterilecek) -->
    <div class="collapse d-lg-none" id="esrarSubcategories">
        <div class="mt-2 alt-category-container">
            <?php if (isset($categories['Esrar'])): ?>
                <?php foreach ($categories['Esrar'] as $subcategory): ?>
                    <a href="index.php?category=Esrar&subcategory=<?php echo $subcategory; ?>" class="btn-subcategory">
                        <?php echo $subcategory; ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Kokain Alt Kategorileri Göster Butonu (Sadece Mobilde) -->
    <button class="btn btn-primary d-lg-none mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#kokainSubcategories" aria-expanded="false" aria-controls="kokainSubcategories">
        Kokain Bölgeleri
    </button>

    <!-- Kokain Alt Kategoriler (Mobilde Gösterilecek) -->
    <div class="collapse d-lg-none" id="kokainSubcategories">
        <div class="mt-2 alt-category-container">
            <?php if (isset($categories['Kokain'])): ?>
                <?php foreach ($categories['Kokain'] as $subcategory): ?>
                    <a href="index.php?category=Kokain&subcategory=<?php echo $subcategory; ?>" class="btn-subcategory">
                        <?php echo $subcategory; ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Masaüstü için Alt Kategori Butonları (Her İki Kategori için) -->
    <div class="mt-4 d-none d-lg-flex">
        <?php if (isset($categories[$selected_category])): ?>
            <?php foreach ($categories[$selected_category] as $subcategory): ?>
                <a href="index.php?category=<?php echo $selected_category; ?>&subcategory=<?php echo $subcategory; ?>" class="btn-subcategory">
                    <?php echo $subcategory; ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>





    <!-- Ürün kartları burada listelenecek -->
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
</div>


    <!-- Sayfalama -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&category=<?php echo $selected_category; ?><?php echo $selected_subcategory ? '&subcategory=' . $selected_subcategory : ''; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</div>


<!-- Sohbet Kutusu -->
<div class="chat-container">
    <div class="chat-header">
        <h3>Canlı Sohbet</h3>
    </div>
    <div class="chat-box" id="chat-box">
        <?php
        // Sohbet mesajlarını veritabanından çekip gösterme
        $stmt = $conn->query("SELECT username, message FROM messages ORDER BY id DESC");
        while ($row = $stmt->fetch()) {
            echo "<div><strong>" . htmlspecialchars($row['username']) . ":</strong> " . htmlspecialchars($row['message']) . "</div>";
        }
        ?>
    </div>
    <div class="chat-input">
        <input type="text" id="message" placeholder="Mesajınızı yazın...">
        <button onclick="sendMessage()">Gönder</button>
    </div>
</div>

<!-- Duyuru Kutusu -->
<div class="announcement-container">
    <div class="announcement-header">
        <h4>Duyurular</h4>
    </div>
    <div class="announcement-content">
        <?php
        // Örnek duyurular dizisi (veritabanından da çekilebilir)
        $announcements = [
            ["date" => "01/11/2024", "message" => "Yeni ürünler eklendi, hemen göz atın!"],
            ["date" => "31/10/2024", "message" => "Sistem güncellemeleri hakkında bilgi almak için <a href='#'>buraya tıklayın</a>."]
        ];
        foreach ($announcements as $announcement): ?>
            <div class="announcement-box">
                <p class="announcement-date"><?php echo htmlspecialchars($announcement['date']); ?></p>
                <p><?php echo $announcement['message']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>


<script>
    function sendMessage() {
        const messageInput = document.getElementById('message');
        const message = messageInput.value.trim();

        if (message !== '') {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "send_message.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    if (xhr.responseText === "success") {
                        const chatBox = document.getElementById('chat-box');
                        const messageDiv = document.createElement('div');
                        messageDiv.textContent = "<?php echo htmlspecialchars($user['username']); ?>: " + message;
                        chatBox.insertBefore(messageDiv, chatBox.firstChild);
                        messageInput.value = '';
                        chatBox.scrollTop = chatBox.scrollHeight; // Otomatik kaydırma
                    } else {
                        alert("Mesaj gönderilemedi!");
                    }
                }
            };
            xhr.send("message=" + encodeURIComponent(message));
        }
    }
</script>

</body>
</html>
