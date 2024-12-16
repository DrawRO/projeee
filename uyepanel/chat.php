<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/db.php'; // Kök dizinden db.php dosyasını dahil edin

// Kullanıcı oturumu yoksa bir varsayılan kullanıcı kimliği tanımlayın
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = rand(1, 1000); // Rastgele bir kullanıcı kimliği oluştur
}

// AJAX ile gönderilen isteğe göre işlem yap
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'sendMessage') {
        // Mesaj gönderme işlemi
        $username = $_SESSION['username'];
        $message = $_POST['message'];
        
        if (!empty($message)) {
            $sql = "INSERT INTO messages (username, message) VALUES (:username, :message)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['username' => $username, 'message' => $message]);
            echo json_encode(["status" => "success", "message" => "Mesaj gönderildi."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Mesaj boş olamaz."]);
        }
        exit();
    } elseif ($_POST['action'] === 'getMessages') {
        // Mesajları çekme işlemi
        $sql = "SELECT * FROM messages ORDER BY created_at DESC LIMIT 20";
        $stmt = $conn->query($sql);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($messages);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canlı Sohbet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e9ecef; /* Daha açık bir arka plan rengi */
            font-family: Arial, sans-serif;
        }
        #messages {
            height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            position: relative;
            display: flex;
            flex-direction: column;
            max-width: 75%;
        }
        .message.user {
            background-color: #d1e7dd; /* Kullanıcı mesajları için arka plan rengi */
            align-self: flex-end;
            text-align: right;
        }
        .message.other {
            background-color: #f8d7da; /* Diğer kullanıcı mesajları için arka plan rengi */
            align-self: flex-start;
            text-align: left;
        }
        .message p {
            margin: 0;
        }
        .message small {
            font-size: 0.8em;
            color: #6c757d;
            align-self: flex-end; /* Tarih ve saat sağda görünsün */
        }
        .input-group {
            margin-top: 20px;
        }
        h1 {
            margin-bottom: 30px; /* Başlık ve içerik arasındaki boşluk */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Canlı Sohbet</h1>
        <div id="messages" class="d-flex flex-column mb-3"></div>
        <div class="input-group">
            <input type="text" id="messageInput" class="form-control" placeholder="Mesajınızı yazın..." aria-label="Mesajınızı yazın...">
            <button onclick="sendMessage()" class="btn btn-primary">Gönder</button>
        </div>
    </div>

    <script>
        function loadMessages() {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'chat.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const messages = JSON.parse(xhr.responseText);
                    const messagesDiv = document.getElementById('messages');
                    messagesDiv.innerHTML = '';
                    messages.forEach(msg => {
                        const messageDiv = document.createElement('div');
                        messageDiv.classList.add('message', 'other'); // Tüm mesajlar için 'other' sınıfı
                        messageDiv.innerHTML = `<p><strong>${msg.username}:</strong> ${msg.message}</p><small>${msg.created_at}</small>`;
                        messagesDiv.appendChild(messageDiv);
                    });
                    messagesDiv.scrollTop = messagesDiv.scrollHeight; // En son mesaja kaydır
                }
            };
            xhr.send('action=getMessages');
        }

        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value;
            if (message === '') return;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'chat.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status === "success") {
                        messageInput.value = ''; // Giriş alanını temizle
                        loadMessages(); // Yeni mesajları yükleyin
                    } else {
                        alert(response.message);
                    }
                }
            };
            xhr.send('action=sendMessage&message=' + encodeURIComponent(message));
        }

        // Mesajları her 2 saniyede bir güncelle
        setInterval(loadMessages, 2000);
    </script>
</body>
</html>
