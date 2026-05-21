<?php
session_start();
require "../config/Database.php";

/* ================= ADMIN AUTH ================= */

if (
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'admin'
) {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

/* ================= GET PENDING RENTALS ================= */

$stmt = $db->prepare("
    SELECT *
    FROM rentals
    WHERE status = 'pending'
    ORDER BY id DESC
");

$stmt->execute();

$rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Pending Rentals</title>

<link rel="stylesheet"
href="../assets/css/pending.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<div class="dashboard">

    <?php include "includes/sidebar.php"; ?>

    <div class="main">

        <div class="page-title">

            <h1>Pending Rentals</h1>

            <p>
                Approve or reject rental requests
            </p>

        </div>

        <?php if(count($rentals) === 0): ?>

            <div class="empty">

                No pending rentals found.

            </div>

        <?php endif; ?>

        <?php foreach($rentals as $rental): ?>

        <div class="card">

            <img
            src="../assets/images/<?= htmlspecialchars($rental['image']) ?>"
            class="image"
            alt="Rental">

            <div class="content">

                <h2>
                    <?= htmlspecialchars($rental['title']) ?>
                </h2>

                <p>
                    <i class="fas fa-location-dot"></i>

                    <?= htmlspecialchars($rental['location']) ?>
                </p>

                <p>
                    <i class="fas fa-layer-group"></i>

                    <?= htmlspecialchars($rental['category']) ?>
                </p>

                <p>
                    <?= htmlspecialchars($rental['description']) ?>
                </p>

                <h3>
                    ETB <?= number_format($rental['price']) ?>
                </h3>

                <div class="actions">

                    <a
                    href="approve_rental.php?id=<?= $rental['id'] ?>"
                    class="approve">

                        <i class="fas fa-check"></i>
                        Approve

                    </a>

                    <a
                    href="reject_rental.php?id=<?= $rental['id'] ?>"
                    class="reject">

                        <i class="fas fa-xmark"></i>
                        Reject

                    </a>

                </div>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

</div>

</body>
</html>