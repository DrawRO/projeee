<?php
include '../db.php';

// Onay Bekleyen Talepler
$onay_bekleyen_sorgu = $conn->query("SELECT * FROM payment_requests WHERE status = 'pending'");

// Onaylanan Talepler
$onaylanan_sorgu = $conn->query("SELECT * FROM payment_requests WHERE status = 'completed'");

// Red Edilen Talepler
$red_edilen_sorgu = $conn->query("SELECT * FROM payment_requests WHERE status = 'failed'");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ödeme Talepleri</title>
    <audio id="notificationSound" src="https://www.myinstants.com/media/sounds/ding-sound-effect_2.mp3" preload="auto"></audio>
    <script>
        function playNotificationSound() {
            document.getElementById('notificationSound').play();
        }

        function checkNewRequests() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "check_new_requests.php", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    if (xhr.responseText.includes("Yeni talep var")) { 
                        playNotificationSound();
                        loadPendingRequests(); 
                    }
                    setTimeout(checkNewRequests, 5000); // 5 saniyede bir kontrol et
                }
            };
            xhr.send();
        }

        function loadPendingRequests() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_pending_requests.php", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById("pendingRequests").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        window.onload = function() {
            loadPendingRequests();
            checkNewRequests();
        };

        function onaylaTalep(id) {
            if (confirm("Bu talebi onaylamak istediğinize emin misiniz?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "talep_onayla.php?id=" + id, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        alert(xhr.responseText);
                        document.getElementById("talep-" + id).remove();
                    }
                };
                xhr.send();
            }
        }

        function reddetTalep(id) {
            if (confirm("Bu talebi reddetmek istediğinize emin misiniz?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "talep_reddet.php?id=" + id, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        alert(xhr.responseText);
                        document.getElementById("talep-" + id).remove();
                    }
                };
                xhr.send();
            }
        }
    </script>
    <style>
        /* Genel stil */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        h2 {
            color: #333;
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        /* Ortak Tablo Stili */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
        }
        
        th {
            color: #fff;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f1f1f1;
        }
        
        /* Duruma Göre Tablo Renkleri */
        .pending th {
            background-color: #ff9800; /* Turuncu - Onay Bekleyen */
        }

        .completed th {
            background-color: #4CAF50; /* Yeşil - Onaylanan */
        }

        .rejected th {
            background-color: #d9534f; /* Kırmızı - Red Edilen */
        }

        /* Buton stil */
        button {
            padding: 8px 12px;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            margin-right: 5px;
            cursor: pointer;
        }
        
        .approve {
            background-color: #4CAF50;
        }
        
        .approve:hover {
            background-color: #45a049;
        }
        
        .reject {
            background-color: #d9534f;
        }
        
        .reject:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>

    <h2>Onay Bekleyen Ödeme Talepleri</h2>
    <table class="pending" id="pendingRequests">
        <tr>
            <th>Kullanıcı ID</th>
            <th>Tutar</th>
            <th>Kullanıcı Cüzdanı</th>
            <th>Talep Tarihi</th>
            <th>İşlem</th>
        </tr>
        <?php if ($onay_bekleyen_sorgu->rowCount() > 0): ?>
            <?php foreach ($onay_bekleyen_sorgu as $talep) : ?>
                <tr id="talep-<?= $talep['id'] ?>">
                    <td><?= htmlspecialchars($talep['user_id']) ?></td>
                    <td><?= htmlspecialchars($talep['amount']) ?></td>
                    <td><?= htmlspecialchars($talep['user_wallet']) ?></td>
                    <td><?= htmlspecialchars($talep['created_at']) ?></td>
                    <td>
                        <button onclick="onaylaTalep(<?= $talep['id'] ?>)" class="approve">Onayla</button>
                        <button onclick="reddetTalep(<?= $talep['id'] ?>)" class="reject">Reddet</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align:center;">Bekleyen ödeme talebi bulunmamaktadır.</td>
            </tr>
        <?php endif; ?>
    </table>

    <h2>Onaylanan Ödeme Talepleri</h2>
    <table class="completed">
        <tr>
            <th>Kullanıcı ID</th>
            <th>Tutar</th>
            <th>Kullanıcı Cüzdanı</th>
            <th>Talep Tarihi</th>
            <th>Onay Tarihi</th>
        </tr>
        <?php if ($onaylanan_sorgu->rowCount() > 0): ?>
            <?php foreach ($onaylanan_sorgu as $talep) : ?>
                <tr>
                    <td><?= htmlspecialchars($talep['user_id']) ?></td>
                    <td><?= htmlspecialchars($talep['amount']) ?></td>
                    <td><?= htmlspecialchars($talep['user_wallet']) ?></td>
                    <td><?= htmlspecialchars($talep['created_at']) ?></td>
                    <td><?= htmlspecialchars($talep['completed_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align:center;">Onaylanan ödeme talebi bulunmamaktadır.</td>
            </tr>
        <?php endif; ?>
    </table>

    <h2>Red Edilen Ödeme Talepleri</h2>
    <table class="rejected">
        <tr>
            <th>Kullanıcı ID</th>
            <th>Tutar</th>
            <th>Kullanıcı Cüzdanı</th>
            <th>Talep Tarihi</th>
            <th>Red Tarihi</th>
        </tr>
        <?php if ($red_edilen_sorgu->rowCount() > 0): ?>
            <?php foreach ($red_edilen_sorgu as $talep) : ?>
                <tr>
                    <td><?= htmlspecialchars($talep['user_id']) ?></td>
                    <td><?= htmlspecialchars($talep['amount']) ?></td>
                    <td><?= htmlspecialchars($talep['user_wallet']) ?></td>
                    <td><?= htmlspecialchars($talep['created_at']) ?></td>
                    <td><?= htmlspecialchars($talep['completed_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align:center;">Red edilen ödeme talebi bulunmamaktadır.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>
