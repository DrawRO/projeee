<?php
require 'vendor/autoload.php'; // Composer'ın autoload dosyasını dahil edin

use Binance\API;

// API anahtarlarınızı buraya girin
$api_key = 'Nv1KG4GqVKfDwnN06FeOY86nKVoloRr4OJl80Ru1a8adfrYKqxyiEeu3AvPnutV8';         // Binance API anahtarınızı buraya girin
$api_secret = 'RUOAPdF3tE79DpDsamRHhYuVEaESnmdQ0p02Qc5zvK6mo1QI9B7HSQYcE9TrC4Fx';   // Binance API gizli anahtarınızı buraya girin

// Binance API nesnesini oluşturun
$binance = new API($api_key, $api_secret);

try {
    // USDT transfer geçmişini al
    $deposits = $binance->depositHistory('USDT');
    
    // Yanıtı yazdırarak kontrol edin
    echo "<pre>";
    print_r($deposits);
    echo "</pre>";
    
} catch (Exception $e) {
    // Hata durumunda hata mesajını görüntüleyin
    echo "API Hatası: " . $e->getMessage();
}
