<?php
include '../db.php'; // Veritabanı bağlantısı

// Ana kategoriler ve alt kategoriler
$categories = [
    'Esrar' => ['Şişli', 'Beşiktaş', 'Nişantaşı', 'Bomonti', 'Kurtuluş', 'Maçka', 'Fulya', 'Gayrettepe', 'Zincirlikuyu', '4. Levent', 'Sarıyer', 'Ortaköy', 'Bebek', 'Etiler', 'İstinye', 'Çayırbaşı', 'Arnavutköy', 'Aşiyan', 'Hisarüstü', 'Maslak', 'Talatpaşa', 'Gülbağ', 'Beyoğlu', 'Taksim', 'Galata', 'Cihangir', 'Tophane', 'Karaköy', 'Kabataş', 'Eminönü', 'Sirkeci', 'Kağıthane', 'Nurtepe', '5. Levent', 'Vadi İstanbul', 'Kemerburgaz'],
    'Kokain' => ['Şişli', 'Beşiktaş', 'Nişantaşı', 'Bomonti', 'Kurtuluş', 'Maçka', 'Fulya', 'Gayrettepe', 'Zincirlikuyu', '4. Levent', 'Sarıyer', 'Ortaköy', 'Bebek', 'Etiler', 'İstinye', 'Çayırbaşı', 'Arnavutköy', 'Aşiyan', 'Hisarüstü', 'Maslak', 'Talatpaşa', 'Gülbağ', 'Beyoğlu', 'Taksim', 'Galata', 'Cihangir', 'Tophane', 'Karaköy', 'Kabataş', 'Eminönü', 'Sirkeci', 'Kağıthane', 'Nurtepe', '5. Levent', 'Vadi İstanbul', 'Kemerburgaz']
];

$selected_category = isset($_POST['category']) ? $_POST['category'] : '';
$selected_subcategory = isset($_POST['subcategory']) ? $_POST['subcategory'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'], $_POST['category'], $_POST['subcategory'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $subcategory = $_POST['subcategory'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $image = $_FILES['image']['name'];

    $target_dir = "../assets/images/";
    $target_file = $target_dir . basename($image);

    // Resim yükleme işlemi
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO products (name, category, subcategory, description, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $category, $subcategory, $description, $price, $quantity, $image])) {
            $success_message = "Ürün başarıyla eklendi!";
        } else {
            $error_message = "Ürün eklenirken hata oluştu.";
        }
    } else {
        $error_message = "Resim yüklenirken hata oluştu.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <title>Ürün Ekle</title>
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
<div class="main-content">
    <h2 class="text-center mb-4">Ürün Ekle</h2>
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Ürün Adı</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="form-group mt-3">
            <label for="category">Kategori</label>
            <select class="form-control" id="category" name="category" required onchange="this.form.submit()">
                <option value="">Ana Kategori Seçin</option>
                <?php foreach ($categories as $category => $subcategories): ?>
                    <option value="<?php echo $category; ?>" <?php echo ($selected_category == $category) ? 'selected' : ''; ?>>
                        <?php echo $category; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="subcategory">Alt Kategori</label>
            <select class="form-control" id="subcategory" name="subcategory" required>
                <option value="">Alt Kategori Seçin</option>
                <?php if ($selected_category && isset($categories[$selected_category])): ?>
                    <?php foreach ($categories[$selected_category] as $subcategory): ?>
                        <option value="<?php echo htmlspecialchars($subcategory); ?>" <?php echo ($selected_subcategory == $subcategory) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($subcategory); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="description">Açıklama</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>
        
        <div class="form-group mt-3">
            <label for="price">Fiyat</label>
            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
        </div>

        <div class="form-group mt-3">
            <label for="quantity">Adet</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
        </div>

        <div class="form-group mt-3">
            <label for="image">Resim Yükle</label>
            <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
        </div>

        <button type="submit" class="btn btn-primary mt-4">Ürün Ekle</button>
    </form>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
