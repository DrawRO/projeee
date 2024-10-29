<?php
include 'config.php'; // API ayar dosyası

$params = [
    'key' => $apiKey,
    'cmd' => 'create_transaction',
    'amount' => 0.01, // Ödeme miktarı
    'currency1' => 'BTC',
    'currency2' => 'BTC',
    'buyer_email' => 'example@example.com'
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
    echo "Ödeme Başarılı: " . $result['result']['txn_id'];
} else {
    echo "Hata: " . $result['error'];
}
?>
