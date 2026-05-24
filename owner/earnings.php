<?php
session_start();

require "../config/Database.php";

if(
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'owner'
){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$owner_id = $_SESSION['user']['id'];


$stmt = $db->prepare("
    SELECT 
        COALESCE(SUM(owner_amount),0)
    FROM payments p
    JOIN rentals r
    ON r.id = p.rental_id
    WHERE r.owner_id = ?
    AND p.status = 'success'
");

$stmt->execute([$owner_id]);

$total = $stmt->fetchColumn();


$stmt = $db->prepare("
    SELECT 
        COALESCE(SUM(owner_amount),0)
    FROM payments p
    JOIN rentals r
    ON r.id = p.rental_id
    WHERE r.owner_id = ?
    AND p.status = 'success'
    AND MONTH(p.created_at) = MONTH(CURRENT_DATE())
    AND YEAR(p.created_at) = YEAR(CURRENT_DATE())
");

$stmt->execute([$owner_id]);

$month = $stmt->fetchColumn();


$stmt = $db->prepare("
    SELECT 
        COALESCE(SUM(owner_amount),0)
    FROM payments p
    JOIN rentals r
    ON r.id = p.rental_id
    WHERE r.owner_id = ?
    AND p.status = 'pending'
");

$stmt->execute([$owner_id]);

$pending = $stmt->fetchColumn();

$stmt = $db->prepare("
    SELECT 
        COALESCE(SUM(admin_amount),0)
    FROM payments p
    JOIN rentals r
    ON r.id = p.rental_id
    WHERE r.owner_id = ?
    AND p.status = 'success'
");

$stmt->execute([$owner_id]);

$commission = $stmt->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Earnings</title>

<link rel="stylesheet"
href="../assets/css/owners_sidebar.css">
<link rel="stylesheet"
href="../assets/css/owner_earning.css">
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<div class="owner-dashboard">
   <?php include "includes/sidebar.php"; ?> 
<main class="owner-main">

    <div class="owner-topbar">

        <div>

            <h1>Earnings</h1>

            <p>
                Real income after platform commission deduction
            </p>

        </div>

    </div>

    <div class="earnings-cards">

        <div class="earn-card">

            <h3>Total Earnings</h3>

            <h2>
                ETB <?= number_format($total, 2) ?>
            </h2>

            <p>
                Amount received after 2% commission
            </p>

        </div>

        <div class="earn-card">

            <h3>This Month</h3>

            <h2>
                ETB <?= number_format($month, 2) ?>
            </h2>

            <p>
                Current month successful payments
            </p>

        </div>

        <div class="earn-card">

            <h3>Pending Payments</h3>

            <h2>
                ETB <?= number_format($pending, 2) ?>
            </h2>

            <p>
                Waiting for payment verification
            </p>

        </div>

        <div class="earn-card">

            <h3>Total Commission</h3>

            <h2>
                ETB <?= number_format($commission, 2) ?>
            </h2>

            <p>
                Total 2% platform commission taken
            </p>

        </div>

    </div>

</main>

</div>

</body>
</html>