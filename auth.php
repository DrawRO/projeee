<?php
session_start();

// Kullanıcı oturumu kontrol edin
if (!isset($_SESSION['user_id'])) {
    // Kullanıcı giriş yapmamışsa login sayfasına yönlendirin
    header("Location: ../login.php");
    exit();
}

// Admin kontrolü
if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin') {
    // Admin değilse erişimi reddedin
    header("Location: ../index.php");
    exit();
}
?>
