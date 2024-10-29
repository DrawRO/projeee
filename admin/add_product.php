<?php
include '../auth.php'; // Proje ana dizinindeki auth.php dosyasını dahil et
include '../db.php'; // Proje ana dizinindeki db.php dosyasını dahil et

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
        // Ürün veritabanına ekleme
        $stmt = $conn->prepare("INSERT INTO products (name, category, description, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $category, $description, $price, $quantity, $image])) {
            $success_message = "Ürün başarıyla eklendi!";
        } else {
            $error_message = "Ürün eklenirken bir hata oluştu.";
        }
    } else {
        $error_message = "Resim yüklenirken bir hata oluştu.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <title>Ürün Ekle</title>
    <style>
        .form-container {
            margin-top: 50px;
            max-width: 500px;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2 class="text-center mb-4">Ürün Ekle</h2>
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php elseif (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Ürün Adı</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="category">Kategori</label>
                <select class="form-control" id="category" name="category" required>
                    <option value="Esrar">Esrar</option>
                    <option value="Kokain">Kokain</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Açıklama</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Fiyat</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="quantity">Adet</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required>
            </div>
            <div class="form-group">
                <label for="image">Resim Yükle</label>
                <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Ürün Ekle</button>
        </form>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
