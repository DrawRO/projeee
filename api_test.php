<?php
include 'config.php'; // API anahtarlarını içeren dosyayı dahil edin

$params = [
    'key' => $apiKey,
    'cmd' => 'get_callback_address',
    'currency' => 'BTC', // Örnek olarak Bitcoin kullanılıyor
    'version' => 1,
    'format' => 'json'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if ($result['error'] === 'ok') {
    echo "Başarılı! BTC Adresi: " . $result['result']['address'];
} else {
    echo "Hata: " . $result['error'];
}
?>
