<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Contact Us - RentFlow</title>
<link rel="stylesheet" href="assets/css/nav.css">
<link rel="stylesheet" href="assets/css/footer.css">
<link rel="stylesheet" href="assets/css/contact.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<?php include "includes/navbar.php"; ?>


<section class="contact-section">

    <div class="contact-info">

        <h1>Contact Us</h1>
        <p>We’re here to help you anytime. Reach out and we’ll respond quickly.</p>

        <div class="info-card">
            <i class="fa fa-phone"></i>
            <div>
                <h3>Phone</h3>
                <p>+251 912 345 678</p>
            </div>
        </div>

        <div class="info-card">
            <i class="fa fa-envelope"></i>
            <div>
                <h3>Email</h3>
                <p>support@rentflow.com</p>
            </div>
        </div>

        <div class="info-card">
            <i class="fa fa-location-dot"></i>
            <div>
                <h3>Location</h3>
                <p>Addis Ababa, Ethiopia</p>
            </div>
        </div>

    </div>

    <div class="contact-form">

        <h2>Send Message</h2>

        <form action="contact_process.php" method="POST">

            <input type="text" name="name" placeholder="Your Name" required>

            <input type="email" name="email" placeholder="Your Email" required>

            <input type="text" name="subject" placeholder="Subject" required>

            <textarea name="message" rows="5" placeholder="Your Message" required></textarea>

            <button type="submit">
                Send Message
            </button>

        </form>

    </div>

</section>

<?php include "includes/footer.php"; ?>

</body>
</html>