<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0) {
        $userId = $_SESSION['user_id'];
        $amount = $_POST['amount'];

        $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$amount, $userId]);

        header("Location: member_panel.php");
        exit;
    } else {
        echo "Geçersiz bakiye miktarı!";
    }
}
?>