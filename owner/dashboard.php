<?php
session_start();
require "../config/Database.php";

/* ================= AUTH ================= */

if (
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'owner'
) {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$user = $_SESSION['user'];
$owner_id = $user['id'];

$firstLetter =
strtoupper(substr($user['fullname'], 0, 1));

/* ================= TOTAL LISTINGS ================= */

$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM rentals
    WHERE owner_id = ?
");

$stmt->execute([$owner_id]);

$totalListings = $stmt->fetchColumn();

/* ================= BOOKINGS ================= */

$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM bookings
    JOIN rentals
    ON rentals.id = bookings.rental_id
    WHERE rentals.owner_id = ?
");

$stmt->execute([$owner_id]);

$totalBookings = $stmt->fetchColumn();

/* ================= EARNINGS ================= */

$stmt = $db->prepare("
    SELECT SUM(rentals.price)
    FROM bookings
    JOIN rentals
    ON rentals.id = bookings.rental_id
    WHERE rentals.owner_id = ?
");

$stmt->execute([$owner_id]);

$totalEarnings = $stmt->fetchColumn() ?: 0;

/* ================= UNREAD CHATS ================= */

$stmt = $db->prepare("
    SELECT COUNT(*)
    FROM messages
    WHERE receiver_id = ?
    AND is_read = 0
");

$stmt->execute([$owner_id]);

$unreadChats = $stmt->fetchColumn();

/* ================= OWNER RENTALS ================= */

$stmt = $db->prepare("
    SELECT *
    FROM rentals
    WHERE owner_id = ?
    ORDER BY id DESC
");

$stmt->execute([$owner_id]);

$rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Owner Dashboard</title>

<link rel="stylesheet"
href="../assets/css/owner_dashboard.css">

<link rel="stylesheet"
href="../assets/css/owner_sidebar.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<div class="dashboard">

<!-- ================= SIDEBAR ================= -->

<?php include "includes/sidebar.php"; ?>

<!-- ================= MAIN ================= -->

<main class="main">

    <!-- ================= TOPBAR ================= -->

    <div class="topbar">

        <div>

            <h1>
                Welcome,
                <?= htmlspecialchars($user['fullname']) ?> 
            </h1>

            <p>
                Manage your rental business
            </p>

        </div>

        

    </div>

    <!-- ================= STATS ================= -->

    <div class="stats">

        <div class="stat-card">

            <i class="fa fa-building"></i>

            <h2>
                <?= $totalListings ?>
            </h2>

            <p>Total Listings</p>

        </div>

        <div class="stat-card">

            <i class="fa fa-calendar-check"></i>

            <h2>
                <?= $totalBookings ?>
            </h2>

            <p>Bookings</p>

        </div>

        <div class="stat-card">

            <i class="fa fa-dollar-sign"></i>

            <h2>
                $<?= number_format($totalEarnings,2) ?>
            </h2>

            <p>Earnings</p>

        </div>

    </div>

    <!-- ================= TITLE ================= -->

    <div class="section-title">

        <h2>Your Properties</h2>

    </div>

    <!-- ================= PROPERTY GRID ================= -->

    <div class="property-grid">

        <?php if(count($rentals) > 0): ?>

            <?php foreach($rentals as $rental): ?>

                <?php

                $image =
                !empty($rental['image'])
                ? $rental['image']
                : "default.png";

                $priceType = '';

                if(
                    strtolower($rental['category']) == 'house' ||
                    strtolower($rental['category']) == 'shop'
                ){

                    $priceType = '/month';

                }else{

                    $priceType = '/day';
                }

                ?>

                <!-- ================= CARD ================= -->

                <div class="property-card">

                    <!-- IMAGE -->

                    <img
                    src="../assets/images/<?= htmlspecialchars($image) ?>"
                    class="property-image"
                    alt="Rental Image">

                    <!-- CONTENT -->

                    <div class="property-content">

                        <!-- TITLE + PRICE -->

                        <div class="title-price">

                            <h3>
                                <?= htmlspecialchars($rental['title']) ?>
                            </h3>

                            <span class="price">

                                $
                                <?= number_format($rental['price'],2) ?>

                                <?= $priceType ?>

                            </span>

                        </div>

                        <!-- CATEGORY -->

                        <div class="category-badge">

                            <?= ucfirst($rental['category']) ?>

                        </div>

                        <!-- LOCATION -->
<div class="location-approval">
                        <p class="location">

                            <i class="fa fa-location-dot"></i>

                            <?= htmlspecialchars($rental['location']) ?>

                        </p>

                        <!-- STATUS -->

                        <div class="status-box">

                            <?php if($rental['status'] == 'pending'): ?>

                                <p class="status pending">

                                    Pending Admin Approval

                                </p>

                            <?php elseif($rental['status'] == 'approved'): ?>

                                <p class="status approved">

                                    Approved & Live

                                </p>

                            <?php elseif($rental['status'] == 'rejected'): ?>

                                <p class="status rejected">

                                    Rejected By Admin

                                </p>

                                <?php if(!empty($rental['reject_reason'])): ?>

                                    <div class="reject-message">

                                        <strong>Reason:</strong>
                                        <br>

                                        <?= htmlspecialchars($rental['reject_reason']) ?>

                                    </div>

                                <?php endif; ?>

                            <?php endif; ?>

                        </div>
</div>
                        <!-- DESCRIPTION -->

                        <?php if(!empty($rental['description'])): ?>

                            <p class="description">

                                <?= nl2br(
                                    htmlspecialchars(
                                        substr(
                                            $rental['description'],
                                            0,
                                            100
                                        )
                                    )
                                ) ?>

                                ...

                            </p>

                        <?php endif; ?>

                        <!-- EXTRA INFO -->

                        <div class="extra-info">

                            <?php if(!empty($rental['bedrooms'])): ?>

                                <span>

                                    🛏
                                    <?= $rental['bedrooms'] ?>
                                    Bedrooms

                                </span>

                            <?php endif; ?>

                            <?php if(!empty($rental['bathrooms'])): ?>

                                <span>

                                    🚿
                                    <?= $rental['bathrooms'] ?>
                                    Bathrooms

                                </span>

                            <?php endif; ?>

                            <?php if(!empty($rental['brand'])): ?>

                                <span>

                                    🚗
                                    <?= htmlspecialchars($rental['brand']) ?>

                                </span>

                            <?php endif; ?>

                            <?php if(!empty($rental['model'])): ?>

                                <span>

                                    <?= htmlspecialchars($rental['model']) ?>

                                </span>

                            <?php endif; ?>

                        </div>

                        <!-- BUTTONS -->

                        <div class="property-buttons">

                            <a
                            href="edit_rental.php?id=<?= $rental['id'] ?>"
                            class="edit-btn">

                                Edit

                            </a>

                            <a
                            href="delete_rental.php?id=<?= $rental['id'] ?>"
                            class="delete-btn"
                            onclick="return confirm('Delete this rental?')">

                                Delete

                            </a>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <!-- EMPTY -->

            <div class="empty-box">

                <i class="fa fa-house"></i>

                <h3>No Rentals Added</h3>

                <p>
                    Start by adding your first rental property.
                </p>

            </div>

        <?php endif; ?>

    </div>

</main>

</div>

</body>

</html>