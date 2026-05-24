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
    LIMIT 10
");

$stmt->execute();

$rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Rent Flow</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet"
href="assets/css/nav.css">

<link rel="stylesheet"
href="assets/css/footer.css">
<link rel="stylesheet"
href="assets/css/home.css">

</head>

<body>

<?php include "includes/navbar.php"; ?>

<section class="hero-section">

    <div class="hero-content">

        <span class="hero-badge">
            Trusted Rental Marketplace
        </span>

        <h1>
            Find Your Perfect Rental Easily
        </h1>

        <p>
            Explore houses, cars, motorcycles and shops
            from trusted owners across the platform.
        </p>

        <div class="hero-buttons">

            <a href="auth/login.php"
            class="primary-btn">

                Explore Rentals

            </a>

            <a href="auth/register.php"
            class="secondary-btn">

                Become Owner

            </a>

        </div>

    </div>

   <div class="hero-image">

    <div class="blob"></div>

    <div class="floating-badge badge1">
        <i class="fa-solid fa-house"></i>
        Houses
    </div>

    <div class="floating-badge badge2">
        <i class="fa-solid fa-car"></i>
        Cars
    </div>

    <div class="floating-badge badge3">
        <i class="fa-solid fa-motorcycle"></i>
        Bikes
    </div>

    <div class="floating-badge badge4">
        <i class="fa-solid fa-shop"></i>
        Shops
    </div>


    <img
    src="assets/images/bg.png"
    alt="Hero">

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

        <a href="auth/login.php"
        class="explore-btn">

            Explore More

            <i class="fa-solid fa-arrow-right"></i>

        </a>

    </div>

    <div class="rental-grid">

        <?php foreach($rentals as $r): ?>

        <?php
        $categoryLower = strtolower($r['category']);

        if ($categoryLower === 'house' || $categoryLower === 'shop') {
            $priceType = "/month";
        } else {
            $priceType = "/day";
        }
        ?>

        <div class="rental-card">

            <div class="rental-image">

                <img
                src="assets/images/<?= htmlspecialchars($r['image']) ?>"
                alt="Rental">

                <span class="category-tag">
                    <?= htmlspecialchars($r['category']) ?>
                </span>

            </div>

            <div class="rental-content">

                <div class="title-price">

                    <h3>
                        <?= htmlspecialchars($r['title']) ?>
                    </h3>

                    <span>
                        ETB <?= number_format($r['price']) ?>
                        <?= $priceType ?>
                    </span>

                </div>

                <p class="location">

                    <i class="fa-solid fa-location-dot"></i>

                    <?= htmlspecialchars($r['location']) ?>

                </p>

                <p class="description">

                    <?= htmlspecialchars(substr($r['description'],0,90)) ?>...

                </p>

                <a href="auth/login.php"
                class="view-btn">

                    View Details

                </a>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

</section>

<section class="features-section">

    <div class="section-title-center">

        <span class="section-badge">
            Why Choose Us
        </span>

        <h2>
            Built For Better Renting Experience
        </h2>

    </div>

    <div class="features-grid">

        <div class="feature-card">

            <i class="fa-solid fa-shield-halved"></i>

            <h3>Trusted Listings</h3>

            <p>
                Every rental is reviewed and approved.
            </p>

        </div>

        <div class="feature-card">

            <i class="fa-solid fa-comments"></i>

            <h3>Easy Communication</h3>

            <p>
                Chat directly with rental owners instantly.
            </p>

        </div>

        <div class="feature-card">

            <i class="fa-solid fa-bolt"></i>

            <h3>Fast Access</h3>

            <p>
                Find rentals within seconds.
            </p>

        </div>

    </div>

</section>

<section class="testimonial-section">

    <div class="section-title-center">

        <span class="section-badge">
            Testimonials
        </span>

        <h2>
            What Our Users Say
        </h2>

    </div>

    <div class="testimonial-grid">

        <div class="testimonial-card">

            <img src="assets/images/user1.jfif">

            <p>
                “Very smooth experience and trusted owners.”
            </p>

            <h4>Daniel K.</h4>

        </div>

        <div class="testimonial-card">

            <img src="assets/images/user4.jfif">

            <p>
                “I found my apartment within one day.”
            </p>

            <h4>Hana M.</h4>

        </div>

        <div class="testimonial-card">

            <img src="assets/images/user3.jfif">

            <p>
                “The best rental platform I have used.”
            </p>

            <h4>Abel T.</h4>

        </div>

    </div>

</section>

<section class="cta-section">

    <h2>
        Ready To Start Renting?
    </h2>

    <p>
        Join thousands of users exploring rentals daily.
    </p>

    <a href="auth/register.php"
    class="cta-btn">

        Get Started

    </a>

</section>

<?php include "includes/footer.php"; ?>

</body>
</html>