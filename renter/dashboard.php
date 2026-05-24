<?php
session_start();
require "../config/Database.php";


if (
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'renter'
) {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$renter_id = $_SESSION['user']['id'];


$stmt = $db->prepare("
    SELECT *
    FROM users
    WHERE id = ?
");
$stmt->execute([$renter_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found");
}


$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? 'All';



$query = "
SELECT rentals.*,
users.fullname AS owner_name
FROM rentals
JOIN users ON users.id = rentals.owner_id
WHERE rentals.status = 'approved'

AND (
    rentals.title LIKE :search
    OR rentals.location LIKE :search
)

AND NOT EXISTS (
    SELECT 1
    FROM bookings b
    WHERE b.rental_id = rentals.id
    AND b.status IN ('Approved', 'Booked', 'Success')
)

AND NOT EXISTS (
    SELECT 1
    FROM payments p
    WHERE p.rental_id = rentals.id
    AND p.status = 'success'
)
";

$params = [
    ':search' => "%$search%"
];


if ($category !== 'All') {

    $map = [
        'Cars' => 'car',
        'Houses' => 'house',
        'Motor Cycles' => 'motorcycle',
        'Shop' => 'shop'
    ];

    if (isset($map[$category])) {
        $query .= " AND rentals.category = :category ";
        $params[':category'] = $map[$category];
    }
}

$query .= " ORDER BY rentals.id DESC ";

$stmt = $db->prepare($query);
$stmt->execute($params);
$rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $db->prepare("
SELECT COUNT(*)
FROM messages
WHERE receiver_id = ?
AND is_read = 0
");
$stmt->execute([$renter_id]);
$unread = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Renter Dashboard</title>

<link rel="stylesheet" href="../assets/css/renter_dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="dashboard">

<?php include "includes/sidebar.php"; ?>

<main class="main">

<div class="page-title">
    <h1>Welcome, <?= htmlspecialchars($user['fullname']) ?></h1>
    <p>Find available rentals easily</p>
</div>

<form class="search-bar" method="GET">
    <i class="fa fa-search"></i>
    <input type="text" name="search"
        placeholder="Search rentals..."
        value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
</form>

<div class="filters">

<?php
$cats = ['All','Cars','Houses','Motor Cycles','Shop'];

foreach ($cats as $c):
?>

<a href="?category=<?= urlencode($c) ?>"
class="<?= $category == $c ? 'active-filter' : '' ?>">
    <?= $c ?>
</a>

<?php endforeach; ?>

</div>

<div class="property-grid">

<?php if (empty($rentals)): ?>
    <p style="padding:20px;">No available rentals found.</p>
<?php endif; ?>

<?php foreach ($rentals as $r): ?>

<?php
$categoryLower = strtolower($r['category']);

$priceType = ($categoryLower === 'house' || $categoryLower === 'shop')
    ? "/month"
    : "/day";
?>

<div class="property-card">

    <div class="card-image">
        <img src="../assets/images/<?= htmlspecialchars($r['image']) ?>"
             class="main-card-image">

        <div class="card-actions">
            <a href="chat.php?owner=<?= $r['owner_id'] ?>&rental=<?= $r['id'] ?>"
               class="icon-btn">
                <i class="fa fa-comments"></i>
            </a>
        </div>
    </div>

    <div class="property-content">

        <div class="title-price">
            <h3><?= htmlspecialchars($r['title']) ?></h3>

            <span>
                $<?= number_format($r['price']) ?>
                <?= $priceType ?>
            </span>
        </div>

        <p class="location">
            <i class="fa fa-location-dot"></i>
            <?= htmlspecialchars($r['location']) ?>
        </p>

        <p class="status">
            <i class="fa fa-circle-check"></i>
            <?= htmlspecialchars($r['status']) ?>
        </p>

        <?php if(!empty($r['description'])): ?>
            <p class="description">
                <?= htmlspecialchars(substr($r['description'],0,100)) ?>...
            </p>
        <?php endif; ?>

        <div class="property-features">

            <?php if($r['category'] == 'house'): ?>
                <div class="feature-box">
                    <i class="fa fa-bed"></i>
                    <strong><?= $r['bedrooms'] ?></strong> Beds
                </div>

                <div class="feature-box">
                    <i class="fa fa-bath"></i>
                    <strong><?= $r['bathrooms'] ?></strong> Baths
                </div>
            <?php endif; ?>

            <?php if($r['category'] == 'car'): ?>
                <div class="feature-box">
                    <i class="fa fa-car"></i>
                    <strong><?= htmlspecialchars($r['brand']) ?></strong>
                </div>
            <?php endif; ?>

            <?php if($r['category'] == 'motorcycle'): ?>
                <div class="feature-box">
                    <i class="fa fa-motorcycle"></i>
                    <strong><?= htmlspecialchars($r['brand']) ?></strong>
                </div>
            <?php endif; ?>

        </div>

        <a href="view.php?id=<?= $r['id'] ?>" class="view-btn">
            View Details
        </a>

    </div>
</div>

<?php endforeach; ?>

</div>

</main>
</div>

</body>
</html>