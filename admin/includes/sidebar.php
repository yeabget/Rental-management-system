<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$current = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Panel</title>

    <link rel="stylesheet" href="../assets/css/admin_sidebar.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<button class="menu-toggle" id="menuToggle">
    <i class="fas fa-bars"></i>
</button>

<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="sidebar" id="sidebar">

    <div class="sidebar-top">

        <div class="sidebar-header">
            <h2 class="sidebar-title">Admin Panel</h2>

            <button class="close-btn" id="closeSidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <a href="../index.php" class="<?= ($current == 'index.php') ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Home
        </a>

        <a href="dashboard.php" class="<?= ($current == 'dashboard.php') ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        <a href="earnings.php" class="<?= ($current == 'earnings.php') ? 'active' : '' ?>">
            <i class="fa-solid fa-coins"></i> Earnings
        </a>
        <a href="messages.php" class="<?= ($current == 'messages.php') ? 'active' : '' ?>">
            <i class="fas fa-comments"></i> Messages
        </a>
        <a href="users.php" class="<?= ($current == 'users.php') ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Manage Users
        </a>

        <a href="reported_rentals.php" class="<?= ($current == 'reported_rentals.php') ? 'active' : '' ?>">
            <i class="fas fa-flag"></i> Reported Rentals
        </a>

        <a href="rentals.php" class="<?= ($current == 'rentals.php') ? 'active' : '' ?>">
            <i class="fas fa-motorcycle"></i> Manage Rentals
        </a>

        <a href="pending_posts.php" class="<?= ($current == 'pending_posts.php') ? 'active' : '' ?>">
            <i class="fas fa-clock"></i> Pending Requests
        </a>

        <a href="../auth/logout.php">
            <i class="fas fa-right-from-bracket"></i> Logout
        </a>

    </div>

    <div class="sidebar-profile">

        <div class="profile-avatar">
            <?= strtoupper(substr($_SESSION['user']['fullname'] ?? 'A', 0, 1)); ?>
        </div>

        <div class="profile-info">
            <h4><?= $_SESSION['user']['fullname'] ?? 'Admin User'; ?></h4>
            <p>Administrator</p>
        </div>

    </div>

</aside>

<script>
const menuToggle = document.getElementById("menuToggle");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("sidebarOverlay");
const closeBtn = document.getElementById("closeSidebar");

function openSidebar(){
    sidebar.classList.add("active");
    overlay.classList.add("active");
    menuToggle.style.display = "none";
}

function closeSidebar(){
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
    menuToggle.style.display = "flex";
}

menuToggle.addEventListener("click", openSidebar);
overlay.addEventListener("click", closeSidebar);
closeBtn.addEventListener("click", closeSidebar);
</script>

</body>
</html>