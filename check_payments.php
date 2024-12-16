<?php
require 'vendor/autoload.php';
include 'db.php';

use Binance\API;

$api_key = 'Nv1KG4GqVKfDwnN06FeOY86nKVoloRr4OJl80Ru1a8adfrYKqxyiEeu3AvPnutV8';
$api_secret = 'RUOAPdF3tE79DpDsamRHhYuVEaESnmdQ0p02Qc5zvK6mo1QI9B7HSQYcE9TrC4Fx';

$binance = new API($api_key, $api_secret);

// Binance hesabınıza gelen depozito geçmişini alın
try {
    $deposits = $binance->depositHistory();
    file_put_contents('api_log.txt', date('Y-m-d H:i:s') . " - API Yanıtı: " . print_r($deposits, true) . "\n", FILE_APPEND);
    

    // Bekleyen ödeme taleplerini alın
    $stmt = $conn->query("SELECT * FROM payment_requests WHERE status = 'pending'");
    $pending_requests = $stmt->fetchAll();

    foreach ($pending_requests as $request) {
        $user_id = $request['user_id'];
        $requested_amount = $request['amount'];
        $user_wallet = $request['user_wallet'];
        $request_id = $request['id'];

        // Gelen tüm depozitolar arasında dolaşarak eşleşen talebi bul
        foreach ($deposits as $deposit) {
            if (
                $deposit['status'] == 1 && // Status 1, işlemin başarılı olduğunu belirtir
                $deposit['address'] == 'SİSTEMİN_CÜZDAN_ADRESİ' && // Sistemin cüzdan adresi
                $deposit['from'] == $user_wallet && // Kullanıcının cüzdan adresi ile eşleşiyor
                $deposit['amount'] == $requested_amount // Talep edilen miktar ile eşleşiyor
            ) {
                // Kullanıcının bakiyesini güncelle
                $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                $stmt->execute([$requested_amount, $user_id]);

                // Ödeme talebini 'completed' olarak işaretle
                $stmt = $conn->prepare("UPDATE payment_requests SET status = 'completed' WHERE id = ?");
                $stmt->execute([$request_id]);

                echo "Ödeme başarıyla işlendi: Kullanıcı ID $user_id için $requested_amount USDT yatırıldı.\n";
            }
        }
    }
} catch (Exception $e) {
    echo "Bir hata oluştu: ", $e->getMessage();
}
?>
