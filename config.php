<?php
// .env dosyasını yükleme fonksiyonu
function loadEnv($file) {
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

// .env dosyasını yükleyin
loadEnv(__DIR__ . '/.env');

// API anahtarlarını .env dosyasından alın
$apiKey = getenv('API_KEY');
$apiSecret = getenv('API_SECRET');
$apiUrl = 'https://api.coinpayments.net/api.php';

// Çıktıyı test edin
echo "API Key: $apiKey, Secret: $apiSecret";
?>
