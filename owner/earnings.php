<?php
session_start();
require "../config/Database.php";

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner'){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$owner_id = $_SESSION['user']['id'];

/* TOTAL EARNINGS (from approved bookings) */
$stmt = $db->prepare("
    SELECT SUM(rentals.price) AS total
    FROM bookings
    JOIN rentals ON rentals.id = bookings.rental_id
    WHERE rentals.owner_id = ?
    AND bookings.status = 'Approved'
");

$stmt->execute([$owner_id]);
$total = $stmt->fetchColumn();
$total = $total ? $total : 0;

/* THIS MONTH */
$stmt = $db->prepare("
    SELECT SUM(rentals.price) AS total
    FROM bookings
    JOIN rentals ON rentals.id = bookings.rental_id
    WHERE rentals.owner_id = ?
    AND bookings.status = 'Approved'
    AND MONTH(bookings.created_at) = MONTH(CURRENT_DATE())
");

$stmt->execute([$owner_id]);
$month = $stmt->fetchColumn();
$month = $month ? $month : 0;

/* PENDING */
$stmt = $db->prepare("
    SELECT SUM(rentals.price) AS total
    FROM bookings
    JOIN rentals ON rentals.id = bookings.rental_id
    WHERE rentals.owner_id = ?
    AND bookings.status = 'Pending'
");

$stmt->execute([$owner_id]);
$pending = $stmt->fetchColumn();
$pending = $pending ? $pending : 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Earnings</title>

<link rel="stylesheet" href="../assets/css/owner-dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="owner-dashboard">

<!-- SIDEBAR -->
<aside class="owner-sidebar">

    <h2>RentFlow</h2>

    <ul>
        <li><a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a></li>
        <li><a href="listings.php"><i class="fa fa-box"></i> My Listings</a></li>
        <li><a href="bookings.php"><i class="fa fa-calendar-check"></i> Bookings</a></li>
        <li class="active"><a href="earnings.php"><i class="fa fa-wallet"></i> Earnings</a></li>
        <li><a href="../auth/logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a></li>
    </ul>

</aside>

<!-- MAIN -->
<main class="owner-main">

<div class="owner-topbar">
    <div>
        <h1>Earnings</h1>
        <p>Real income from bookings</p>
    </div>
</div>

<div class="earnings-cards">

    <div class="earn-card">
        <h3>Total Earnings</h3>
        <h2>$<?= number_format($total, 2) ?></h2>
    </div>

    <div class="earn-card">
        <h3>This Month</h3>
        <h2>$<?= number_format($month, 2) ?></h2>
    </div>

    <div class="earn-card">
        <h3>Pending</h3>
        <h2>$<?= number_format($pending, 2) ?></h2>
    </div>

</div>

</main>

</div>

</body>
</html>