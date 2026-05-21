<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$firstLetter = null;

if(isset($_SESSION['user'])){
    $fullname = $_SESSION['user']['fullname'];
    $firstLetter = strtoupper(substr($fullname, 0, 1));
}
?>

<nav class="navbar">

    <!-- LOGO -->
    <div class="logo">
        <a href="/rental-management-system/index.php">
            <img src="/rental-management-system/assets/images/logo.png" alt="Logo">
        </a>
    </div>

    <!-- RIGHT ICONS (MOBILE ONLY) -->
    <div class="nav-icons">
        <?php if(isset($_SESSION['user'])): ?>

            <div class="profile-avatar mobile-avatar">
                <?php echo $firstLetter; ?>
            </div>
        <?php endif; ?>
 <div class="menu-toggle" id="menuToggle">
            <i class="fa-solid fa-bars"></i>
        </div>
    </div>

    <!-- NAV MENU -->
    <div class="nav-right" id="navMenu">

        <ul class="nav-links">
            <li><a href="/rental-management-system/index.php">Home</a></li>
            <li><a href="/rental-management-system/renter/dashboard.php">Rentals</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
        </ul>

        <!-- AUTH -->
        <?php if(isset($_SESSION['user'])): ?>

            <div class="profile-wrapper">

                <div class="profile-avatar desktop-avatar">
                    <?php echo $firstLetter; ?>
                </div>

                <a href="/rental-management-system/auth/logout.php" class="logout-btn">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </a>

            </div>

        <?php else: ?>

            <div class="auth-buttons">

                <a href="/rental-management-system/auth/login.php" class="btn login">
                    Login
                </a>

                <a href="/rental-management-system/auth/register.php" class="btn signup">
                    Sign Up
                </a>

            </div>

        <?php endif; ?>

    </div>

</nav>

<script>
const menuToggle = document.getElementById("menuToggle");
const navMenu = document.getElementById("navMenu");

menuToggle.addEventListener("click", () => {
    navMenu.classList.toggle("active");
});
</script>
</body>
</html>