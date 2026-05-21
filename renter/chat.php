<?php
session_start();
require "../config/Database.php";

/* AUTH */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'renter') {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$renter_id = $_SESSION['user']['id'];
$owner_id  = isset($_GET['owner']) ? (int)$_GET['owner'] : 0;

/* INVALID OWNER */
if ($owner_id <= 0) {
    header("Location: chat_list.php");
    exit();
}

/* OWNER INFO */
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

/* UNREAD MESSAGES */
$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM messages
    WHERE receiver_id = ?
    AND is_read = 0
");
$stmt->execute([$renter_id]);
$unread = $stmt->fetchColumn();

/* MARK AS READ */
$stmt = $db->prepare("
    UPDATE messages
    SET is_read = 1
    WHERE sender_id = ?
    AND receiver_id = ?
");
$stmt->execute([$owner_id, $renter_id]);

/* SEND MESSAGE */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $message = trim($_POST['message']);

    if ($message !== '') {

        $stmt = $db->prepare("
            INSERT INTO messages (sender_id, receiver_id, message, created_at)
            VALUES (?, ?, ?, NOW())
        ");

        $stmt->execute([
            $renter_id,
            $owner_id,
            $message
        ]);
    }

    header("Location: chat.php?owner=$owner_id");
    exit();
}

/* LOAD CHAT */
$stmt = $db->prepare("
    SELECT *
    FROM messages
    WHERE
        (sender_id = ? AND receiver_id = ?)
        OR
        (sender_id = ? AND receiver_id = ?)
    ORDER BY created_at ASC
");

$stmt->execute([
    $renter_id,
    $owner_id,
    $owner_id,
    $renter_id
]);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* PROFILE FIX */
$fullname = $_SESSION['user']['fullname'] ?? 'User';
$firstLetter = strtoupper(substr($fullname, 0, 1));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Chat</title>

<link rel="stylesheet" href="../assets/css/chat.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="dashboard">

<!-- SIDEBAR -->
<aside class="sidebar">

    <div class="sidebar-top">

        <h2>RentFlow</h2>

        <a href="../index.php">
            <i class="fa fa-home"></i>
            Home
        </a>
        <a href="dashboard.php">
            <i class="fa fa-chart-line"></i>
            Dashboard
        </a>

        <a href="saved.php">
            <i class="fa fa-heart"></i> Saved
        </a>

        <a href="chat_list.php" class="active">
            <i class="fa fa-comments"></i> Chat

            <?php if ($unread > 0): ?>
                <span class="badge"><?= $unread ?></span>
            <?php endif; ?>
        </a>

        <a href="../auth/logout.php">
            <i class="fa fa-right-from-bracket"></i> Logout
        </a>

    </div>

    <!-- PROFILE -->
    <div class="sidebar-profile">

        <div class="profile-avatar">
            <?= $firstLetter ?>
        </div>

        <div class="profile-info">
            <h4><?= htmlspecialchars($fullname) ?></h4>
            <p>Renter</p>
        </div>

    </div>

</aside>

<!-- MAIN -->
<main class="main">

<div class="chat-wrapper">

    <!-- HEADER -->
    <div class="chat-header">
        <h2><?= htmlspecialchars($owner['fullname']) ?></h2>
        <p>Property Owner</p>
    </div>

    <!-- CHAT BOX -->
    <div class="chat-box" id="chatBox">

        <?php if (empty($messages)): ?>
            <div class="empty-chat">No messages yet 👋</div>
        <?php endif; ?>

        <?php foreach ($messages as $m): ?>

            <div class="message <?= $m['sender_id'] == $renter_id ? 'me' : 'them' ?>">

                <?= htmlspecialchars($m['message']) ?>

                <div class="msg-time">
                    <?= date("h:i A", strtotime($m['created_at'])) ?>
                </div>

            </div>

        <?php endforeach; ?>

    </div>

    <!-- INPUT -->
    <form method="POST" class="chat-input">

        <input type="text" name="message" placeholder="Type message..." required>

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
</script>

</body>
</html>