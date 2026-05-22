<?php
session_start();
require "../config/Database.php";

/* AUTH CHECK */
if(!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) !== 'owner'){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$owner_id = $_SESSION['user']['id'];

/* GET REAL LISTINGS */
$stmt = $db->prepare("
    SELECT * FROM rentals
    WHERE owner_id = ?
    ORDER BY id DESC
");

$stmt->execute([$owner_id]);
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Listings</title>

<link rel="stylesheet" href="../assets/css/owners-dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="owner-dashboard">

<!-- SIDEBAR -->
<aside class="owner-sidebar">

    <h2>RentFlow</h2>

    <ul>
        <li><a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a></li>
        <li class="active"><a href="listings.php"><i class="fa fa-box"></i> My Listings</a></li>
        <li><a href="bookings.php"><i class="fa fa-calendar-check"></i> Bookings</a></li>
        <li><a href="earnings.php"><i class="fa fa-wallet"></i> Earnings</a></li>
        <li><a href="owner_chat_list.php"><i class="fa fa-comments"></i> Chat</a></li>
        <li><a href="../auth/logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a></li>
    </ul>

</aside>

<!-- MAIN -->
<main class="owner-main">

<div class="owner-topbar">

    <div>
        <h1>My Listings</h1>
        <p>Manage all your rental items</p>
    </div>

    <a href="dashboard.php#add-section" class="add-btn">
        <i class="fa fa-plus"></i> Add New
    </a>

</div>

<!-- LISTINGS -->
<div class="listing-grid">

<?php if(empty($listings)): ?>

    <p>No listings yet. Add your first rental item.</p>

<?php else: ?>

    <?php foreach($listings as $item): ?>

        <div class="listing-card">

            <img src="../assets/images/<?= htmlspecialchars($item['image']) ?>">

            <div class="listing-info">

                <h3><?= htmlspecialchars($item['title']) ?></h3>

                <p>$<?= htmlspecialchars($item['price']) ?>/day</p>

                <div class="listing-actions">

                    <button>Edit</button>

                    <a href="delete_rental.php?id=<?= $item['id'] ?>"
                       onclick="return confirm('Delete this item?')">

                        <button class="delete">Delete</button>

                    </a>

                </div>

            </div>

        </div>

    <?php endforeach; ?>

<?php endif; ?>

</div>

</main>

</div>

</body>
</html>