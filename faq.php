<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>FAQ - RentFlow</title>

<link rel="stylesheet"
href="assets/css/nav.css">

<link rel="stylesheet"
href="assets/css/footer.css">

<link rel="stylesheet"
href="assets/css/faq.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<?php include "includes/navbar.php"; ?>

<section class="faq-container">

    <div class="faq-header">

        <h1>Frequently Asked Questions</h1>

        <p>
            Find answers about renting, payments,
            bookings, commissions, and account usage
        </p>

        <!-- SEARCH -->

        <div class="faq-search">

            <i class="fa fa-search"></i>

            <input
            type="text"
            id="searchInput"
            placeholder="Search questions...">

        </div>

    </div>


    <div class="faq-list">

        <div class="faq-item">

            <button class="faq-question">

                How does RentFlow work?

                <i class="fa fa-chevron-down"></i>

            </button>

            <div class="faq-answer">

                RentFlow connects renters with verified owners.
                Users can browse rental listings, chat directly,
                make booking requests, and complete rental agreements
                safely through the platform.

            </div>

        </div>

        <div class="faq-item">

            <button class="faq-question">

                Does RentFlow support online payments?

                <i class="fa fa-chevron-down"></i>

            </button>

            <div class="faq-answer">

                Yes. RentFlow supports secure online payments
                through Chapa payment integration.
                Renters can pay their first booking payment
                directly inside the platform.

            </div>

        </div>

        <div class="faq-item">

            <button class="faq-question">

                Does RentFlow charge commission fees?

                <i class="fa fa-chevron-down"></i>

            </button>

            <div class="faq-answer">

                Yes. When payments are completed through
                the RentFlow platform using Chapa,
                a small 2% commission fee is automatically
                deducted from the owner's payment.

            </div>

        </div>

        <div class="faq-item">

            <button class="faq-question">

                Can owners avoid the 2% commission?

                <i class="fa fa-chevron-down"></i>

            </button>

            <div class="faq-answer">

                Yes. Owners and renters can communicate
                through the RentFlow chat system and
                finalize their rental agreement outside
                the platform if they prefer not to use
                the integrated payment system.
                In that case, no platform commission is charged.

            </div>

        </div>

        <div class="faq-item">

            <button class="faq-question">

                How do I become an owner?

                <i class="fa fa-chevron-down"></i>

            </button>

            <div class="faq-answer">

                Create an account and select
                "Owner" during registration.
                After logging in, you can add rental
                listings and manage your properties.

            </div>

        </div>

        <div class="faq-item">

            <button class="faq-question">

                Can renters chat with owners?

                <i class="fa fa-chevron-down"></i>

            </button>

            <div class="faq-answer">

                Yes. RentFlow includes a built-in
                messaging system where renters and
                owners can communicate instantly,
                discuss rental details, negotiate pricing,
                and share agreements.

            </div>

        </div>

        <div class="faq-item">

            <button class="faq-question">

                Is RentFlow free to use?

                <i class="fa fa-chevron-down"></i>

            </button>

            <div class="faq-answer">

                Yes. Browsing rentals, chatting,
                and creating accounts are completely free.
                Only online payments processed through
                the platform include the 2% commission fee.

            </div>

        </div>

        <div class="faq-item">

            <button class="faq-question">

                Are owners verified?

                <i class="fa fa-chevron-down"></i>

            </button>

            <div class="faq-answer">

                RentFlow reviews listings and owner accounts
                to help improve trust and reduce fake listings.
                Users are encouraged to communicate safely
                and verify details before making payments.

            </div>

        </div>

    </div>

</section>

<?php include "includes/footer.php"; ?>

<script>

const items =
document.querySelectorAll(".faq-item");

items.forEach(item => {

    const btn =
    item.querySelector(".faq-question");

    btn.addEventListener("click", () => {

        items.forEach(i => {

            if(i !== item){

                i.classList.remove("active");
            }
        });

        item.classList.toggle("active");
    });
});

const searchInput =
document.getElementById("searchInput");

searchInput.addEventListener("input", function () {

    const value =
    this.value.toLowerCase();

    document
    .querySelectorAll(".faq-item")
    .forEach(item => {

        const text =
        item.innerText.toLowerCase();

        item.style.display =
        text.includes(value)
        ? "block"
        : "none";
    });
});

</script>

</body>
</html>