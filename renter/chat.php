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

        $extension = strtolower(
            pathinfo($originalName, PATHINFO_EXTENSION)
        );

        $allowed = [
            'jpg','jpeg','png','gif','webp',
            'pdf','doc','docx','txt','zip'
        ];

        if (in_array($extension, $allowed)) {

            $newName =
            time() . "_" . rand(1000,9999) . "." . $extension;

            $target = $uploadDir . $newName;

            if (
                move_uploaded_file(
                    $_FILES['file']['tmp_name'],
                    $target
                )
            ) {
                $file_name = $newName;
            }
        }
    }

    if ($message !== '' || $file_name !== null) {

        $stmt = $db->prepare("
            INSERT INTO messages
            (sender_id, receiver_id, message, file, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $renter_id,
            $owner_id,
            $message,
            $file_name
        ]);
    }

    header("Location: chat.php?owner=$owner_id");
    exit();
}


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
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Chat</title>

<link rel="stylesheet"
href="../assets/css/renter_chats.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<div class="dashboard">

<?php include "includes/sidebar.php"; ?>

<main class="main">

<div class="chat-wrapper">

    <div class="topbar">

        <a href="chat_list.php" class="back-btn">

            <i class="fa fa-arrow-left"></i>

            Back

        </a>
        <div>

        <h2>

            <?= htmlspecialchars($owner['fullname']) ?>

        </h2>

        <p>Property Owner</p>
        </div>
    </div>


    <div class="chat-box" id="chatBox">

        <?php if (empty($messages)): ?>

            <div class="empty-chat">

                No messages yet 

            </div>

        <?php endif; ?>

        <?php foreach ($messages as $m): ?>

            <?php

            $isMe =
            $m['sender_id'] == $renter_id;

            $file =
            $m['file'] ?? '';

            $path =
            "../assets/uploads/" . $file;

            $ext =
            strtolower(
                pathinfo(
                    $file,
                    PATHINFO_EXTENSION
                )
            );

            $isImage = in_array($ext, [
                'jpg',
                'jpeg',
                'png',
                'gif',
                'webp'
            ]);

            ?>

            <div class="message <?= $isMe ? 'me' : 'them' ?>">


                <?php if (!empty($m['message'])): ?>

                    <div class="message-text">

                        <?= nl2br(
                            htmlspecialchars($m['message'])
                        ) ?>

                    </div>

                <?php endif; ?>


                <?php if (!empty($file) && $isImage): ?>

                    <a href="<?= $path ?>" target="_blank">

                        <img
                        src="<?= $path ?>"
                        class="chat-image">

                    </a>

                <?php endif; ?>


                <?php if (!empty($file) && !$isImage): ?>

                    <a
                    href="<?= $path ?>"
                    class="file-box"
                    download>

                        <i class="fa fa-file"></i>

                        <span>

                            <?= htmlspecialchars($file) ?>

                        </span>

                    </a>

                <?php endif; ?>

                <div class="msg-time">

                    <?= date(
                        "h:i A",
                        strtotime($m['created_at'])
                    ) ?>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

    <form
    method="POST"
    enctype="multipart/form-data"
    class="chat-input">


        <label class="file-upload">

            <i class="fa fa-paperclip"></i>

            <input
            type="file"
            name="file"
            id="fileInput">

        </label>


        <input
        type="text"
        name="message"
        id="messageInput"
        placeholder="Type message...">


        <button type="submit">

            <i class="fa fa-paper-plane"></i>

        </button>

    </form>

</div>

</main>
</div>

<script>

const chatBox =
document.getElementById("chatBox");

chatBox.scrollTop =
chatBox.scrollHeight;



const fileInput =
document.getElementById("fileInput");

const messageInput =
document.getElementById("messageInput");

fileInput.addEventListener("change", function(){

    if(this.files.length > 0){

        const fileName =
        this.files[0].name;

        messageInput.placeholder =
        "Selected file: " + fileName;

    }else{

        messageInput.placeholder =
        "Type message...";
    }
});

</script>

</body>
</html>