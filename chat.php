
<?php
session_start();
include 'db.php';

// Oturum açılmadıysa kullanıcıyı giriş sayfasına yönlendirme
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sohbet Odası</title>
    <link rel="stylesheet" href="css/styles.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <h3>Sohbet Odası</h3>
            <h4>Çevrimiçi Kullanıcılar:</h4>
            <div>
                <?php
                // Çevrimiçi kullanıcıları listelemek için PHP döngüsü
                $online_users = ['User1', 'User2', 'User3']; // Örnek veri, dinamik olarak veritabanından çekilebilir
                foreach ($online_users as $user) {
                    echo "<span>" . htmlspecialchars($user) . "</span> ";
                }
                ?>
            </div>
        </div>
        <div class="chat-box" id="chat-box">
            <?php
            // Mesajları veritabanından çekip göstermek
            $stmt = $pdo->query("SELECT username, message FROM messages ORDER BY id DESC");
            while ($row = $stmt->fetch()) {
                echo "<div><strong>" . htmlspecialchars($row['username']) . ":</strong> " . htmlspecialchars($row['message']) . "</div>";
            }
            ?>
        </div>
        <div class="chat-input">
            <input type="text" id="message" placeholder="Mesajınızı yazın...">
            <button onclick="sendMessage()">Gönder</button>
        </div>
    </div>

    <script>
        function sendMessage() {
            const messageInput = document.getElementById('message');
            const message = messageInput.value.trim();

            if (message !== '') {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "send_message.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        if (xhr.responseText === "success") {
                            const chatBox = document.getElementById('chat-box');
                            const messageDiv = document.createElement('div');
                            messageDiv.textContent = username + ": " + message; // Güvenlik için textContent kullanımı
                            chatBox.insertBefore(messageDiv, chatBox.firstChild);
                            messageInput.value = '';
                            chatBox.scrollTop = chatBox.scrollHeight; // Otomatik kaydırma
                        } else {
                            alert("Mesaj gönderilemedi!");
                        }
                    }
                };
                xhr.send("message=" + encodeURIComponent(message));
            }
        }
    </script>
</body>
</html>
