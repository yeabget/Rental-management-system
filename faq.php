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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<?php include "includes/navbar.php"; ?>

<section class="faq-container">

    <!-- HEADER -->
    <div class="faq-header">
        <h1>Frequently Asked Questions</h1>
        <p>Find answers about renting, payments, and account usage</p>

        <!-- SEARCH -->
        <div class="faq-search">
            <i class="fa fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search questions...">
        </div>
    </div>

    <!-- FAQ LIST -->
    <div class="faq-list">

        <!-- ITEM -->
        <div class="faq-item">
            <button class="faq-question">
                How does RentFlow work?
                <i class="fa fa-chevron-down"></i>
            </button>
            <div class="faq-answer">
                RentFlow connects renters with verified owners. You can browse listings, chat, and rent directly.
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">
                Is payment done inside the platform?
                <i class="fa fa-chevron-down"></i>
            </button>
            <div class="faq-answer">
                Currently payments are handled between users, but secure payment integration is coming soon.
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">
                How do I become an owner?
                <i class="fa fa-chevron-down"></i>
            </button>
            <div class="faq-answer">
                Register an account and choose "Owner" during signup. Then you can list your properties.
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">
                Can I chat with owners?
                <i class="fa fa-chevron-down"></i>
            </button>
            <div class="faq-answer">
                Yes, RentFlow includes a real-time chat system between renters and owners.
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">
                Is RentFlow free?
                <i class="fa fa-chevron-down"></i>
            </button>
            <div class="faq-answer">
                Yes, using RentFlow is free for both renters and owners.
            </div>
        </div>

    </div>

</section>

<?php include "includes/footer.php"; ?>

<script>
/* ================= ACCORDION ================= */

const items = document.querySelectorAll(".faq-item");

items.forEach(item => {
    const btn = item.querySelector(".faq-question");

    btn.addEventListener("click", () => {

        items.forEach(i => {
            if(i !== item){
                i.classList.remove("active");
            }
        });

        item.classList.toggle("active");
    });
});

/* ================= SEARCH FILTER ================= */

const searchInput = document.getElementById("searchInput");

searchInput.addEventListener("input", function () {

    const value = this.value.toLowerCase();

    document.querySelectorAll(".faq-item").forEach(item => {

        const text = item.innerText.toLowerCase();

        item.style.display = text.includes(value) ? "block" : "none";
    });
});
</script>

</body>
</html>