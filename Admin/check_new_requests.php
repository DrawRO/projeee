<?php
include '../db.php';

// Yeni talep kontrolü
$onay_bekleyen_sorgu = $conn->query("SELECT COUNT(*) FROM payment_requests WHERE status = 'pending'");
$newRequestCount = $onay_bekleyen_sorgu->fetchColumn();

if ($newRequestCount > 0) {
    echo "Yeni talep var";
} else {
    echo "Yeni talep yok";
}
?>
