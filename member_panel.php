<?php
include 'auth.php'; // Oturum kontrolü
include 'db.php'; // Veritabanı bağlantısı

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
    <title>Üye Kontrol Paneli</title>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand, .nav-link {
            color: #fff;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
        }
        .announcement-container {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        .announcement {
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">Üye Paneli</a>
    <div class="ml-auto">
        <a href="index.php" class="btn btn-warning">Ana Sayfaya Dön</a>
        <a class="btn btn-danger" href="logout.php">Çıkış Yap</a>
    </div>
</nav>

<div class="container">
    <h2>Merhaba, <?php echo htmlspecialchars($user['username']); ?></h2>
    <p>Bakiye: $<?php echo number_format($user['balance'], 2); ?></p>

    <hr>
    <h4>Bakiye Yükle</h4>
    <form action="balance.php" method="POST">
        <div class="form-group">
            <input type="number" name="amount" class="form-control" placeholder="Yüklemek istediğiniz miktar" required>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Bakiye Yükle</button>
    </form>

    <!-- Duyurular Bölümü -->
    <div class="announcement-container">
        <h4>Duyurular</h4>
        <?php if (!empty($announcements)): ?>
            <?php foreach ($announcements as $announcement): ?>
                <div class="announcement">
                    <h5><?php echo $announcement['title']; ?></h5>
                    <p><?php echo $announcement['content']; ?></p>
                    <small><?php echo date('d/m/Y', strtotime($announcement['created_at'])); ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Henüz duyuru yok.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
