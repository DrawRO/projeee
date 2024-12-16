<?php
session_start();

// Oturum açma kontrolü
if (!isset($_SESSION['user_id'])) {
    die("Kullanıcı oturum açmamış. Lütfen giriş yapın.");
}

// Veritabanı bağlantısını dahil et
include $_SERVER['DOCUMENT_ROOT'] . '/db.php';

// Bildirimleri veritabanından PDO ile çek
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM notifications ORDER BY created_at DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Sorgu hatası: " . $conn->errorInfo()[2]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Notifications</h1>
        <div class="list-group">
            <?php if ($result->rowCount() > 0): ?>
                <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="list-group-item">
                        <?php echo htmlspecialchars($row['message']); ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="list-group-item">Bildirim bulunamadı.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
