<?php
$host = 'localhost';
$db = 'testdrawtest'; // phpMyAdmin'de oluşturduğunuz veritabanı adı
$user = 'root'; // Varsayılan XAMPP kullanıcı adı
$pass = ''; // Varsayılan şifre (boş bırakın)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
