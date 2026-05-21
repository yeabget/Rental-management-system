<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$current = basename($_SERVER['PHP_SELF']);
?>
if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/Database.php";

$db = (new Database())->connect();

$user = $_SESSION['user'];

$firstLetter =
strtoupper(substr($user['fullname'],0,1));

/* ================= CURRENT PAGE ================= */

$currentPage = basename($_SERVER['PHP_SELF']);

/* ================= UNREAD CHATS ================= */

$unreadChats = 0;

try{

    $stmt = $db->prepare("
        SELECT COUNT(*)
        FROM messages
        WHERE receiver_id = ?
        AND is_read = 0
    ");

    $stmt->execute([$user['id']]);

    $unreadChats = $stmt->fetchColumn();

}catch(PDOException $e){

    $unreadChats = 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Owner Sidebar</title>

<link rel="stylesheet"
href="../assets/css/owner_sidebar.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<!-- ================= MENU BUTTON ================= -->

<button class="menu-toggle" id="menuToggle">

    <i class="fa fa-bars"></i>

</button>

<!-- ================= OVERLAY ================= -->

<div class="sidebar-overlay"
id="sidebarOverlay"></div>

<!-- ================= SIDEBAR ================= -->
<button class="menu-toggle" id="menuToggle">
    <i class="fas fa-bars"></i>
</button>

<!-- OVERLAY -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">

    <!-- TOP HEADER (THIS CHANGES ON MOBILE OPEN) -->
    <div class="sidebar-header">

        <h2 class="sidebar-title">Admin Panel</h2>

        <button class="close-btn" id="closeSidebar">
            <i class="fas fa-times"></i>
        </button>

    </div>

    <div class="sidebar-top">

        <a href="../index.php" class="<?= ($current == 'index.php') ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Home
        </a>

        <a href="dashboard.php" class="<?= ($current == 'dashboard.php') ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i> Dashboard
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

    <!-- PROFILE -->
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
<!-- ================= JAVASCRIPT ================= -->

<script>

const sidebar =
document.getElementById("sidebar");

const menuToggle =
document.getElementById("menuToggle");

const closeSidebar =
document.getElementById("closeSidebar");

const overlay =
document.getElementById("sidebarOverlay");

/* ================= OPEN SIDEBAR ================= */

menuToggle.onclick = () => {

    sidebar.classList.add("active");

    overlay.classList.add("active");

    /* HIDE MENU BUTTON */

    menuToggle.style.display = "none";
};

/* ================= CLOSE FUNCTION ================= */

function closeMenu(){

    sidebar.classList.remove("active");

    overlay.classList.remove("active");

    /* SHOW MENU BUTTON AGAIN */

    if(window.innerWidth <= 768){

        menuToggle.style.display = "flex";
    }
}

/* ================= CLOSE BUTTON ================= */

closeSidebar.onclick = () => {

    closeMenu();
};

/* ================= OVERLAY CLOSE ================= */

overlay.onclick = () => {

    closeMenu();
};

/* ================= WINDOW RESIZE FIX ================= */

window.addEventListener("resize", () => {

    if(window.innerWidth > 768){

        menuToggle.style.display = "none";

        sidebar.classList.remove("active");

        overlay.classList.remove("active");

    }else{

        if(!sidebar.classList.contains("active")){

            menuToggle.style.display = "flex";
        }
    }
});

/* ================= INITIAL MOBILE FIX ================= */

window.addEventListener("load", () => {

    if(window.innerWidth <= 768){

        menuToggle.style.display = "flex";

    }else{

        menuToggle.style.display = "none";
    }
});

</script>

</body>

</html>