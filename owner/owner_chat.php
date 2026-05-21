<?php
session_start();
require "../config/Database.php";

if (
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'owner'
) {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$owner_id = $_SESSION['user']['id'];

$user_id = isset($_GET['user_id'])
    ? (int)$_GET['user_id']
    : 0;

/* ================= GET RENTER ================= */

$u = $db->prepare("
    SELECT id, fullname, role
    FROM users
    WHERE id = ?
");

$u->execute([$user_id]);

$user = $u->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['role'] !== 'renter') {
    header("Location: owner_chat_list.php");
    exit();
}

/* ================= RENTER INFO ================= */

$renter_name = trim($user['fullname']);

/* GET FIRST NAME ONLY */
$firstName = explode(" ", $renter_name)[0];

/* GET FIRST LETTER OF RENTER FIRST NAME */
$firstLetter = strtoupper(
    mb_substr($firstName, 0, 1, "UTF-8")
);

/* ================= MARK READ ================= */

$db->prepare("
    UPDATE messages
    SET is_read = 1
    WHERE receiver_id = ?
    AND sender_id = ?
")->execute([$owner_id, $user_id]);

/* ================= SEND MESSAGE ================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $msg = trim($_POST['message'] ?? '');

    $fileName = null;
    $fileType = null;

    /* ================= FILE UPLOAD ================= */

    if (
        isset($_FILES['file']) &&
        $_FILES['file']['error'] == 0 &&
        !empty($_FILES['file']['name'])
    ) {

        $allowed = [
            'jpg',
            'jpeg',
            'png',
            'webp',
            'gif',
            'pdf',
            'doc',
            'docx'
        ];

        $ext = strtolower(
            pathinfo(
                $_FILES['file']['name'],
                PATHINFO_EXTENSION
            )
        );

        if (in_array($ext, $allowed)) {

            $fileName =
                time() .
                "_" .
                rand(1000,9999) .
                "." .
                $ext;

            $uploadPath =
                "../assets/uploads/" . $fileName;

            if (
                move_uploaded_file(
                    $_FILES['file']['tmp_name'],
                    $uploadPath
                )
            ) {

                $fileType = $ext;

            } else {

                die("Failed to upload file.");

            }

        } else {

            die("Invalid file type.");

        }
    }

    /* ================= SAVE MESSAGE ================= */

    if ($msg !== '' || $fileName) {

        $stmt = $db->prepare("
            INSERT INTO messages
            (
                sender_id,
                receiver_id,
                message,
                file,
                file_type,
                created_at
            )
            VALUES (?, ?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $owner_id,
            $user_id,
            $msg,
            $fileName,
            $fileType
        ]);
    }

    header("Location: owner_chat.php?user_id=$user_id");
    exit();
}

/* ================= GET CHAT ================= */

$stmt = $db->prepare("
    SELECT *
    FROM messages
    WHERE (
        sender_id = ?
        AND receiver_id = ?
    )
    OR (
        sender_id = ?
        AND receiver_id = ?
    )
    ORDER BY created_at ASC
");

$stmt->execute([
    $owner_id,
    $user_id,
    $user_id,
    $owner_id
]);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Owner Chat</title>

<link rel="stylesheet"
href="../assets/css/owner_chats.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<div class="dashboard">

    <?php include "includes/sidebar.php"; ?>

    <main class="main">

        <div class="chat-page">

            <!-- TOP BAR -->

            <div class="top-bar">

                <a href="owner_chat_list.php"
                class="back-btn">

                    <i class="fa fa-arrow-left"></i>

                    Back

                </a>

                <div class="chat-header-user">

                    <div class="avatar">
                        <?= htmlspecialchars($firstLetter) ?>
                    </div>

                    <div class="user-info">

                        <h2>
                            <?= htmlspecialchars($renter_name) ?>
                        </h2>

                        <p>Renter</p>

                    </div>

                </div>

            </div>

            <!-- CHAT BOX -->

            <div class="chat-box" id="chatBox">

                <?php foreach($messages as $m): ?>

                    <div class="msg <?= $m['sender_id'] == $owner_id ? 'me' : 'them' ?>">

                        <?php if(!empty($m['message'])): ?>

                            <div class="text">
                                <?= htmlspecialchars($m['message']) ?>
                            </div>

                        <?php endif; ?>

                        <!-- FILE -->

                        <?php if(!empty($m['file'])): ?>

                            <?php
                            $path =
                            "../assets/uploads/" .
                            $m['file'];

                            $ext =
                            strtolower($m['file_type']);
                            ?>

                            <?php if(
                                in_array(
                                    $ext,
                                    ['jpg','jpeg','png','webp','gif']
                                )
                            ): ?>

                                <img
                                src="<?= $path ?>"
                                class="chat-img">

                            <?php else: ?>

                                <a
                                href="<?= $path ?>"
                                target="_blank"
                                class="file-link">

                                    <i class="fa fa-file"></i>

                                    Download File

                                </a>

                            <?php endif; ?>

                        <?php endif; ?>

                        <div class="time">

                            <?= date(
                                "h:i A",
                                strtotime($m['created_at'])
                            ) ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <!-- INPUT -->

            <form
            method="POST"
            enctype="multipart/form-data"
            class="chat-input">

                <!-- FILE BUTTON -->

                <label class="file-btn">

                    <i class="fa fa-paperclip"></i>

                    <input
                    type="file"
                    name="file"
                    id="fileInput"
                    hidden>

                </label>

                <!-- MESSAGE -->

                <input
                type="text"
                name="message"
                id="messageInput"
                placeholder="Type message...">

                <!-- SEND -->

                <button type="submit">

                    <i class="fa fa-paper-plane"></i>

                </button>

            </form>

        </div>

    </main>

</div>

<script>

/* AUTO SCROLL */

let box =
document.getElementById("chatBox");

box.scrollTop = box.scrollHeight;

/* SHOW FILE NAME INSIDE INPUT */

const fileInput =
document.getElementById("fileInput");

const messageInput =
document.getElementById("messageInput");

fileInput.addEventListener("change", function(){

    if(this.files.length > 0){

        messageInput.value =
        this.files[0].name;

    } else {

        messageInput.value = "";

    }
});

</script>

</body>
</html>