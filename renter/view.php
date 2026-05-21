<?php
session_start();
require "../config/Database.php";

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$user_id = $_SESSION['user']['id'];

/* UNREAD MESSAGES */
$stmt = $db->prepare("
    SELECT COUNT(*) 
    FROM messages 
    WHERE receiver_id = ? AND is_read = 0
");
$stmt->execute([$user_id]);
$unread = $stmt->fetchColumn();

/* GET PROPERTY */
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid Property");
}

$stmt = $db->prepare("
    SELECT rentals.*, users.fullname
    FROM rentals
    JOIN users ON users.id = rentals.owner_id
    WHERE rentals.id = ?
");

$stmt->execute([$_GET['id']]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$property){
    die("Property not found");
}

$category = strtolower($property['category']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= htmlspecialchars($property['title']) ?></title>

<link rel="stylesheet" href="../assets/css/view_detail.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="dashboard">

<!-- ================= SIDEBAR ================= -->
<aside class="sidebar">

    <h2>RentFlow</h2>

    <a href="../index.php">
        <i class="fa fa-home"></i> Home
    </a>

    <a href="dashboard.php">
        <i class="fa fa-gauge"></i> Dashboard
    </a>

    <a href="saved.php">
        <i class="fa fa-heart"></i> Saved
    </a>

    <a href="chat_list.php">
        <i class="fa fa-comments"></i> Chat

        <?php if($unread > 0): ?>
            <span class="badge"><?= $unread ?></span>
        <?php endif; ?>
    </a>

    <a href="../auth/logout.php">
        <i class="fa fa-right-from-bracket"></i> Logout
    </a>

</aside>

<!-- ================= MAIN ================= -->
<main class="main">

<!-- HERO IMAGE -->
<div class="hero-section">
    <img src="../assets/images/<?= htmlspecialchars($property['image']) ?>" class="hero-image">
</div>

<!-- DETAILS -->
<div class="details-container">

    <!-- HEADER -->
    <div class="details-header">

        <div>
            <h1><?= htmlspecialchars($property['title']) ?></h1>

            <p class="location">
                <i class="fa fa-location-dot"></i>
                <?= htmlspecialchars($property['location']) ?>
            </p>

            <div class="rating">
                <i class="fa fa-star"></i>
                4.9 Rating
            </div>
        </div>

        <!-- PRICE -->
        <div class="price-box">
            <h2>$<?= number_format($property['price'],2) ?></h2>
            <span><?= in_array($category,['house','shop']) ? '/month' : '/day' ?></span>
        </div>

    </div>

    <!-- ================= FEATURES (DYNAMIC) ================= -->
    <div class="features-grid">

        <div class="feature-card">
            <i class="fa fa-house"></i>
            <h3>Category</h3>
            <p><?= htmlspecialchars($property['category']) ?></p>
        </div>

        <?php if(!empty($property['bedrooms'])): ?>
        <div class="feature-card">
            <i class="fa fa-bed"></i>
            <h3>Bedrooms</h3>
            <p><?= $property['bedrooms'] ?></p>
        </div>
        <?php endif; ?>

        <?php if(!empty($property['bathrooms'])): ?>
        <div class="feature-card">
            <i class="fa fa-bath"></i>
            <h3>Bathrooms</h3>
            <p><?= $property['bathrooms'] ?></p>
        </div>
        <?php endif; ?>

        <?php if(!empty($property['size'])): ?>
        <div class="feature-card">
            <i class="fa fa-ruler-combined"></i>
            <h3>Size</h3>
            <p><?= $property['size'] ?> m²</p>
        </div>
        <?php endif; ?>

        <?php if(!empty($property['brand'])): ?>
        <div class="feature-card">
            <i class="fa fa-car"></i>
            <h3>Brand</h3>
            <p><?= htmlspecialchars($property['brand']) ?></p>
        </div>
        <?php endif; ?>

        <?php if(!empty($property['model'])): ?>
        <div class="feature-card">
            <i class="fa fa-cog"></i>
            <h3>Model</h3>
            <p><?= htmlspecialchars($property['model']) ?></p>
        </div>
        <?php endif; ?>

        <?php if(!empty($property['year'])): ?>
        <div class="feature-card">
            <i class="fa fa-calendar"></i>
            <h3>Year</h3>
            <p><?= $property['year'] ?></p>
        </div>
        <?php endif; ?>

        <?php if(!empty($property['fuel'])): ?>
        <div class="feature-card">
            <i class="fa fa-gas-pump"></i>
            <h3>Fuel</h3>
            <p><?= htmlspecialchars($property['fuel']) ?></p>
        </div>
        <?php endif; ?>

        <?php if(!empty($property['seats'])): ?>
        <div class="feature-card">
            <i class="fa fa-chair"></i>
            <h3>Seats</h3>
            <p><?= $property['seats'] ?></p>
        </div>
        <?php endif; ?>

    </div>

    <!-- DESCRIPTION -->
    <div class="section-card">
        <h2>About This Rental</h2>
        <p><?= nl2br(htmlspecialchars($property['description'])) ?></p>
    </div>

    <!-- AMENITIES (ONLY FOR HOUSE/SHOP) -->
    <?php if(in_array($category,['house','shop'])): ?>
    <div class="section-card">

        <h2>Amenities</h2>

        <div class="amenities">

            <?php if(!empty($property['bedrooms'])): ?>
            <div><i class="fa fa-bed"></i> <?= $property['bedrooms'] ?> Bedrooms</div>
            <?php endif; ?>

            <?php if(!empty($property['bathrooms'])): ?>
            <div><i class="fa fa-bath"></i> <?= $property['bathrooms'] ?> Bathrooms</div>
            <?php endif; ?>

            <?php if(!empty($property['size'])): ?>
            <div><i class="fa fa-ruler-combined"></i> <?= $property['size'] ?> m²</div>
            <?php endif; ?>

            <div><i class="fa fa-shield"></i> Secure Property</div>

        </div>

    </div>
    <?php endif; ?>

    <!-- OWNER -->
    <div class="owner-card">

        <div class="owner-info">

            <div class="owner-avatar">
                <?= strtoupper(substr($property['fullname'],0,1)) ?>
            </div>

            <div>
                <h3><?= htmlspecialchars($property['fullname']) ?></h3>
                <p>Property Owner</p>
            </div>

        </div>

        <div class="owner-actions">
            <a href="chat.php?owner=<?= $property['owner_id'] ?>" class="chat-btn">
                <i class="fa fa-comments"></i> Chat Owner
            </a>
        </div>

    </div>

    <!-- ACTIONS -->
    <div class="action-buttons">
<a href="report.php?id=<?= $property['id']; ?>" class="report-btn">
    Report
</a>

        <a href="book.php?id=<?= $property['id'] ?>" class="book-btn">
    Book Now
</a>

        <a href="save_item.php?id=<?= $property['id'] ?>" class="save-btn">
            <i class="fa fa-heart"></i> Save Item
        </a>

    </div>

    <!-- GUARANTEE -->
    <div class="guarantee-box">
        <h2>RentFlow Protection</h2>
        <p>
            This rental is protected by verified ownership and secure booking system.
        </p>
    </div>

</div>

</main>

</div>

</body>
</html>