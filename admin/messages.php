<?php
session_start();
require "../config/Database.php";

$db = (new Database())->connect();

$stmt = $db->query("SELECT * FROM contact_messages ORDER BY id DESC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages</title>

    <link rel="stylesheet" href="../assets/css/contact_message.css">
</head>

<body>

<div class="container">

    <?php include "includes/sidebar.php"; ?>

    <div class="main">
<div class="page-top">
    <div class="welcome-box">
    <h1>Contact Messages</h1>
    <p>Message sent from users</p>
</div>
</div>
       

        <div class="messages-grid">

            <?php foreach($messages as $m): ?>

                <div class="message-card">

                    <h3><?= htmlspecialchars($m['subject']) ?></h3>

                    <p>
                        <b>Name:</b>
                        <?= htmlspecialchars($m['name']) ?>
                    </p>

                    <p>
                        <b>Email:</b>
                        <?= htmlspecialchars($m['email']) ?>
                    </p>

                    <p>
                        <?= htmlspecialchars($m['message']) ?>
                    </p>

                    <small>
                        <?= htmlspecialchars($m['created_at']) ?>
                    </small>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</div>

</body>
</html>