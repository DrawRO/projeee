<?php
$host = 'localhost';
$db = 'ecommerce'; // phpMyAdmin'de oluşturduğunuz veritabanı adı
$user = 'root'; // Varsayılan XAMPP kullanıcı adı
$pass = ''; // Varsayılan şifre (boş bırakın)

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
