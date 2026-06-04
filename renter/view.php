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
    WHERE receiver_id = ? AND is_read = 0
");
$stmt->execute([$user_id]);
$unread = $stmt->fetchColumn();

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
    <title><?= htmlspecialchars($property['title']) ?> | Rent Flow</title>
    <link rel="stylesheet" href="../assets/css/renters_view.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="dashboard">
    <?php include "includes/sidebar.php"; ?>

    <div class="content-wrapper">
        <main class="main">
            
            <div class="page-title">
                <span class="page-badge">
                    <i class="fa-solid fa-circle-info"></i> Marketplace Details
                </span>
                <h1>View Rental Details</h1>
                <p>Find your perfect rental today with complete transparency.</p>
            </div>

            <a href="javascript:history.back()" class="back-btn">
                <i class="fa fa-arrow-left"></i> Back to Listings
            </a>

            <div class="hero-section">
                <img src="../assets/images/<?= htmlspecialchars($property['image']) ?>" class="hero-image" alt="Rental Premium Asset">
            </div>

            <div class="details-container">

                <div class="details-header">
                    <div class="header-main-info">
                        <h1><?= htmlspecialchars($property['title']) ?></h1>
                        <p class="location">
                            <i class="fa-solid fa-location-dot"></i>
                            <?= htmlspecialchars($property['location']) ?>
                        </p>
                    </div>

                    <div class="price-box">
                        <h2>ETB <?= number_format($property['price']) ?></h2>
                        <span><?= in_array($category,['house','shop']) ? '/month' : '/day' ?></span>
                    </div>
                </div>

                <div class="features-grid">
                    <div class="feature-card">
                        <i class="fa fa-house"></i>
                        <h3>Category</h3>
                        <p><?= htmlspecialchars(ucfirst($property['category'])) ?></p>
                    </div>

                    <?php if(!empty($property['bedrooms'])): ?>
                    <div class="feature-card">
                        <i class="fa fa-bed"></i>
                        <h3>Bedrooms</h3>
                        <p><?= $property['bedrooms'] ?> Beds</p>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($property['bathrooms'])): ?>
                    <div class="feature-card">
                        <i class="fa fa-bath"></i>
                        <h3>Bathrooms</h3>
                        <p><?= $property['bathrooms'] ?> Baths</p>
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
                        <p><?= $property['seats'] ?> Positions</p>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="section-card">
                    <h2>About This Rental</h2>
                    <p class="description-text"><?= nl2br(htmlspecialchars($property['description'])) ?></p>
                </div>

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
                        <div><i class="fa fa-ruler-combined"></i> <?= $property['size'] ?> m² Space Area</div>
                        <?php endif; ?>
                        <div><i class="fa fa-shield"></i> Secure Guard Infrastructure</div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="owner-card">
                    <div class="owner-info">
                        <div class="owner-avatar">
                            <?= strtoupper(substr($property['fullname'],0,1)) ?>
                        </div>
                        <div>
                            <h3><?= htmlspecialchars($property['fullname']) ?></h3>
                            <p>Verified Property Owner</p>
                        </div>
                    </div>
                    <div class="owner-actions">
                        <a href="chat.php?owner=<?= $property['owner_id'] ?>" class="chat-btn">
                            <i class="fa fa-comments"></i> Chat Owner
                        </a>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="report.php?id=<?= $property['id']; ?>" class="report-btn">
                        <i class="fa-solid fa-flag"></i> Report
                    </a>
                    <a href="save_item.php?id=<?= $property['id'] ?>" class="save-btn">
                        <i class="fa-regular fa-heart"></i> Save Listing
                    </a>
                    <a href="book.php?id=<?= $property['id'] ?>" class="book-btn">
                        Book Selection Now
                    </a>
                </div>

                <div class="guarantee-box">
                    <h2><i class="fa-solid fa-shield-halved"></i> RentFlow Protection Guarantee</h2>
                    <p>This rental is structurally verified via physical asset authentication tracking pipelines to prevent user marketplace listing risk vectors.</p>
                </div>

            </div>
        </main>
        
        <?php include "../includes/footer.php"; ?>
    </div>
</div>

</body>
</html>