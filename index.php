<?php
require "config/Database.php";

$db = (new Database())->connect();

$stmt = $db->prepare("
    SELECT rentals.*,
           users.fullname AS owner_name
    FROM rentals
    JOIN users ON users.id = rentals.owner_id
    WHERE rentals.status = 'approved'
    ORDER BY rentals.id DESC
    LIMIT 20
");

$stmt->execute();

$rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Flow | Premium Marketplace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/nav.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/home_page.css">
</head>

<body>

<?php include "includes/navbar.php"; ?>

<section class="hero-section">
    <div class="hero-content">
        <span class="hero-badge">
            <i class="fa-solid fa-circle-check"></i> Trusted Rental Marketplace
        </span>

        <h1>
            Find Your Perfect Rental Easily
        </h1>

        <p>
            Explore luxury houses, reliable cars, swift motorcycles, and prime commercial shops from verified owners across the platform.
        </p>

        <div class="hero-buttons">
            <a href="auth/login.php" class="primary-btn">
                Explore Rentals
            </a>
            <a href="auth/register.php" class="secondary-btn">
                Become Owner
            </a>
        </div>
    </div>

    <div class="hero-image">
        <img src="assets/images/bgimg.png" alt="Hero Featured Product">
    </div>
</section>

<section class="stats-section">
    <div class="stat-box">
        <h2>10K+</h2>
        <p>Active Users</p>
    </div>
    <div class="stat-box">
        <h2>5K+</h2>
        <p>Verified Rentals</p>
    </div>
    <div class="stat-box">
        <h2>99%</h2>
        <p>Trusted Listings</p>
    </div>
    <div class="stat-box">
        <h2>24/7</h2>
        <p>Customer Support</p>
    </div>
</section>

<div class="promo-ticker-wrap">
    <div class="promo-ticker">
        <div class="promo-item"><i class="fa-solid fa-fire"></i> Limited Time Offer: Get 10% off your first luxury car rental this month!</div>
        <div class="promo-item"><i class="fa-solid fa-bolt"></i> Flash Deal: Verified commercial shops now listed.</div>
        <div class="promo-item"><i class="fa-solid fa-house-chimney"></i> Premium Suburban Villas added today — Book instant site visits now!</div>
        <div class="promo-item"><i class="fa-solid fa-fire"></i> Get your first luxury car rental this month!</div>
        <div class="promo-item"><i class="fa-solid fa-bolt"></i> Flash Deal: Verified commercial shops now listed.</div>
        <div class="promo-item"><i class="fa-solid fa-house-chimney"></i> Premium Suburban Villas added today — Book instant site visits now!</div>
    </div>
</div>

<section class="rentals-section">
    <div class="section-header">
        <div>
            <span class="section-badge">
                Featured Rentals
            </span>
            <h2>
                Latest Approved Rentals
            </h2>
        </div>
        <a href="auth/login.php" class="explore-btn">
            Explore More
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <div class="market-filters">
        <button class="filter-pill active" data-filter="all"><i class="fa-solid fa-border-all"></i> All Items</button>
        <button class="filter-pill" data-filter="house"><i class="fa-solid fa-house-chimney"></i> Houses</button>
        <button class="filter-pill" data-filter="car"><i class="fa-solid fa-car"></i> Cars</button>
        <button class="filter-pill" data-filter="motorcycle"><i class="fa-solid fa-motorcycle"></i> Bikes</button>
        <button class="filter-pill" data-filter="shop"><i class="fa-solid fa-shop"></i> Shops</button>
    </div>

    <div class="rental-grid">
        <?php foreach($rentals as $index => $r): ?>
        <?php
        $categoryLower = strtolower($r['category']);
        $priceType = ($categoryLower === 'house' || $categoryLower === 'shop') ? "/month" : "/day";
        
        // Define clean icons based on category type
        $catIcon = "fa-layer-group";
        if ($categoryLower === 'house') $catIcon = "fa-house-chimney";
        if ($categoryLower === 'car') $catIcon = "fa-car";
        if ($categoryLower === 'motorcycle') $catIcon = "fa-motorcycle";
        if ($categoryLower === 'shop') $catIcon = "fa-shop";

        // Generate synthetic consistent rating weights for pristine UI presentation
        $ratingScore = number_format(4.5 + (($index * 7) % 6) / 10, 1);
        $reviewCount = (12 + ($index * 13) % 40);
        ?>
        <div class="rental-card" data-category="<?= htmlspecialchars($categoryLower) ?>">
            <div class="rental-image">
                <img src="assets/images/<?= htmlspecialchars($r['image']) ?>" alt="Rental Image">
                <span class="category-tag">
                    <i class="fa-solid <?= $catIcon ?>"></i> <?= htmlspecialchars($r['category']) ?>
                </span>
               
            </div>

            <div class="rental-content">


                <div class="title-price">
                    <h3><?= htmlspecialchars($r['title']) ?></h3>
                    <span class="price-tag">ETB <?= number_format($r['price']) ?><small><?= $priceType ?></small></span>
                </div>
                
                <p class="location">
                    <i class="fa-solid fa-location-dot"></i>
                    <?= htmlspecialchars($r['location']) ?>
                </p>
                
                <p class="description">
                    <?= htmlspecialchars(substr($r['description'], 0, 90)) ?>...
                </p>
                
                <div class="card-footer">
                    <a href="auth/login.php" class="view-btn">View Details</a>
                    
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="features-section">
    <div class="section-title-center">
        <span class="section-badge">Why Choose Us</span>
        <h2>Built For Better Renting Experience</h2>
    </div>
    <div class="features-grid">
        <div class="feature-card">
            <i class="fa-solid fa-shield-halved"></i>
            <h3>Trusted Listings</h3>
            <p>Every rental listing undergoes rigorous safety validation verification checks.</p>
        </div>
        <div class="feature-card">
            <i class="fa-solid fa-comments"></i>
            <h3>Easy Communication</h3>
            <p>Connect safely and chat directly with verified asset owners inside minutes.</p>
        </div>
        <div class="feature-card">
            <i class="fa-solid fa-bolt"></i>
            <h3>Instant Booking</h3>
            <p>Eliminate unnecessary paperwork and deploy immediate booking requests seamlessly.</p>
        </div>
    </div>
</section>

<section class="testimonial-section">
    <div class="section-title-center">
        <span class="section-badge">Testimonials</span>
        <h2>What Our Users Say</h2>
    </div>
    <div class="testimonial-grid">
        <div class="testimonial-card">
            <img src="assets/images/user1.jfif" alt="User">
            <p>“Very smooth experience and trusted owners.”</p>
            <h4>Daniel K.</h4>
        </div>
        <div class="testimonial-card">
            <img src="assets/images/user4.jfif" alt="User">
            <p>“I found my apartment within one day.”</p>
            <h4>Hana M.</h4>
        </div>
        <div class="testimonial-card">
            <img src="assets/images/user3.jfif" alt="User">
            <p>“The best rental platform I have used.”</p>
            <h4>Abel T.</h4>
        </div>
    </div>
</section>

<section class="cta-section">
    <h2>Ready To Start Renting?</h2>
    <p>Join thousands of users exploring premium rentals daily.</p>
    <a href="auth/register.php" class="cta-btn">Get Started</a>
</section>

<?php include "includes/footer.php"; ?>

<script>
document.querySelectorAll('.filter-pill').forEach(pill => {
    pill.addEventListener('click', () => {
        document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
        pill.classList.add('active');
        
        const filterValue = pill.getAttribute('data-filter');
        document.querySelectorAll('.rental-card').forEach(card => {
            if (filterValue === 'all' || card.getAttribute('data-category') === filterValue) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>

</body>
</html>