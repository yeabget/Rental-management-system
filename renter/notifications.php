<?php
session_start();
require "../config/database.php";

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$user_id = $_SESSION['user']['id'];

$stmt = $db->prepare("
    SELECT * FROM notifications
    WHERE user_id = ?
    ORDER BY created_at DESC
");

$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>

<div class="main">

<h2>Notifications</h2>

<div class="notification-list">

<?php if(empty($notifications)): ?>

    <div class="card">
        <p>No notifications yet.</p>
    </div>

<?php else: ?>

    <?php foreach($notifications as $n): ?>

        <div class="card">
            <p><?= htmlspecialchars($n['message']) ?></p>
            <small><?= $n['created_at'] ?></small>
        </div>

    <?php endforeach; ?>

<?php endif; ?>

</div>

</div>

</body>
</html>