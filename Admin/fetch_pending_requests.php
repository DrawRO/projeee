<?php
include '../db.php';

// Bekleyen talepleri al
$onay_bekleyen_sorgu = $conn->query("SELECT * FROM payment_requests WHERE status = 'pending'");

echo "<tr>
        <th>Kullanıcı ID</th>
        <th>Tutar</th>
        <th>Kullanıcı Cüzdanı</th>
        <th>Talep Tarihi</th>
        <th>İşlem</th>
      </tr>";

if ($onay_bekleyen_sorgu->rowCount() > 0) {
    foreach ($onay_bekleyen_sorgu as $talep) {
        echo "<tr id='talep-{$talep['id']}'>";
        echo "<td>" . htmlspecialchars($talep['user_id']) . "</td>";
        echo "<td>" . htmlspecialchars($talep['amount']) . "</td>";
        echo "<td>" . htmlspecialchars($talep['user_wallet']) . "</td>";
        echo "<td>" . htmlspecialchars($talep['created_at']) . "</td>";
        echo "<td>";
        echo "<button onclick='onaylaTalep({$talep['id']})' class='approve'>Onayla</button>";
        echo "<button onclick='reddetTalep({$talep['id']})' class='reject'>Reddet</button>";
        echo "</td></tr>";
    }
} else {
    echo "<tr><td colspan='5' style='text-align:center; color: #555;'>Bekleyen ödeme talebi bulunmamaktadır.</td></tr>";
}
?>
