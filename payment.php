<?php
session_start();
include 'db.php'; // Veritabanı bağlantısı

if (isset($_POST['payment_status'], $_POST['transaction_id'], $_POST['amount'])) {
    $paymentStatus = $_POST['payment_status'];
    $transactionId = $_POST['transaction_id'];
    $amount = $_POST['amount'];
    $userId = $_SESSION['user_id'];

    if ($paymentStatus === 'success') {
        // Kullanıcının bakiyesini güncelle
        $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$amount, $userId]);

        // İşlemi kaydet
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, amount, status, transaction_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $amount, 'success', $transactionId]);

        echo "Ödeme başarıyla tamamlandı ve bakiye güncellendi!";
    } else {
        echo "Ödeme başarısız oldu!";
    }
} else {
    echo "Geçersiz ödeme verileri!";
}
?>
