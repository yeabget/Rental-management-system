<?php
session_start();
require "../config/Database.php";

/* AUTH */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$owner_id = $_SESSION['user']['id'];
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($user_id <= 0) {
    header("Location: owner_chat_list.php");
    exit();
}

/* GET RENTER */
$stmt = $db->prepare("SELECT id, fullname FROM users WHERE id = ? AND role='renter'");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: owner_chat_list.php");
    exit();
}

$renter_name = $user['fullname'];
$firstLetter = strtoupper(substr($renter_name, 0, 1));

/* MARK AS READ */
$db->prepare("
    UPDATE messages
    SET is_read = 1
    WHERE sender_id = ? AND receiver_id = ?
")->execute([$user_id, $owner_id]);

/* ================= SEND MESSAGE ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $message = trim($_POST['message'] ?? '');
    $fileName = null;

    /* UPLOAD DIR (SAME FOR BOTH) */
    $uploadDir = "../assets/uploads/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    /* FILE UPLOAD */
    if (!empty($_FILES['file']['name'])) {

        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

        $allowed = ['jpg','jpeg','png','gif','webp','pdf','doc','docx','txt','zip'];

        if (in_array($ext, $allowed)) {

            $fileName = time() . "_" . rand(1000,9999) . "." . $ext;

            move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $fileName);
        }
    }

    /* SAVE MESSAGE */
    if ($message !== '' || $fileName) {

        $stmt = $db->prepare("
            INSERT INTO messages (sender_id, receiver_id, message, file, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $owner_id,
            $user_id,
            $message,
            $fileName
        ]);
    }

    header("Location: owner_chat.php?user_id=$user_id");
    exit();
}

/* ================= LOAD CHAT ================= */
$stmt = $db->prepare("
    SELECT *
    FROM messages
    WHERE (sender_id = ? AND receiver_id = ?)
       OR (sender_id = ? AND receiver_id = ?)
    ORDER BY created_at ASC
");

$stmt->execute([$owner_id, $user_id, $user_id, $owner_id]);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Owner Chat</title>

<link rel="stylesheet" href="../assets/css/owner_chat.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="dashboard">
<?php include "includes/sidebar.php"; ?>

<main class="main">

<div class="chat-page">

<!-- TOP BAR -->
<div class="topbar">

    <a href="owner_chat_list.php" class="back-btn">
        <i class="fa fa-arrow-left"></i>
        Back
    </a>

    <div class="chat-header-user">

        <div class="user-info">
            <h2><?= htmlspecialchars($renter_name) ?></h2>
            <p>Renter</p>
        </div>
    </div>

</div>

<!-- CHAT BOX -->
<div class="chat-box" id="chatBox">

<?php foreach ($messages as $m): ?>

<?php
$file = $m['file'];
$path = "../assets/uploads/" . $file;

$ext = strtolower(pathinfo($file ?? '', PATHINFO_EXTENSION));
$isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);

$isMe = $m['sender_id'] == $owner_id;
?>

<div class="msg <?= $isMe ? 'me' : 'them' ?>">

    <!-- TEXT -->
    <?php if (!empty($m['message'])): ?>
        <div class="text">
            <?= nl2br(htmlspecialchars($m['message'])) ?>
        </div>
    <?php endif; ?>

    <!-- FILE / IMAGE -->
    <?php if (!empty($file) && file_exists($path)): ?>

        <?php if ($isImage): ?>
            <a href="<?= $path ?>" target="_blank">
                <img src="<?= $path ?>" class="chat-img">
            </a>
        <?php else: ?>
            <a href="<?= $path ?>" target="_blank" class="file-link">
                <i class="fa fa-file"></i>
                <?= htmlspecialchars($file) ?>
            </a>
        <?php endif; ?>

    <?php endif; ?>

    <!-- TIME -->
    <div class="time">
        <?= date("h:i A", strtotime($m['created_at'])) ?>
    </div>

</div>

<?php endforeach; ?>

</div>

<!-- INPUT -->
<form method="POST" enctype="multipart/form-data" class="chat-input">

    <label class="file-btn">
        <i class="fa fa-paperclip"></i>
        <input type="file" name="file" id="fileInput" hidden>
    </label>

    <input type="text" name="message" id="messageInput" placeholder="Type message...">

    <button type="submit">
        <i class="fa fa-paper-plane"></i>
    </button>

</form>

</div>

</main>
</div>

<script>
const chatBox = document.getElementById("chatBox");
chatBox.scrollTop = chatBox.scrollHeight;

/* show file name in input */
const fileInput = document.getElementById("fileInput");
const messageInput = document.getElementById("messageInput");

fileInput.addEventListener("change", function () {
    if (this.files.length > 0) {
        messageInput.value = this.files[0].name;
    }
});
</script>

</body>
</html>