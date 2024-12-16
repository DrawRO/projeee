<?php
include 'auth.php';
include 'db.php';

$user_id = $_SESSION['user_id'];

// Siparişleri getirme sorgusu
$stmt = $conn->prepare("
    SELECT 
        o.id, 
        o.quantity, 
        o.total, 
        o.order_status, 
        o.created_at,
        p.name AS product_name,
        p.subcategory AS subcategory,
        IFNULL(pd.product_details, '') AS product_details,
        'Normal Ürün' AS product_type
    FROM orders o
    LEFT JOIN products p ON o.product_id = p.id
    LEFT JOIN product_details pd ON o.id = pd.order_id -- Benzersiz detay için order_id kullanılır
    WHERE o.user_id = ?

    UNION ALL

    SELECT 
        o.id, 
        1 AS quantity, 
        o.payment_amount AS total, 
        o.payment_status AS order_status, 
        o.created_at,
        o.product_name,
        NULL AS subcategory,
        IFNULL(cd.product_details, '') AS product_details,
        'Özel Ürün' AS product_type
    FROM request o
    LEFT JOIN custom_product_details cd ON o.id = cd.request_id
    WHERE o.user_id = ?
");

$stmt->execute([$user_id, $user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Siparişleri Normal Ürünler ve Özel Ürünler olarak ayırma
$normalProducts = array_filter($orders, function($order) {
    return $order['product_type'] === 'Normal Ürün';
});
$specialProducts = array_filter($orders, function($order) {
    return $order['product_type'] === 'Özel Ürün';
});
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <title>Siparişler</title>
    <style>
        body {
            background-image: url('https://r.resimlink.com/M6jwq.jpg');
            font-family: Arial, sans-serif;
            background-size: cover;
            background-position: center;
        }
        .container {
            margin-top: 50px;
        }
        .table-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .table-header {
            font-size: 24px;
            font-weight: bold;
            color: #495057;
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 5px;
        }
        .table th {
            background-color: #0d6efd;
            color: white;
            text-align: center;
        }
        .table tbody tr:hover {
            background-color: #f0f8ff;
            cursor: pointer;
        }
        .status-completed {
            color: #198754;
            font-weight: bold;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .status-cancelled {
            color: #dc3545;
            font-weight: bold;
        }
        .btn-info {
            background-color: #17a2b8;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .btn-info:hover {
            background-color: #138496;
            transform: scale(1.05);
            color: white;
        }
        .modal-header {
            background-color: #0d6efd;
            color: white;
        }
        .modal-content {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        /* Mobil uyumluluk için medya sorguları */
        @media (max-width: 768px) {
            .table th, .table td {
                font-size: 12px;
            }
            .table-container {
                padding: 10px;
            }
            .table-header {
                font-size: 20px;
            }
            .btn-info {
                padding: 3px 6px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Ana Sayfaya Git Butonu -->
    <div class="text-center mb-4">
        <a href="index.php" class="btn btn-primary">Ana Sayfaya Git</a>
    </div>

    <!-- Normal Ürünler Tablosu -->
    <div class="table-container">
        <div class="table-header text-primary">Normal Ürünler</div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ürün Adı</th>
                    <th>Bölge</th>
                    <th>Miktar</th>
                    <th>Toplam</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                    <th>Detaylı Göster</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($normalProducts)): ?>
                    <?php foreach ($normalProducts as $product): ?>
                        <tr>
                            <td class="text-center"><?= htmlspecialchars($product['id']) ?></td>
                            <td><?= htmlspecialchars($product['product_name']) ?></td>
                            <td><?= htmlspecialchars(isset($product['subcategory']) ? $product['subcategory'] : 'N/A') ?></td>
                            <td class="text-center"><?= htmlspecialchars($product['quantity']) ?></td>
                            <td class="text-center">$<?= number_format($product['total'], 2) ?></td>
                            <td class="text-center <?= $product['order_status'] === 'completed' ? 'status-completed' : ($product['order_status'] === 'pending' ? 'status-pending' : 'status-cancelled') ?>">
                                <?= htmlspecialchars($product['order_status']) ?>
                            </td>
                            <td class="text-center"><?= htmlspecialchars($product['created_at']) ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-info" onclick="fetchDetails('Normal Ürün', '<?= htmlspecialchars($product['product_details']) ?>')">
                                    Detaylı Göster
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Henüz siparişiniz yok.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Özel Ürünler Tablosu -->
    <div class="table-container">
        <div class="table-header text-warning">Özel Ürünler</div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ürün Adı</th>
                    <th>Miktar</th>
                    <th>Toplam</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                    <th>Detaylı Göster</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($specialProducts)): ?>
                    <?php foreach ($specialProducts as $product): ?>
                        <tr>
                            <td class="text-center"><?= htmlspecialchars($product['id']) ?></td>
                            <td><?= htmlspecialchars($product['product_name']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($product['quantity']) ?></td>
                            <td class="text-center">$<?= number_format($product['total'], 2) ?></td>
                            <td class="text-center <?= $product['order_status'] === 'completed' ? 'status-completed' : ($product['order_status'] === 'pending' ? 'status-pending' : 'status-cancelled') ?>">
                                <?= htmlspecialchars($product['order_status']) ?>
                            </td>
                            <td class="text-center"><?= htmlspecialchars($product['created_at']) ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-info" onclick="fetchDetails('Özel Ürün', '<?= htmlspecialchars($product['product_details']) ?>')">
                                    Detaylı Göster
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Henüz siparişiniz yok.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Detaylar Modalı -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detaylar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Detay içeriği burada güncellenecek -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function fetchDetails(productType, details) {
        let content;
        if (productType === 'Normal Ürün') {
            content = `<strong>Ürün Detayları:</strong><br>${details}`;
        } else if (productType === 'Özel Ürün') {
            content = `<strong>Özel Ürün Detayları:</strong><br>${details}`;
        } else {
            content = 'Detay bilgisi bulunamadı.';
        }

        document.getElementById('modalContent').innerHTML = content;
        var myModal = new bootstrap.Modal(document.getElementById('detailModal'), {
            keyboard: true
        });
        myModal.show();
    }
</script>
</body>
</html>
