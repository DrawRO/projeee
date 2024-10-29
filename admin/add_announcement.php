<?php
include '../db.php';
session_start();

// Admin girişi kontrolü
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Duyuru ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO announcements (title, content) VALUES (?, ?)");
    $stmt->execute([$title, $content]);

    echo "Duyuru başarıyla eklendi!";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <title>Duyuru Ekle</title>
</head>
<body>

<!-- Sidebar -->
<?php include 'sidebar.php'; ?> <!-- Sidebar eklendi -->

<div class="container mt-5" style="margin-left: 260px;">
    <h2>Duyuru Ekle</h2>
    <form method="POST">
        <div class="form-group">
            <label for="title">Başlık</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="content">İçerik</label>
            <textarea name="content" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Duyuru Ekle</button>
    </form>
</div>

</body>
</html>
