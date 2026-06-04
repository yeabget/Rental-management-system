<?php
session_start();
require "../config/Database.php";

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();
$user_id = $_SESSION['user']['id'];

$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM messages
    WHERE receiver_id = ?
    AND is_read = 0
");
$stmt->execute([$user_id]);
$unread = $stmt->fetchColumn();

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Items | Rent Flow</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/saved_item.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
</head>

<body>

<div class="dashboard">
    <?php include "includes/sidebar.php"; ?>

    <div class="content-wrapper">
        <main class="main">
            <div class="page-title">
                <span class="page-badge">
                    <i class="fa-solid fa-heart"></i> Wishlist
                </span>
                <h1>Saved Items</h1>
                <p>Manage and track your preferred premium rentals instantly.</p>
            </div>

            <div class="property-grid">
                <?php if(empty($savedRentals)): ?>
                    <div class="empty-box">
                        <i class="fa-solid fa-heart-crack"></i>
                        <h3>No Saved Items</h3>
                        <p>Your saved rentals will appear here. Explore the marketplace to add listings to your wishlist.</p>
                        <a href="../index.php" class="explore-btn">Browse Marketplace</a>
                    </div>
                <?php endif; ?>

                <?php foreach($savedRentals as $index => $r): ?>
                    <?php
                    $categoryLower = strtolower($r['category']);
                    $priceType = ($categoryLower === 'house' || $categoryLower === 'shop') ? "/month" : "/day";
                    
                    $catIcon = "fa-layer-group";
                    if ($categoryLower === 'house') $catIcon = "fa-house-chimney";
                    if ($categoryLower === 'car') $catIcon = "fa-car";
                    if ($categoryLower === 'motorcycle') $catIcon = "fa-motorcycle";
                    if ($categoryLower === 'shop') $catIcon = "fa-shop";
                    ?>
                    <div class="property-card">
                        <div class="property-image-wrapper">
                            <img src="../assets/images/<?= htmlspecialchars($r['image']) ?>" alt="Rental Image" class="main-card-image">
                            <span class="category-tag">
                                <?= strtoupper(htmlspecialchars($r['category'])) ?>
                            </span>
                        </div>

                        <div class="property-content">
                            <div class="title-price">
                                <h3><?= htmlspecialchars($r['title']) ?></h3>
                                <span class="price-tag">
                                    ETB <?= number_format($r['price']) ?><small><?= $priceType ?></small>
                                </span>
                            </div>

                            <p class="location">
                                <i class="fa-solid fa-location-dot"></i>
                                <?= htmlspecialchars($r['location']) ?>
                            </p>

                            <div class="action-buttons">
                                <a href="view.php?id=<?= $r['id'] ?>" class="view-btn">View Details</a>
                                <a href="unsave.php?id=<?= $r['id'] ?>" class="delete-btn" title="Remove from Wishlist">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>

        <?php include "../includes/footer.php"; ?>
    </div>
</div>

</body>
</html>