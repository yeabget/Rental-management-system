<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Gatekeeper: Ensure only authenticated renters access this layout
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'renter') {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/Database.php";

$db = (new Database())->connect();
$renter_id = $_SESSION['user']['id'];

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$renter_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User database error context missing.");
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

$params = [':search' => "%$search%"];

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Marketplace | RentFlow</title>
    <link rel="stylesheet" href="../assets/css/renters_dashboard.css">
   <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include "includes/sidebar.php"; ?>

<div class="dashboard-viewport-wrapper">
    <main class="main-content-area">
        
        <div class="welcome-hero-banner">
            <div class="hero-text-content">
                <h1>Welcome back, <?= htmlspecialchars(explode(' ', $user['fullname'])[0]) ?>! </h1>
                <p>Explore luxury accommodations, premium high-performance vehicles, and strategic workspaces tailored for you.</p>
            </div>
        </div>

        <div class="search-filter-section">
            <form class="search-bar-form" method="GET">
                <div class="input-icon-group">
                    <i class="fa fa-search search-icon"></i>
                    <input type="text" name="search"
                           placeholder="Search by city, brand, or landmark specification..."
                           value="<?= htmlspecialchars($search) ?>">
                </div>
                <button type="submit" class="search-submit-btn">Search Marketplace</button>
            </form>

            <div class="category-filters-scroll-row">
                <?php
                $cats = ['All','Cars','Houses','Motor Cycles','Shop'];
                foreach ($cats as $c):
                    $icon = '';
                    if($c === 'All') $icon = '<i class="fa-solid fa-border-all"></i>';
                    if($c === 'Cars') $icon = '<i class="fa-solid fa-car"></i>';
                    if($c === 'Houses') $icon = '<i class="fa-solid fa-house"></i>';
                    if($c === 'Motor Cycles') $icon = '<i class="fa-solid fa-motorcycle"></i>';
                    if($c === 'Shop') $icon = '<i class="fa-solid fa-shop"></i>';
                ?>
                <a href="?category=<?= urlencode($c) ?>" class="filter-chip <?= $category == $c ? 'active-filter' : '' ?>">
                    <?= $icon ?> <span><?= $c ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="ecommerce-products-grid">
            <?php if (empty($rentals)): ?>
                <div class="marketplace-empty-state">
                    <div class="empty-icon-frame">
                        <i class="fa-regular fa-folder-open"></i>
                    </div>
                    <h3>No Available Listings Found</h3>
                    <p>We couldn't find matches for your current filters. Try altering your keyword entry or changing categories.</p>
                    <a href="dashboard.php" class="reset-marketplace-btn">Clear All Filters</a>
                </div>
            <?php endif; ?>

            <?php foreach ($rentals as $r): 
                $categoryLower = strtolower($r['category']);
                $priceType = ($categoryLower === 'house' || $categoryLower === 'shop') ? "/mo" : "/day";
            ?>
            <article class="product-retail-card">
                <div class="product-thumbnail-frame">
                    <img src="../assets/images/<?= htmlspecialchars($r['image']) ?>" 
                         class="product-image" 
                         alt="<?= htmlspecialchars($r['title']) ?>"
                         loading="lazy">
                    
                    <span class="badge-tag-category"><?= ucfirst($categoryLower) ?></span>
                    
                    <div class="floating-action-overlay-btn">
                        <a href="chat.php?owner=<?= $r['owner_id'] ?>&rental=<?= $r['id'] ?>" class="overlay-circle-btn" title="Message Merchant">
                            <i class="fa-solid fa-envelope"></i>
                        </a>
                    </div>
                </div>

                <div class="product-details-content">
                    <div class="product-meta-header">
                        <span class="location-pin">
                            <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($r['location']) ?>
                        </span>
                        <span class="quality-badge">
                            <i class="fa-solid fa-shield-check"></i> Verified
                        </span>
                    </div>

                    <h3 class="product-title-heading"><?= htmlspecialchars($r['title']) ?></h3>

                    <?php if(!empty($r['description'])): ?>
                        <p class="product-short-description">
                            <?= htmlspecialchars(substr($r['description'], 0, 85)) ?>...
                        </p>
                    <?php endif; ?>

                    <div class="product-specifications-tray">
                        <?php if($r['category'] == 'house'): ?>
                            <div class="spec-pill"><i class="fa fa-bed"></i> <span><strong><?= $r['bedrooms'] ?></strong> Beds</span></div>
                            <div class="spec-pill"><i class="fa fa-bath"></i> <span><strong><?= $r['bathrooms'] ?></strong> Baths</span></div>
                        <?php elseif(!empty($r['brand'])): ?>
                            <div class="spec-pill"><i class="fa-solid fa-tags"></i> <span><?= htmlspecialchars($r['brand']) ?></span></div>
                            <div class="spec-pill"><i class="fa-solid fa-gears"></i> <span>Premium</span></div>
                        <?php else: ?>
                            <div class="spec-pill"><i class="fa-solid fa-calendar"></i> <span>Instant</span></div>
                            <div class="spec-pill"><i class="fa-solid fa-clock"></i> <span>Flexible</span></div>
                        <?php endif; ?>
                    </div>

                    <div class="product-pricing-footer">
                        <div class="price-structured-container">
                            <span class="currency-label">ETB</span>
                            <span class="price-integer-val"><?= number_format($r['price']) ?></span>
                            <span class="billing-cycle-interval"><?= $priceType ?></span>
                        </div>
                        <a href="view.php?id=<?= $r['id'] ?>" class="product-cta-action-btn">
                            <span>Details</span> <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </main>
</div>
<?php include "../includes/footer.php"; ?>
</body>
</html>