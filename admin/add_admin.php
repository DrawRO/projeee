<?php
session_start();
include '../db.php';

// Hata ayıklama modunu aç
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Admin oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php'); // Admin giriş sayfasına yönlendir
    exit;
}

// Admin ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Veritabanına yeni admin ekle
    $stmt = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $email, $hashed_password])) {
        $success_message = "Admin başarıyla eklendi!";
    } else {
        $error_message = "Admin eklenirken bir hata oluştu.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <title>Admin Ekle</title>
    <style>
        .admin-content {
            margin-left: 240px;
            padding: 20px;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
		        .sidebar {
            height: 100vh;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #2a2a40;
            color: #e4e4e7;
            padding-top: 20px;
            transition: all 0.3s;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.4);
        }
        .sidebar a {
            padding: 15px;
            text-decoration: none;
            font-size: 18px;
            color: #e4e4e7;
            display: flex;
            align-items: center;
            transition: background 0.3s, transform 0.2s;
        }
        .sidebar a i {
            margin-right: 10px;
            font-size: 18px;
        }
        .sidebar a:hover {
            background-color: #4a4a6a;
            color: white;
            transform: translateX(5px);
        }
    </style>
<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<!-- Ortak Yan Menü Dahil Edilir -->
<?php include 'sidebar.php'; ?>

<div class="admin-content">
    <h1 class="text-center">Admin Ekle</h1>
    <div class="form-container">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php elseif (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Kullanıcı Adı</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">E-Posta</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Admin Ekle</button>
        </form>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
