<?php
session_start();
include 'db.php'; // Veritabanı bağlantısı

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Kullanıcıyı veritabanından çek
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Kullanıcı doğrulaması
    if ($user && password_verify($password, $user['password'])) {
        // Oturum değişkenlerini ayarla
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Admin ise admin paneline yönlendir, değilse ana sayfaya
        if ($user['role'] == 'admin') {
            header("Location: admin/index.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error_message = "Geçersiz e-posta veya şifre.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="robots" content="noindex, nofollow">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <title>Giriş Yap</title>
    <style>
        body {
            background-image: url('https://r.resimlink.com/OwqA4gVBl.jpg'); /* Arka plan resminiz */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative; /* Pozisyonu relative olarak ayarla */
        }

        /* Şeffaf siyah zemin için yeni bir before öğesi ekle */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5); /* Siyah renk, %50 opaklık */
            z-index: 0; /* Zeminin arka planda kalmasını sağlar */
        }

        .container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 100%;
            position: relative; /* İçerik zemin üzerinde görünsün diye */
            z-index: 1; /* İçeriği öne çıkar */
        }
    </style>
<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Giriş Yap</h2>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">E-posta</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Giriş Yap</button>
        </form>
        <div class="text-center mt-3">
            <a href="register.php">Hesabınız yok mu? Kayıt olun!</a>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>