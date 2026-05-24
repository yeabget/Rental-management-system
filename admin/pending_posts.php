<?php
session_start();
require "../config/Database.php";

if (
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'admin'
) {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$stmt = $db->prepare("
    SELECT 
        rentals.*,
        users.fullname
    FROM rentals
    INNER JOIN users ON users.id = rentals.owner_id
    WHERE rentals.status = 'pending'
    ORDER BY rentals.id DESC
");

$stmt->execute();
$rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Pending Rentals</title>

<link rel="stylesheet" href="../assets/css/pending_request.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<?php include "includes/sidebar.php"; ?>

<div class="main">
<div class="page-top">
    <div class="welcome-box">
    <h1>Pending Rental Requests</h1>
    <p>Review and manage pending rental requests</p>
</div>
</div>
    <?php if (count($rentals) === 0): ?>
        <div class="empty">
            No pending rental requests found.
        </div>
    <?php endif; ?>

    <div class="grid">

        <?php foreach ($rentals as $r): ?>
            <div class="card">

                <img src="../assets/images/<?= htmlspecialchars($r['image']) ?>" alt="Rental Image">

                <h3><?= htmlspecialchars($r['title']) ?></h3>

                <p>
                    <i class="fas fa-location-dot"></i>
                    <?= htmlspecialchars($r['location']) ?>
                </p>

                <p>
                    <i class="fas fa-user"></i>
                    Owner: <?= htmlspecialchars($r['fullname']) ?>
                </p>

                <p>
                    <strong>ETB <?= number_format($r['price']) ?></strong>
                </p>

                <a href="view_pending.php?id=<?= $r['id'] ?>">
                    <button>
                        View Details
                    </button>
                </a>

            </div>
        <?php endforeach; ?>

    </div>

</div>

</body>
</html>