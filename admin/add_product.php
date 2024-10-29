<?php
include '../db.php'; // Veritabanı bağlantısı

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $image = $_FILES['image']['name'];

    $target_dir = "../assets/images/";
    $target_file = $target_dir . basename($image);

    // Resim yükleme işlemi
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO products (name, category, description, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $category, $description, $price, $quantity, $image])) {
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
        .main-content {
            margin-left: 240px;
            padding: 20px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<?php include 'sidebar.php'; ?> <!-- Sidebar eklendi -->

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
            <select class="form-control" id="category" name="category" required>
                <option value="Esrar">Esrar</option>
                <option value="Kokain">Kokain</option>
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
