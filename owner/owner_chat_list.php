<?php
session_start();
require "../config/Database.php";

if(
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'owner'
){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$owner_id = $_SESSION['user']['id'];


$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM messages
    WHERE receiver_id = ?
    AND is_read = 0
");

$stmt->execute([$owner_id]);

$unread = $stmt->fetchColumn();


$stmt = $db->prepare("
    SELECT 
        u.id,
        u.fullname,

        (
            SELECT message
            FROM messages
            WHERE (
                sender_id = u.id
                AND receiver_id = ?
            )
            OR (
                sender_id = ?
                AND receiver_id = u.id
            )
            ORDER BY created_at DESC
            LIMIT 1
        ) AS last_message,

        (
            SELECT created_at
            FROM messages
            WHERE (
                sender_id = u.id
                AND receiver_id = ?
            )
            OR (
                sender_id = ?
                AND receiver_id = u.id
            )
            ORDER BY created_at DESC
            LIMIT 1
        ) AS last_time,

        (
            SELECT sender_id
            FROM messages
            WHERE (
                sender_id = u.id
                AND receiver_id = ?
            )
            OR (
                sender_id = ?
                AND receiver_id = u.id
            )
            ORDER BY created_at DESC
            LIMIT 1
        ) AS last_sender,

        (
            SELECT COUNT(*)
            FROM messages
            WHERE sender_id = u.id
            AND receiver_id = ?
            AND is_read = 0
        ) AS unread_count

    FROM users u

    WHERE u.id IN (

        SELECT sender_id
        FROM messages
        WHERE receiver_id = ?

        UNION

        SELECT receiver_id
        FROM messages
        WHERE sender_id = ?
    )

    ORDER BY last_time DESC
");

$stmt->execute([
    $owner_id,
    $owner_id,

    $owner_id,
    $owner_id,

    $owner_id,
    $owner_id,

    $owner_id,

    $owner_id,
    $owner_id
]);

$chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Messages</title>

<link rel="stylesheet"
href="../assets/css/owners_chat_list.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<div class="dashboard">

    <?php include "includes/sidebar.php"; ?>

    <main class="main">

        <div class="chat-page">

            <!-- TOP -->

            <div class="top-bar">

                <div class="welcome-box">

                    <h1>Messages</h1>

                    <p>
                        Chat with renters instantly
                    </p>

                </div>

            </div>


            <div class="chat-list">

                <?php if(count($chats) > 0): ?>

                    <?php foreach($chats as $c): ?>

                        <?php
                        $chatName =
                        trim($c['fullname']);

                        $chatFirstName =
                        explode(" ", $chatName)[0];

                        $cLetter =
                        strtoupper(
                            mb_substr(
                                $chatFirstName,
                                0,
                                1,
                                "UTF-8"
                            )
                        );
                        ?>

                        <a
                        href="owner_chat.php?user_id=<?= $c['id'] ?>"
                        class="chat-card">


                            <div class="avatar">
                                <?= htmlspecialchars($cLetter) ?>
                            </div>


                            <div class="chat-details">

                                <div class="chat-top-info">

                                    <h3>
                                        <?= htmlspecialchars($chatName) ?>
                                    </h3>

                                    <span class="chat-time">

                                        <?php

                                        if($c['last_time']){

                                            $d = date(
                                                "Y-m-d",
                                                strtotime($c['last_time'])
                                            );

                                            $today =
                                            date("Y-m-d");

                                            $yesterday =
                                            date(
                                                "Y-m-d",
                                                strtotime("-1 day")
                                            );

                                            if($d == $today){

                                                echo date(
                                                    "h:i A",
                                                    strtotime($c['last_time'])
                                                );

                                            }elseif($d == $yesterday){

                                                echo "Yesterday";

                                            }else{

                                                echo date(
                                                    "D",
                                                    strtotime($c['last_time'])
                                                );
                                            }
                                        }

                                        ?>

                                    </span>

                                </div>

                                <div class="chat-bottom-info">

                                    <p>

                                        <?php if($c['last_sender'] == $owner_id): ?>

                                            <span class="sender-badge me">
                                                You
                                            </span>

                                        <?php else: ?>

                                            <span class="sender-badge them">
                                                <?= htmlspecialchars($chatFirstName) ?>
                                            </span>

                                        <?php endif; ?>

                                        <?= htmlspecialchars(
                                            $c['last_message']
                                            ?? 'No messages yet'
                                        ) ?>

                                    </p>

                                    <?php if($c['unread_count'] > 0): ?>

                                        <div class="msg-count">
                                            <?= $c['unread_count'] ?>
                                        </div>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </a>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="empty-state">

                        <i class="fa fa-comments"></i>

                        <h2>No Messages Yet</h2>

                        <p>
                            Start chatting with renters
                            to see conversations here.
                        </p>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </main>

</div>

</body>
</html>