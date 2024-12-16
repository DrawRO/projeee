<?php
session_start();
include 'db.php';

// Sistem cüzdan adresini ve ağı tanımlayın
$system_wallet_address = "TVzk43i7ezQejvTKYWnN83J3AuUrbdNoaY";
$network = "USDT (TRC20) AĞI"; // veya başka bir ağ

// Kullanıcı oturumundan kullanıcı ID'sini al
$user_id = $_SESSION['user_id'];

// Kullanıcının daha önce bir cüzdan adresi kaydedip kaydetmediğini kontrol edin
$stmt = $conn->prepare("SELECT wallet_address FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$existing_wallet = $stmt->fetchColumn();

$message = ""; // Başarı mesajını depolamak için

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $user_wallet = $existing_wallet ? $existing_wallet : $_POST['user_wallet'];

    // İlk defa cüzdan adresi giriliyorsa, veritabanına kaydedin
    if (!$existing_wallet && isset($_POST['user_wallet'])) {
        $stmt = $conn->prepare("UPDATE users SET wallet_address = ? WHERE id = ?");
        $stmt->execute([$user_wallet, $user_id]);
    }

    // Ödeme talebini veritabanına kaydedin
    $stmt = $conn->prepare("INSERT INTO payment_requests (user_id, amount, user_wallet, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
    $stmt->execute([$user_id, $amount, $user_wallet]);

    // Başarı mesajı ayarla
    $message = "Ödeme talebiniz oluşturuldu. Lütfen aşağıdaki adrese gönderim yapın."; 

    // Ana sayfaya yönlendirin
    header("Location: /index.php"); // Ana sayfa URL'sini buraya ekleyin
    exit(); // Yönlendirme sonrası işlemi sonlandır
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bakiye Yükle</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('https://r.resimlink.com/M6jwq.jpg');
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-size: cover;
        }
        .balance-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            max-width: 90%;
            width: 500px;
            text-align: center;
        }
        .balance-header h2 {
            font-size: 20px;
            color: #343a40;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .wallet-info {
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
            word-wrap: break-word;
        }
        /* Sistem cüzdan adresini daha büyük ve kalın yapın */
        .wallet-info p:first-child {
            font-size: 14px; /* Yazı boyutunu büyütün */
            font-weight: bold; /* Yazıyı kalınlaştırın */
        }
        .btn-submit {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 8px;
            width: 100%;
            padding: 8px;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        .wallet-bilgi {
            color: #dc3545;
            font-weight: bold;
            text-align: center;
            margin-top: 5px;
            margin-bottom: 5px;
        }
        .alert-success, .alert-info {
            font-size: 12px;
        }
        .instructions {
            background-color: #e9ecef;
            padding: 8px;
            border-radius: 8px;
            font-size: 12px;
            text-align: left;
            margin-top: 10px;
        }

        /* Medya sorguları mobil uyumluluk için */
        @media (max-width: 576px) {
            .balance-header h2 {
                font-size: 18px;
            }
            .wallet-info {
                font-size: 10px;
            }
            .btn-submit {
                padding: 6px;
                font-size: 12px;
            }
            .instructions {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>

<div class="balance-container">
    <?php if ($message): ?>
        <div class="alert alert-success">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="balance-header">
        <h2>Bakiye Yükle</h2>
        <p>Lütfen USDT ile bakiye yüklemek için aşağıdaki adımları izleyin.</p>
    </div>

    <div class="wallet-info">
        <p><strong>GÖNDERİCEĞİNİZ CÜZDAN ADRESİi:</strong> <?php echo $system_wallet_address; ?></p>
        <p><strong>Ağ:</strong> <?php echo $network; ?></p>
    </div>

    <form method="POST" action="">
        <?php if (!$existing_wallet): ?>
            <div class="form-group">
                <label for="user_wallet">Kendi Cüzdan Adresiniz:</label>
                <input type="text" class="form-control" id="user_wallet" name="user_wallet" required placeholder="Kendi cüzdan adresinizi girin">
                <div class="wallet-bilgi">Cüzdan kodunuzu doğrulayın</div>
            </div>
        <?php else: ?>
            <div class="user-info">
                <p>Cüzdan Adresiniz: <strong><?php echo htmlspecialchars($existing_wallet); ?></strong></p>
            </div>
        <?php endif; ?>
        
        <div class="form-group">
            <label for="amount">Yüklemek İstediğiniz Tutar (USDT):</label>
            <input type="number" class="form-control" id="amount" name="amount" min="10" required placeholder="Örneğin: 50">
        </div>
        
        <button type="submit" class="btn btn-submit mt-3">Ödeme Yaptım</button>
    </form>
    
    <div class="alert alert-info mt-3">
        <p>Lütfen belirtilen cüzdan adresine USDT gönderin. Daha sonra ÖDEME YAPTIM Butonuna basınız. Ödemeniz onaylandığında bakiyeniz güncellenecektir.</p>
    </div>

    <div class="instructions">
        <h5>Ödeme Yapma Talimatları:</h5>
        <ol>
            <li>Bir kripto para cüzdanı uygulaması (örn. Binance, Trust Wallet) açın.</li>
            <li>Hesabınıza giriş yapın ve "Çekim" veya "Gönder" seçeneğini bulun.</li>
            <li>USDT seçeneğini seçin ve **TRC20** ağı üzerinden gönderim yapacağınızı belirtin.</li>
            <li>Sistem cüzdan adresini kopyalayın ve bu alanı ilgili yere yapıştırın.</li>
            <li>Yüklemek istediğiniz tutarı girin.</li>
            <li>İşlemi onaylayın ve gönderin.</li>
            <li>Ödeme işleminiz onaylandığında bakiyeniz güncellenecektir.</li>
        </ol>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>