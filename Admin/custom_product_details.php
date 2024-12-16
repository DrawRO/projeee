<?php
session_start();
include '../db.php';

// Admin oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Özel ürün siparişlerini çekme
$stmtSpecial = $conn->query("SELECT r.id, r.product_name, r.user_id, r.payment_amount, r.payment_status, r.region, u.username, IFNULL(c.product_details, '') AS details 
                             FROM request r 
                             LEFT JOIN users u ON r.user_id = u.id 
                             LEFT JOIN custom_product_details c ON r.id = c.request_id 
                             ORDER BY r.id DESC");
$specialRequests = $stmtSpecial->fetchAll(PDO::FETCH_ASSOC);

// Normal ürünleri çekme (benzersiz siparişlere göre)
$stmtNormal = $conn->query("SELECT o.id AS order_id, p.id AS product_id, p.name AS product_name, p.price, p.subcategory, 
                            o.order_status AS payment_status, u.username, IFNULL(pd.product_details, '') AS details
                            FROM products p
                            LEFT JOIN orders o ON p.id = o.product_id
                            LEFT JOIN users u ON o.user_id = u.id
                            LEFT JOIN product_details pd ON o.id = pd.order_id
                            ORDER BY o.id DESC");
$normalProducts = $stmtNormal->fetchAll(PDO::FETCH_ASSOC);

// Özel ürün detayı ekleme/güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_details'])) {
    $id = $_POST['id'];
    $details = $_POST['details'];

    $stmt = $conn->prepare("SELECT id FROM custom_product_details WHERE request_id = ?");
    $stmt->execute([$id]);
    $detailId = $stmt->fetchColumn();

    if ($detailId) {
        $stmt = $conn->prepare("UPDATE custom_product_details SET product_details = ? WHERE request_id = ?");
        $stmt->execute([$details, $id]);
        $success_message = "Özel ürün detayı başarıyla güncellendi.";
    } else {
        $stmt = $conn->prepare("INSERT INTO custom_product_details (request_id, product_details) VALUES (?, ?)");
        try {
            $stmt->execute([$id, $details]);
            $success_message = "Özel ürün detayı başarıyla eklendi.";
        } catch (PDOException $e) {
            $error_message = "Geçersiz ürün ID'si. Lütfen geçerli bir ürün seçin.";
        }
    }
}

// Normal ürünler için detay ekleme/güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_normal_details'])) {
    $order_id = $_POST['order_id'];
    $details = $_POST['details'];

    $stmt = $conn->prepare("SELECT id FROM product_details WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $detailId = $stmt->fetchColumn();

    if ($detailId) {
        $stmt = $conn->prepare("UPDATE product_details SET product_details = ? WHERE order_id = ?");
        $stmt->execute([$details, $order_id]);
        $success_message = "Normal ürün detayı başarıyla güncellendi.";
    } else {
        $stmt = $conn->prepare("INSERT INTO product_details (order_id, product_details) VALUES (?, ?)");
        $stmt->execute([$order_id, $details]);
        $success_message = "Normal ürün detayı başarıyla eklendi.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Ürün Siparişleri ve Detay Yönetimi</title>
    <style>
        body {
            background-color: #1e1e2f;
            color: #e4e4e7;
            font-family: Arial, sans-serif;
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
        .admin-content {
            margin-left: 260px;
            padding: 25px;
        }
        .form-title {
            font-size: 28px;
            font-weight: bold;
            color: #e4e4e7;
            text-align: center;
            margin-bottom: 25px;
        }
        .table-card {
            background-color: #2a2a40;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
        }
        .table-card h3 {
            font-size: 24px;
            font-weight: bold;
            color: #ffc107;
            margin-bottom: 20px;
            text-align: center;
        }
        .table {
            width: 100%;
            color: #e4e4e7;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #4a4a6a;
            font-size: 16px;
        }
        .table th {
            background-color: #3c3c52;
            color: #e4e4e7;
            font-weight: 600;
        }
        .table tbody tr {
            transition: background-color 0.3s;
        }
        .table tbody tr:hover {
            background-color: #383b5b;
        }
        .btn-detail {
            background-color: #198754;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.2s;
        }
        .btn-detail:hover {
            background-color: #145c32;
            transform: scale(1.05);
        }
        .form-control {
            font-size: 14px;
            background-color: #1e1e2f;
            color: #e4e4e7;
            border: 1px solid #4a4a6a;
        }
        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 5px rgba(255, 193, 7, 0.5);
        }
        .alert {
            font-size: 14px;
            margin-top: 15px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="add_admin.php"><i class="fas fa-user-plus"></i> Admin Ekle</a>
    <a href="add_product.php"><i class="fas fa-plus-circle"></i> Ürün Ekle</a>
    <a href="view_products.php"><i class="fas fa-eye"></i> Ürünleri Görüntüle</a>
    <a href="custom_product_management.php"><i class="fas fa-tasks"></i> Özel Ürün Yönetimi</a>
    <a href="custom_product_details.php"><i class="fas fa-image"></i> Özel Konum Resim Ekleme</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a>
</div>

<div class="admin-content">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <a class="navbar-brand" href="#">Ürün Siparişleri ve Detay Yönetimi</a>
    </nav>

    <div class="container">
        <h1 class="form-title">Ürün Siparişleri ve Detay Yönetimi</h1>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Özel Ürün Siparişleri Tablosu -->
        <div class="table-card">
            <h3>Özel Ürün Siparişleri</h3>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ürün Adı</th>
                        <th>Kullanıcı Adı</th>
                        <th>Region</th>
                        <th>Ödeme Tutarı</th>
                        <th>Ödeme Durumu</th>
                        <th>Detay Ekle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($specialRequests as $request): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['id']); ?></td>
                            <td><?php echo htmlspecialchars($request['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($request['username']); ?></td>
                            <td><?php echo htmlspecialchars($request['region']); ?></td>
                            <td>$<?php echo number_format($request['payment_amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($request['payment_status']); ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="id" value="<?php echo $request['id']; ?>">
                                    <textarea name="details" rows="2" class="form-control mb-2" placeholder="Detayları girin..."><?php echo htmlspecialchars($request['details']); ?></textarea>
                                    <button type="submit" name="save_details" class="btn btn-detail">Kaydet</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Normal Ürünler Tablosu -->
        <div class="table-card">
            <h3>Normal Ürünler</h3>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ürün Adı</th>
                        <th>Kullanıcı Adı</th>
                        <th>Fiyat</th>
                        <th>Alt Kategori</th>
                        <th>Ödeme Durumu</th>
                        <th>Detay Ekle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($normalProducts as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($product['username']); ?></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($product['subcategory']); ?></td>
                            <td><?php echo htmlspecialchars($product['payment_status']); ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="order_id" value="<?php echo $product['order_id']; ?>">
                                    <textarea name="details" rows="2" class="form-control mb-2" placeholder="Detayları girin..."><?php echo htmlspecialchars($product['details']); ?></textarea>
                                    <button type="submit" name="save_normal_details" class="btn btn-detail">Kaydet</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
