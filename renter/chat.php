<?php
session_start();
require "../config/Database.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'renter') {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$renter_id = $_SESSION['user']['id'];
$owner_id  = isset($_GET['owner']) ? (int)$_GET['owner'] : 0;

if ($owner_id <= 0) {
    header("Location: chat_list.php");
    exit();
}

$stmt = $db->prepare("
    SELECT id, fullname
    FROM users
    WHERE id = ?
");
$stmt->execute([$owner_id]);
$owner = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$owner) {
    header("Location: chat_list.php");
    exit();
}

$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM messages
    WHERE receiver_id = ?
    AND is_read = 0
");
$stmt->execute([$renter_id]);
$unread = $stmt->fetchColumn();

$stmt = $db->prepare("
    UPDATE messages
    SET is_read = 1
    WHERE sender_id = ?
    AND receiver_id = ?
");
$stmt->execute([$owner_id, $renter_id]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    $file_name = null;

    if (!empty($_FILES['file']['name'])) {
        $uploadDir = "../assets/uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $originalName = $_FILES['file']['name'];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp','pdf','doc','docx','txt','zip'];

        if (in_array($extension, $allowed)) {
            $newName = time() . "_" . rand(1000,9999) . "." . $extension;
            $target = $uploadDir . $newName;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
                $file_name = $newName;
            }
        }
    }

    if ($message !== '' || $file_name !== null) {
        $stmt = $db->prepare("
            INSERT INTO messages (sender_id, receiver_id, message, file, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$renter_id, $owner_id, $message, $file_name]);
    }

    header("Location: chat.php?owner=$owner_id");
    exit();
}

$stmt = $db->prepare("
    SELECT *
    FROM messages
    WHERE (sender_id = ? AND receiver_id = ?)
       OR (sender_id = ? AND receiver_id = ?)
    ORDER BY created_at ASC
");
$stmt->execute([$renter_id, $owner_id, $owner_id, $renter_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?= htmlspecialchars($owner['fullname']) ?> | Rent Flow</title>
    <link rel="stylesheet" href="../assets/css/renter_chat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="dashboard">


    <main class="main">
        <div class="chat-wrapper">
            
            <div class="chat-promo-panel">
                <div class="promo-header">
                    <span class="brand-tag">RentFlow Premium</span>
                    <h2>Direct Verification & Secure Messaging Workspace</h2>
                    <p>Negotiate rates, verify specifications, and track transaction milestones confidently with encrypted direct-to-owner messaging protocols.</p>
                </div>
                
                <div class="promo-features">
                    <div class="promo-feature-item">
                        <i class="fa fa-shield-halved"></i>
                        <div>
                            <h4>Verified Asset Protection</h4>
                            <p>Conversations are logged under security safeguards to monitor transaction safety.</p>
                        </div>
                    </div>
                    <div class="promo-feature-item">
                        <i class="fa fa-file-signature"></i>
                        <div>
                            <h4>Sleek Document Pipelines</h4>
                            <p>Share and review verification paperwork, vehicle records, or leases using the attachment clip inline.</p>
                        </div>
                    </div>
                </div>
                
                <div class="promo-footer">
                    <div class="trust-badge">
                        <i class="fa fa-circle-check"></i> 100% Authenticated Communications Grid
                    </div>
                </div>
            </div>

            <div class="chat-panel">
                <div class="topbar">
                    <a href="chat_list.php" class="back-btn">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                    <div>
                        <h2><?= htmlspecialchars($owner['fullname']) ?></h2>
                        <p><i class="fa-solid fa-circle" style="color: #10b981; font-size: 8px; margin-right: 4px;"></i> Verified Property Owner</p>
                    </div>
                </div>

                <div class="chat-box" id="chatBox">
                    <?php if (empty($messages)): ?>
                        <div class="empty-chat">
                            <i class="fa-regular fa-comments"></i> No messages yet. Say hello to get started!
                        </div>
                    <?php endif; ?>

                    <?php foreach ($messages as $m): 
                        $isMe = $m['sender_id'] == $renter_id;
                        $file = $m['file'] ?? '';
                        $path = "../assets/uploads/" . $file;
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    ?>
                        <div class="message <?= $isMe ? 'me' : 'them' ?>">
                            
                            <?php if (!empty($m['message'])): ?>
                                <div class="message-text">
                                    <?= nl2br(htmlspecialchars($m['message'])) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($file) && $isImage): ?>
                                <a href="<?= $path ?>" target="_blank" class="chat-img-link">
                                    <img src="<?= $path ?>" class="chat-image" alt="Transmitted Asset Document">
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($file) && !$isImage): ?>
                                <a href="<?= $path ?>" class="file-box" download>
                                    <i class="fa fa-file-arrow-down"></i>
                                    <span><?= htmlspecialchars($file) ?></span>
                                </a>
                            <?php endif; ?>

                            <div class="msg-time">
                                <?= date("h:i A", strtotime($m['created_at'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <form method="POST" enctype="multipart/form-data" class="chat-input" id="chatForm">
                    <label class="file-upload" title="Attach Document / Media">
                        <i class="fa fa-paperclip"></i>
                        <input type="file" name="file" id="fileInput">
                    </label>

                    <input type="text" name="message" id="messageInput" placeholder="Type message..." autocomplete="off">

                    <button type="submit">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                </form>
            </div>

        </div> </main>
</div>

<script>

const chatBox = document.getElementById("chatBox");
chatBox.scrollTop = chatBox.scrollHeight;

const fileInput = document.getElementById("fileInput");
const messageInput = document.getElementById("messageInput");

fileInput.addEventListener("change", function(){
    if(this.files.length > 0){
        const fileName = this.files[0].name;
        messageInput.placeholder = "📎 Selected File: " + fileName;
        messageInput.style.backgroundColor = "#fffbeb";
        messageInput.style.borderColor = "#f59e0b";
    } else {
        messageInput.placeholder = "Type message...";
        messageInput.style.backgroundColor = "";
        messageInput.style.borderColor = "";
    }
});
</script>

</body>
</html>