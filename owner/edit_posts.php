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

$stmt = $db->prepare("
    SELECT *
    FROM rentals r
    WHERE r.owner_id = ?

    AND r.id NOT IN (
        SELECT p.rental_id
        FROM payments p
        WHERE p.status = 'success'
    )

    ORDER BY r.id DESC
");

$stmt->execute([$owner_id]);

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Posts</title>

<link rel="stylesheet" href="../assets/css/edit_post.css">
<link rel="stylesheet" href="../assets/css/owners_sidebar.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<div class="dashboard">

    <?php include "includes/sidebar.php"; ?>

    <main class="main">

        <div class="topbar">
            <div class="topbar-content">
                <h1>Edit Posts</h1>
                <p>Manage, update and organize your rental listings</p>
            </div>
        </div>

        <div class="property-grid">

        <?php if(count($posts) > 0): ?>

            <?php foreach($posts as $rental): ?>

                <?php
                $image = !empty($rental['image']) ? $rental['image'] : "default.png";

                $priceType =
                    (strtolower($rental['category']) == 'house' ||
                     strtolower($rental['category']) == 'shop')
                    ? '/month'
                    : '/day';
                ?>

                <div class="property-card">

                    <img src="../assets/images/<?= htmlspecialchars($image) ?>"
                         class="property-image"
                         alt="Rental Image">

                    <div class="property-content">

                        <div class="title-price">
                            <h3><?= htmlspecialchars($rental['title']) ?></h3>

                            <span class="price">
                                ETB <?= number_format($rental['price'],2) ?>
                                <?= $priceType ?>
                            </span>
                        </div>

                        <div class="category-badge">
                            <?= ucfirst($rental['category']) ?>
                        </div>

                        <div class="location-approval">

                            <p class="location">
                                <i class="fa fa-location-dot"></i>
                                <?= htmlspecialchars($rental['location']) ?>
                            </p>

                            <p class="status approved">
                                Available
                            </p>

                        </div>

                        <?php if (!empty($rental['description'])): ?>
                            <p class="description">
                                <?= htmlspecialchars(substr($rental['description'], 0, 100)) ?>...
                            </p>
                        <?php endif; ?>

                        <div class="extra-info">

                            <?php if (!empty($rental['bedrooms'])): ?>
                                <span>
                                    <i class="fa fa-bed"></i>
                                    <?= $rental['bedrooms'] ?> Bedrooms
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($rental['bathrooms'])): ?>
                                <span>
                                    <i class="fa fa-bath"></i>
                                    <?= $rental['bathrooms'] ?> Bathrooms
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($rental['brand'])): ?>
                                <span>
                                    <i class="fa fa-car"></i>
                                    <?= htmlspecialchars($rental['brand']) ?>
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($rental['model'])): ?>
                                <span>
                                    <i class="fa fa-tag"></i>
                                    <?= htmlspecialchars($rental['model']) ?>
                                </span>
                            <?php endif; ?>

                        </div>

                        <div class="property-buttons">

                            <a href="edit_rental.php?id=<?= $rental['id'] ?>"
                               class="edit-btn">
                                Edit
                            </a>

                            <a href="delete_rental.php?id=<?= $rental['id'] ?>"
                               class="delete-btn"
                               onclick="return confirm('Delete this rental?')">
                                Delete
                            </a>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="empty-box">
                <i class="fa fa-house"></i>
                <h3>No Available Rentals</h3>
                <p>All your properties are either booked or inactive.</p>
            </div>

        <?php endif; ?>

        </div>

    </main>

</div>

</body>

</html>