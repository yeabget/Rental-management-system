<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rental Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   <link rel="stylesheet" href="assets/css/style.css?v=2">
</head>
<body>
<?php include "includes/navbar.php"; ?>
<div>
<section class="hero">
    <div class="hero-content">
        <h1>Find Anything to Rent Easily</h1>
        <p>
          Cars, houses,special occasion clothes, electronics, and many more items are available for rent in one simple platform.  
          Compare prices, explore trusted listings, and choose what fits your needs in seconds.   
          Enjoy a fast, secure, and hassle-free rental experience all in one place.
        </p>
        <div class="hero-buttons">
            <a href="#" class="btn primary">Browse Rentals</a>
            <a href="owner/add_item.php" class="btn secondary">List Property</a>
        </div>
    </div>
    <div class="hero-image">
        <img src="assets/images/bg.png" alt="Hero Image">
    </div>
</section>
<section >
    <h2 class="title">Why Choose Us?</h2>
   <div class="features">
      <div class="feature-card">
        <div class="icon"> 
            <i class="fas fa-star"></i>
        </div>
           <h3>Premium Listings</h3>
           <p>We only list high-quality properties that meet our strict standards for comfort and safety.</p>
       </div>
       <div class="feature-card">
           <div class="icon">
              <i class="fas fa-shield-alt"></i>
           </div>
           <h3>Secure Process</h3>
           <p>Your transactions and data are protected with the latest security protocols and verification.</p>
        </div>
       <div class="feature-card">
           <div class="icon">
               <i class="fas fa-clock"></i>
            </div>
            <h3>Quick Verification</h3>
            <p>List your property in minutes and get verified within hours to start receiving offers.</p>
       </div>
    </div>
</section>
<!--category -->
<div>

        <div>
           
            <div class="category-intro">
                <div>
                 <h2 class="title">Explore Popular Categories</h2>
                <p>
                  Discover a wide range of rental options across various categories. Whether you need a car for a weekend trip, a house for a vacation, or special occasion clothes, we have you covered.
                </p>
                </div>
                <a href="#" class="btn">Browse Rentals <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
     
    
    <section class="categories">
        <div class="category-card">
            <img src="assets/images/carrs.jfif" alt="Cars">
            <div class="icon-circle">
                <i class="fas fa-car"></i>
            </div>
            <h3>Cars</h3>
       </div>
       <div class="category-card">
            <img src="assets/images/home.jfif" alt="Houses">
            <div class="icon-circle">
              <i class="fas fa-home"></i>
            </div>
            <h3>Houses</h3>
       </div>
      <div class="category-card">
          <img src="assets/images/dress.jpg" alt="Special Occasion Clothes">
         <div class="icon-circle">
            <i class="fas fa-tshirt"></i>
         </div>
         <h3>Special Occasion Clothes</h3>
     </div>
     <div class="category-card">
       <img src="assets/images/wedding.jfif" alt="wedding dress">
       <div class="icon-circle">
           <i class="fas fa-person-dress"></i>
        </div>
        <h3>Wedding Dresses</h3>
     </div>
    <div class="category-card">
        <img src="assets/images/hair.webp" alt="human hair">
        <div class="icon-circle">
            <i class="fa-solid fa-spa"></i>
        </div>
        <h3>Human Hair</h3>
    </div>
</section>
</div>
<!-- TESTIMONIALS -->
<section class="testimonials">
      <h2 class="title">What Our Users Say</h2>
      <div class="testimonial-slider">
           <div class="testimonial-track">
                 <div class="testimonial-card">
                 <img src="assets/images/user1.jfif" alt="">
                <p>
                    “Very easy to rent a car within minutes.
                    The platform is simple and professional.”
                </p>
                <h4>Daniel K.</h4>
            </div>
            <div class="testimonial-card">
                <img src="assets/images/user4.jfif" alt="">
                <p>
                    “I found an affordable graduation outfit quickly.
                    Highly recommended platform.”
                </p>
                <h4>Hana M.</h4>
            </div>
            <div class="testimonial-card">
                <img src="assets/images/user3.jfif" alt="">
                <p>
                    “The rental process was smooth and secure.
                    I will definitely use it again.”
                </p>
                <h4>Michael T.</h4>
            </div>
            <div class="testimonial-card">
                <img src="assets/images/user5.jfif" alt="">
                <p>
                    “Amazing customer support and verified listings.
                    Everything worked perfectly.”
                </p>
                <h4>Sara L.</h4>
            </div>
        </div>
    </div>
</section>
<!--content -->
<section class="cta">
    <h2 class="title">Start Renting Today</h2>
    <p>
        Join thousands of users finding affordable rentals every day.
    </p>
    <div class="hero-buttons">
        <a href="#" class="btn primary">Browse Rentals</a>
        <a href="#" class="btn secondary">Become an Owner</a>
    </div>
</section>
<!-- footer -->
<footer class="footer">
    <div class="footer-container">
        <div>
        <div class="footer-logo">
           <img src="assets/images/logo.png" alt="Logo" >
        </div>
            <p>
                Your trusted rental marketplace for everything.
            </p>
        </div>
        <div >
            <h3>Quick Links</h3>
            <ul class="footer-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="rentals.php">Rentals</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
</ul>
        </div>
        <div>
            <h3>Contact</h3>
            <p>Email: ahadu_rental@gmail.com</p>
            <p>Phone: +251 912344689</p>
        </div>
    </div>
    <div class="copyright">
        © 2026 Ahadu Rental. All Rights Reserved.
    </div>
</footer>
</div>
<?php include "includes/footer.php"; ?>
</body>
</html>