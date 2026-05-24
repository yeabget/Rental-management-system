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
    SELECT COUNT(*)
    FROM rentals
    WHERE owner_id = ?
");
$stmt->execute([$owner_id]);
$totalListings = $stmt->fetchColumn();

$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM payments p
    JOIN rentals r ON r.id = p.rental_id
    WHERE r.owner_id = ?
    AND p.status = 'success'
");
$stmt->execute([$owner_id]);
$totalBookings = $stmt->fetchColumn();


$stmt = $db->prepare("
    SELECT COALESCE(SUM(p.owner_amount),0)
    FROM payments p
    JOIN rentals r ON r.id = p.rental_id
    WHERE r.owner_id = ?
    AND p.status = 'success'
");
$stmt->execute([$owner_id]);
$totalEarnings = $stmt->fetchColumn();

$stmt = $db->prepare("
    SELECT *
    FROM rentals r
    WHERE r.owner_id = ?
    AND r.id NOT IN (
        SELECT rental_id
        FROM payments
        WHERE status = 'success'
    )
    ORDER BY r.id DESC
");
$stmt->execute([$owner_id]);
$rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Owner Dashboard</title>

<link rel="stylesheet" href="../assets/css/owners_dashboard.css">
<link rel="stylesheet" href="../assets/css/owners_sidebar.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="dashboard">

<?php include "includes/sidebar.php"; ?>

<main class="main">

    <div class="topbar">
        <div>
            <h1>
                Welcome, <?= htmlspecialchars($_SESSION['user']['fullname']) ?>
            </h1>
            <p>Manage your rental business</p>
        </div>
    </div>

    <div class="stats">

        <div class="stat-card">
            <i class="fa fa-building"></i>
            <h2><?= $totalListings ?></h2>
            <p>Total Listings</p>
        </div>

        <div class="stat-card">
            <i class="fa fa-calendar-check"></i>
            <h2><?= $totalBookings ?></h2>
            <p>Bookings (Paid)</p>
        </div>

        <div class="stat-card">
            <i class="fa fa-money-bill"></i>
            <h2>ETB <?= number_format($totalEarnings,2) ?></h2>
            <p>Earnings (after 2% commission)</p>
        </div>

    </div>

    <div class="section-title">
        <h2>Your Properties</h2>
    </div>

    <div class="property-grid">

        <?php if (count($rentals) > 0): ?>

            <?php foreach ($rentals as $rental): ?>

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

                            <p class="status pending">
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
                                    <i class="fa fa-shower"></i>
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
                <h3>No Rentals Added</h3>
                <p>Start by adding your first property</p>
            </div>

        <?php endif; ?>

    </div>

</main>
</div>

</body>
</html>