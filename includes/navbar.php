<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = basename($_SERVER['PHP_SELF']);

$firstLetter = "";
if (isset($_SESSION['user'])) {
    $fullname = $_SESSION['user']['fullname'] ?? "U";
    $firstLetter = strtoupper(substr($fullname, 0, 1));
}
?>

<div class="overlay" id="overlay"></div>

<nav class="navbar">

    <!-- LOGO -->
    <div class="logo">
        <a href="/rental-management-system/index.php">
          <h1> Rent Flow</h1>
        </a>
    </div>

    <!-- MOBILE ICONS -->
    <div class="nav-icons">

        <?php if(isset($_SESSION['user'])): ?>
            <div class="profile-avatar mobile-avatar">
                <?= $firstLetter ?>
            </div>
        <?php endif; ?>

        <div class="dark-toggle" id="darkToggle">
            <i class="fa-solid fa-moon"></i>
        </div>

        <div class="menu-toggle" id="menuToggle">
            <i class="fa-solid fa-bars"></i>
        </div>

    </div>

    <!-- SIDEBAR MENU -->
    <div class="nav-right" id="navMenu">

        <div class="close-btn" id="closeBtn">
            <i class="fa-solid fa-xmark"></i>
        </div>

        <ul class="nav-links">

            <li>
                <a href="/rental-management-system/index.php"
                   class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">
                    Home
                </a>
            </li>

            <li><a href="/rental-management-system/auth/login.php">Rentals</a></li>
            <li><a href="/rental-management-system/faq.php">FAQ</a></li>
            <li><a href="/rental-management-system/contact.php">Contact</a></li>

        </ul>

        <?php if(isset($_SESSION['user'])): ?>

            <div class="profile-wrapper">

                <div class="profile-avatar desktop-avatar">
                    <?= $firstLetter ?>
                </div>

                <a href="/rental-management-system/auth/logout.php" class="logout-btn">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </a>

            </div>

        <?php else: ?>

            <div class="auth-buttons">
                <a href="/rental-management-system/auth/login.php" class="nav-btn login-btn">Login</a>
                <a href="/rental-management-system/auth/register.php" class="nav-btn signup-btn">Sign Up</a>
            </div>

        <?php endif; ?>

    </div>
</nav>

<script>
const menuToggle = document.getElementById("menuToggle");
const navMenu = document.getElementById("navMenu");
const overlay = document.getElementById("overlay");
const closeBtn = document.getElementById("closeBtn");

function openMenu() {
    navMenu.classList.add("active");
    overlay.classList.add("active");
    document.body.style.overflow = "hidden";
}

function closeMenu() {
    navMenu.classList.remove("active");
    overlay.classList.remove("active");
    document.body.style.overflow = "auto";
}

menuToggle.addEventListener("click", openMenu);
closeBtn.addEventListener("click", closeMenu);
overlay.addEventListener("click", closeMenu);

/* DARK MODE */
const darkToggle = document.getElementById("darkToggle");

if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark");
}

darkToggle.addEventListener("click", () => {
    document.body.classList.toggle("dark");

    localStorage.setItem(
        "theme",
        document.body.classList.contains("dark") ? "dark" : "light"
    );
});
</script>