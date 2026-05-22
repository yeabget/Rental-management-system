<?php

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

/* ================= AUTH CHECK ================= */

if(
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'renter'
){
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/Database.php";

$db = (new Database())->connect();

$user = $_SESSION['user'];

$firstLetter =
strtoupper(substr($user['fullname'],0,1));

/* ================= CURRENT PAGE ================= */

$currentPage =
basename($_SERVER['PHP_SELF']);

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

<title>Renter Sidebar</title>

<link rel="stylesheet"
href="../assets/css/renter_sidebar.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<!-- ================= MENU BUTTON ================= -->

<button class="menu-toggle" id="menuToggle">

    <i class="fas fa-bars"></i>

</button>

<!-- ================= OVERLAY ================= -->

<div class="sidebar-overlay"
id="sidebarOverlay"></div>

<!-- ================= SIDEBAR ================= -->

<aside class="sidebar" id="sidebar">

    <!-- HEADER -->

    <div class="sidebar-header">

        <h2 class="sidebar-title">
            Renter Panel
        </h2>

        <button class="close-btn"
        id="closeSidebar">

            <i class="fas fa-times"></i>

        </button>

    </div>

    <!-- NAVIGATION -->

    <div class="sidebar-top">

        <a
        href="../index.php"
        class="<?= ($currentPage == 'index.php') ? 'active' : '' ?>">

            <i class="fas fa-home"></i>

            Home

        </a>
 <a
        href="dashboard.php"
        class="<?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">

            <i class="fas fa-dashboard"></i>

            Dashboard

        </a>
        <a
        href="saved.php"
        class="<?= ($currentPage == 'saved.php') ? 'active' : '' ?>">

            <i class="fas fa-bookmark"></i>

            Saved

        </a>

        <a
        href="chat_list.php"
        class="<?= ($currentPage == 'chat_list.php') ? 'active' : '' ?>">

            <i class="fas fa-comments"></i>

            Chats

            <?php if($unreadChats > 0): ?>

                <span class="chat-badge">

                    <?= $unreadChats ?>

                </span>

            <?php endif; ?>

        </a>

        <a href="../auth/logout.php">

            <i class="fas fa-right-from-bracket"></i>

            Logout

        </a>

    </div>

    <!-- PROFILE -->

    <div class="sidebar-profile">

        <div class="profile-avatar">

            <?= $firstLetter ?>

        </div>

        <div class="profile-info">

            <h4>
                <?= htmlspecialchars($user['fullname']) ?>
            </h4>

            <p>
                Renter
            </p>

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

    menuToggle.style.display = "none";
};

/* ================= CLOSE SIDEBAR ================= */

function closeMenu(){

    sidebar.classList.remove("active");

    overlay.classList.remove("active");

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

/* ================= WINDOW RESIZE ================= */

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

/* ================= INITIAL LOAD ================= */

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