<?php
session_start();
include 'db.php'; // Veritabanı bağlantısı

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['message']) && isset($_SESSION['user_id'])) {
        $message = trim($_POST['message']);
        $user_id = $_SESSION['user_id'];

        if (!empty($message)) {
            try {
                // Kullanıcı adını almak için
                $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();

                if ($user) {
                    $username = $user['username'];

                    // Mesajı veritabanına ekleme
                    $stmt = $conn->prepare("INSERT INTO messages (username, message) VALUES (:username, :message)");
                    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                    $stmt->bindParam(':message', $message, PDO::PARAM_STR);
                    $stmt->execute();

                    echo "success";
                } else {
                    echo "invalid_user";
                }
            } catch (PDOException $e) {
                echo "error";
            }
        } else {
            echo "empty_message";
        }
    } else {
        echo "invalid_request";
    }
} else {
    echo "invalid_method";
}
?>
