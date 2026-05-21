<?php
session_start();
require "../config/Database.php";

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$user_id = $_SESSION['user']['id'];

/* UNREAD CHAT */
$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM messages
    WHERE receiver_id = ?
    AND is_read = 0
");

$stmt->execute([$user_id]);

$unread = $stmt->fetchColumn();

/* SAVED ITEMS */

$stmt = $db->prepare("
    SELECT rentals.*
    FROM saved_items
    JOIN rentals
    ON rentals.id = saved_items.rental_id
    WHERE saved_items.user_id = ?
    ORDER BY saved_items.id DESC
");

$stmt->execute([$user_id]);

$savedRentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Saved Items</title>

<link rel="stylesheet"
href="../assets/css/saved.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<div class="dashboard">

<!-- SIDEBAR -->
<aside class="sidebar">

    <h2>RentFlow</h2>

    <a href="../index.php">
            <i class="fa fa-home"></i>
            Home
        </a>
        <a href="dashboard.php">
            <i class="fa fa-chart-line"></i>
            Dashboard
        </a>

    <a href="saved.php" class="active">

        <i class="fa fa-heart"></i>

        Saved

    </a>

    <a href="chat_list.php">

        <i class="fa fa-comments"></i>

        Chat

        <?php if($unread > 0): ?>
            <span class="badge">
                <?= $unread ?>
            </span>
        <?php endif; ?>

    </a>

    <a href="../auth/logout.php">
        <i class="fa fa-right-from-bracket"></i>
        Logout
    </a>

</aside>

<!-- MAIN -->
<main class="main">

<h1 class="page-title">
    My Saved Items
</h1>

<div class="property-grid">

<?php if(empty($savedRentals)): ?>

<div class="empty-box">

    <i class="fa fa-heart-crack"></i>

    <h3>No Saved Items</h3>

    <p>
        Your saved rentals will appear here.
    </p>

</div>

<?php endif; ?>

<?php foreach($savedRentals as $r): ?>

<div class="property-card">

    <img
    src="../assets/images/<?= htmlspecialchars($r['image']) ?>"
    class="main-card-image"
    >

    <div class="property-content">

        <div class="title-price">

            <h3>
                <?= htmlspecialchars($r['title']) ?>
            </h3>

            <span>
                $<?= htmlspecialchars($r['price']) ?>/day
            </span>

        </div>

        <p class="location">

            <i class="fa fa-location-dot"></i>

            <?= htmlspecialchars($r['location']) ?>

        </p>

        <div class="action-buttons">
<a href="view.php?id=<?= $r['id'] ?>" class="view-btn">
    View Details
</a>

<a href="unsave.php?id=<?= $r['id'] ?>" class="delete-btn">
    Remove
</a>

        </div>

    </div>

</div>

<?php endforeach; ?>

</div>

</main>

</div>

</body>
</html>