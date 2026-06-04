<?php
session_start();
require "../config/Database.php";

/* AUTH CHECK */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'renter') {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$user_id = $_SESSION['user']['id'];
$name = $_SESSION['user']['fullname'];
$firstLetter = strtoupper(substr($name, 0, 1));

$stmt = $db->prepare("
    SELECT 
        u.id,
        u.fullname,

        (
            SELECT message
            FROM messages
            WHERE (sender_id = u.id AND receiver_id = ?)
               OR (sender_id = ? AND receiver_id = u.id)
            ORDER BY created_at DESC
            LIMIT 1
        ) AS last_message,

        (
            SELECT created_at
            FROM messages
            WHERE (sender_id = u.id AND receiver_id = ?)
               OR (sender_id = ? AND receiver_id = u.id)
            ORDER BY created_at DESC
            LIMIT 1
        ) AS last_time,

        (
            SELECT COUNT(*)
            FROM messages
            WHERE sender_id = u.id
              AND receiver_id = ?
              AND is_read = 0
        ) AS unread_count

    FROM users u
    WHERE u.role = 'owner'
    AND u.id IN (
        SELECT sender_id FROM messages WHERE receiver_id = ?
        UNION
        SELECT receiver_id FROM messages WHERE sender_id = ?
    )
    ORDER BY last_time DESC
");

$stmt->execute([
    $user_id, $user_id,
    $user_id, $user_id,
    $user_id,
    $user_id,
    $user_id
]);

$chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | Rent Flow</title>
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/chat_lists.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="dashboard">
    <?php include "includes/sidebar.php"; ?>

    <main class="main">
        <div class="chat-page">
            
            <div class="chat-page-wrapper">
                
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
                                <p>Share and review verification paperwork, vehicle records, or leases using inline attachment workflows.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="promo-footer">
                        <div class="trust-badge">
                            <i class="fa fa-circle-check"></i> 100% Authenticated Communications Grid
                        </div>
                    </div>
                </div>

                <div class="chat-list-panel">
                    <div class="top-bar">
                        <div>
                            <h1>Messages</h1>
                            <p>Chat with owners instantly</p>
                        </div>
                    </div>

                    <div class="chat-list">
                        <?php if (empty($chats)): ?>
                            <p style="color:#6b7280; padding: 20px; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0;">No chats yet.</p>
                        <?php endif; ?>

                        <?php foreach ($chats as $c): ?>
                            <?php $letter = strtoupper(substr($c['fullname'], 0, 1)); ?>

                            <a href="chat.php?owner=<?= $c['id'] ?>" class="chat-card">
                                <div class="avatar"><?= $letter ?></div>

                                <div class="chat-details">
                                    <div class="chat-top-info">
                                        <h3><?= htmlspecialchars($c['fullname']) ?></h3>
                                        <div class="chat-time">
                                            <?php
                                            if ($c['last_time']) {
                                                $date = date("Y-m-d", strtotime($c['last_time']));
                                                $today = date("Y-m-d");
                                                $yesterday = date("Y-m-d", strtotime("-1 day"));

                                                if ($date == $today) {
                                                    echo date("h:i A", strtotime($c['last_time']));
                                                } elseif ($date == $yesterday) {
                                                    echo "Yesterday";
                                                } else {
                                                    echo date("l", strtotime($c['last_time']));
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <p class="<?= ($c['unread_count'] > 0) ? 'unread-text' : '' ?>">
                                        <?php if ($c['unread_count'] > 0): ?>
                                            <strong><?= htmlspecialchars($c['fullname']) ?></strong> sent you a message
                                        <?php else: ?>
                                            <?= htmlspecialchars($c['last_message'] ?? 'No messages yet') ?>
                                        <?php endif; ?>
                                    </p>
                                </div>

                                <?php if ($c['unread_count'] > 0): ?>
                                    <span class="chat-badge">
                                        <?= $c['unread_count'] ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div> </div> <div class="footer-container">
           
        </div>
    </main>
</div>
 <?php include "../includes/footer.php"; ?>
</body>
</html>