<?php
include 'config.php';
include 'db.php';

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data['status'] === 'success') {
    $txn_id = $data['txn_id'];
    $stmt = $conn->prepare("UPDATE transactions SET status = 'success' WHERE txn_id = ?");
    $stmt->execute([$txn_id]);
    echo "Ödeme başarılı!";
}
?>
