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

/* ================= FIXED QUERY ================= */

$stmt = $db->prepare("
    SELECT 
        b.id AS booking_id,
        b.created_at AS booking_date,
        b.status AS booking_status,

        u.fullname AS renter_name,

        r.id AS rental_id,
        r.title,
        r.image,
        r.price,
        r.category,

        (
            SELECT p.amount
            FROM payments p
            WHERE p.rental_id = b.rental_id
            AND p.user_id = b.user_id
            ORDER BY p.id DESC
            LIMIT 1
        ) AS amount,

        (
            SELECT p.owner_amount
            FROM payments p
            WHERE p.rental_id = b.rental_id
            AND p.user_id = b.user_id
            ORDER BY p.id DESC
            LIMIT 1
        ) AS owner_amount,

        (
            SELECT p.admin_amount
            FROM payments p
            WHERE p.rental_id = b.rental_id
            AND p.user_id = b.user_id
            ORDER BY p.id DESC
            LIMIT 1
        ) AS admin_amount,

        (
            SELECT p.status
            FROM payments p
            WHERE p.rental_id = b.rental_id
            AND p.user_id = b.user_id
            ORDER BY p.id DESC
            LIMIT 1
        ) AS payment_status,

        (
            SELECT p.tx_ref
            FROM payments p
            WHERE p.rental_id = b.rental_id
            AND p.user_id = b.user_id
            ORDER BY p.id DESC
            LIMIT 1
        ) AS tx_ref

    FROM bookings b
    INNER JOIN rentals r ON r.id = b.rental_id
    INNER JOIN users u ON u.id = b.user_id

    WHERE r.owner_id = ?

    ORDER BY b.created_at DESC
");

$stmt->execute([$owner_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Owner Bookings</title>

<link rel="stylesheet" href="../assets/css/owner_bookings.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="dashboard">

<?php include "includes/sidebar.php"; ?>

<main class="main">

    <div class="topbar">
        <div>
            <h1>Bookings</h1>
            <p>Who booked your rentals and payment details</p>
        </div>
    </div>

    <div class="booking-grid">

        <?php if (count($bookings) > 0): ?>

            <?php foreach ($bookings as $b): ?>

                <div class="booking-card">

                    <img 
                        src="../assets/images/<?= htmlspecialchars($b['image'] ?: 'default.png') ?>" 
                        class="booking-img"
                    >

                    <div class="booking-info">

                        <h3><?= htmlspecialchars($b['title']) ?></h3>

                        <p><i class="fa fa-user"></i> <?= htmlspecialchars($b['renter_name']) ?></p>

                        <p><i class="fa fa-calendar"></i> <?= date("d M Y", strtotime($b['booking_date'])) ?></p>

                        <p class="status <?= strtolower($b['payment_status'] ?? 'pending') ?>">
                            <i class="fa fa-circle-check"></i>
                            <?= htmlspecialchars($b['payment_status'] ?? 'Pending') ?>
                        </p>

                        <p>
                            <i class="fa fa-money-bill"></i>
                            Total: ETB <?= number_format($b['amount'] ?? 0, 2) ?>
                        </p>

                        <p>
                            <i class="fa fa-wallet"></i>
                            Owner Earned: ETB <?= number_format($b['owner_amount'] ?? 0, 2) ?>
                        </p>

                        <p>
                            <i class="fa fa-hand-holding-dollar"></i>
                            Admin Commission: ETB <?= number_format($b['admin_amount'] ?? 0, 2) ?>
                        </p>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="empty">
                <i class="fa fa-calendar-check"></i>
                <h3>No Bookings Yet</h3>
                <p>When renters book your properties, they will appear here.</p>
            </div>

        <?php endif; ?>

    </div>

</main>
</div>

</body>
</html>