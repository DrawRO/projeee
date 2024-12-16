<?php
require 'vendor/autoload.php';
include 'db.php'; // Veritabanı bağlantısını dahil edin

use Binance\API;

$api_key = 'Nv1KG4GqVKfDwnN06FeOY86nKVoloRr4OJl80Ru1a8adfrYKqxyiEeu3AvPnutV8';
$api_secret = 'RUOAPdF3tE79DpDsamRHhYuVEaESnmdQ0p02Qc5zvK6mo1QI9B7HSQYcE9TrC4Fx';
$api = new API($api_key, $api_secret);

try {
    // Binance üzerinden USDT transferlerini kontrol et
    $deposits = $api->depositHistory('USDT');
    file_put_contents('api_log.txt', date('Y-m-d H:i:s') . " - API Yanıtı: " . print_r($deposits, true) . "\n", FILE_APPEND);
    

    foreach ($deposits as $deposit) {
        if ($deposit['status'] == 1) { // İşlem başarılı
            $txId = $deposit['txId'];
            $amount = $deposit['amount'];
            $wallet_address = $deposit['address'];

            // Ödeme talebini veritabanında kontrol edin
            $stmt = $conn->prepare("SELECT id, user_id FROM payment_requests WHERE user_wallet = ? AND amount = ? AND status = 'pending' LIMIT 1");
            $stmt->execute([$wallet_address, $amount]);
    file_put_contents('transaction_log.txt', date('Y-m-d H:i:s') . " - Ödeme Talebi: " . print_r($payment_request, true) . "\n", FILE_APPEND);
    
            $payment_request = $stmt->fetch();

            if ($payment_request) {
                // Ödemeyi "tamamlandı" olarak güncelle ve tamamlanma tarihini ekle
                $stmt = $conn->prepare("UPDATE payment_requests SET status = 'completed', completed_at = NOW() WHERE id = ?");
                $stmt->execute([$payment_request['id']]);
                
                // Kullanıcı bakiyesini güncelle
                $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    try {
        $stmt->execute([$amount, $user_id]);
        if ($stmt->rowCount() > 0) {
            file_put_contents('transaction_log.txt', date('Y-m-d H:i:s') . " - Kullanıcı ID: $user_id bakiyesi $amount artırıldı\n", FILE_APPEND);
        } else {
            file_put_contents('transaction_log.txt', date('Y-m-d H:i:s') . " - Kullanıcı ID: $user_id bakiyesi güncellenemedi (satır etkilenmedi)\n", FILE_APPEND);
        }
    } catch (PDOException $e) {
        file_put_contents('transaction_log.txt', date('Y-m-d H:i:s') . " - Veritabanı Hatası: " . $e->getMessage() . "\n", FILE_APPEND);
    }
    
                $stmt->execute([$amount, $payment_request['user_id']]);

                // Log veya çıktı yerine daha sağlam bir yapı kullanabilirsiniz
                echo "Ödeme onaylandı ve kullanıcı bakiyesi güncellendi. İşlem ID: $txId, Kullanıcı ID: {$payment_request['user_id']}\n";
            }
        }
    }
} catch (Exception $e) {
    // Hata durumunu kaydedin veya yönetin
    error_log("Ödeme doğrulama hatası: " . $e->getMessage());
    echo "Bir hata oluştu. Daha fazla bilgi için hata günlüğüne bakın.";
}
?>
