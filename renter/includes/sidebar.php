<?php
session_start();
require "../config/Database.php";

/* ================= AUTH CHECK ================= */

if(
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'renter'
){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$renter_id = $_SESSION['user']['id'];

/* ================= USER INFO ================= */

$stmt = $db->prepare("
    SELECT *
    FROM users
    WHERE id = ?
");

$stmt->execute([$renter_id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user){
    die("User not found");
}

$profileImage = !empty($user['profile_image'])
    ? $user['profile_image']
    : 'default.png';

$firstLetter =
strtoupper(substr($user['fullname'],0,1));

/* ================= FILTERS ================= */

$search =
$_GET['search'] ?? '';

$category =
$_GET['category'] ?? 'All';

/* ================= RENTALS QUERY ================= */

$query = "

SELECT rentals.*,
users.fullname AS owner_name

FROM rentals

JOIN users
ON users.id = rentals.owner_id

WHERE rentals.status = 'approved'

AND (

    rentals.title LIKE :search

    OR rentals.location LIKE :search

)

";

$params = [

    ':search' => "%$search%"
];

/* ================= CATEGORY FILTER ================= */

if($category !== 'All'){

    $map = [

        'Cars' => 'car',
        'Houses' => 'house',
        'Motor Cycles' => 'motorcycle',
        'Shop' => 'shop'
    ];

    if(isset($map[$category])){

        $query .= "
        AND rentals.category = :category
        ";

        $params[':category'] = $map[$category];
    }
}

/* ================= ORDER ================= */

$query .= "
ORDER BY rentals.id DESC
";

$stmt = $db->prepare($query);

$stmt->execute($params);

$rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ================= UNREAD MESSAGES ================= */

$stmt = $db->prepare("

SELECT COUNT(*)

FROM messages

WHERE receiver_id = ?
AND is_read = 0

");

$stmt->execute([$renter_id]);

$unread = $stmt->fetchColumn();

/* ================= CURRENT PAGE ================= */

$current = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Renter Dashboard</title>

<link rel="stylesheet"
href="../assets/css/dashboardd.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<div class="dashboard">

<!-- ================= SIDEBAR ================= -->

<button class="menu-toggle"
id="menuToggle">

    <i class="fa fa-bars"></i>

</button>

<div class="sidebar-overlay"
id="sidebarOverlay"></div>

<div class="sidebar"
id="sidebar">

    <!-- SIDEBAR HEADER -->

    <div class="sidebar-header">

        <h2 class="sidebar-title">
            RentFlow
        </h2>

        <button class="close-btn"
        id="closeSidebar">

            <i class="fas fa-times"></i>

        </button>

    </div>

    <!-- SIDEBAR LINKS -->

    <div class="sidebar-top">

        <a
        href="../index.php"
        class="<?= ($current == 'index.php') ? 'active' : '' ?>">

            <i class="fa fa-home"></i>

            Home

        </a>

        <a
        href="saved.php"
        class="<?= ($current == 'saved.php') ? 'active' : '' ?>">

            <i class="fa fa-heart"></i>

            Saved

        </a>

        <a
        href="chat_list.php"
        class="<?= ($current == 'chat_list.php') ? 'active' : '' ?>">

            <i class="fa fa-comments"></i>

            Chat

            <?php if($unread > 0): ?>

                <span class="badge">

                    <?= $unread ?>

                </span>

            <?php endif; ?>

        </a>

        <a href="../auth/logout.php">

            <i class="fa fa-right-from-bracket"></i>

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

</div>

<!-- ================= MAIN ================= -->

<main class="main">

    <!-- TOPBAR -->

    <div class="topbar">

        <div>

            <h1>

                Welcome,
                <?= htmlspecialchars($user['fullname']) ?>

            </h1>

            <p>

                Find approved rentals easily

            </p>

        </div>

    </div>

    <!-- SEARCH -->

    <form class="search-bar"
    method="GET">

        <i class="fa fa-search"></i>

        <input
        type="text"
        name="search"
        placeholder="Search rentals..."
        value="<?= htmlspecialchars($search) ?>">

        <button type="submit">

            Search

        </button>

    </form>

    <!-- FILTERS -->

    <div class="filters">

        <?php

        $cats = [

            'All',
            'Cars',
            'Houses',
            'Motor Cycles',
            'Shop'
        ];

        foreach($cats as $c):

        ?>

        <a
        href="?category=<?= urlencode($c) ?>"
        class="<?= $category == $c ? 'active-filter' : '' ?>">

            <?= $c ?>

        </a>

        <?php endforeach; ?>

    </div>

    <!-- RENTALS -->

    <div class="property-grid">

        <?php if(empty($rentals)): ?>

            <p style="padding:20px;">

                No approved rentals found.

            </p>

        <?php endif; ?>

        <?php foreach($rentals as $r): ?>

        <div class="property-card">

            <!-- IMAGE -->

            <div class="card-image">

                <img
                src="../assets/images/<?= htmlspecialchars($r['image']) ?>"
                class="main-card-image"
                alt="Rental Image">

                <div class="card-actions">

                    <a
                    href="chat.php?owner=<?= $r['owner_id'] ?>&rental=<?= $r['id'] ?>"
                    class="icon-btn">

                        <i class="fa fa-comments"></i>

                    </a>

                </div>

            </div>

            <!-- CONTENT -->

            <div class="property-content">

                <div class="title-price">

                    <h3>

                        <?= htmlspecialchars($r['title']) ?>

                    </h3>

                    <span>

                        ETB <?= number_format($r['price']) ?>

                    </span>

                </div>

                <p class="location">

                    <i class="fa fa-location-dot"></i>

                    <?= htmlspecialchars($r['location']) ?>

                </p>

                <p class="owner-name">

                    Owner:
                    <?= htmlspecialchars($r['owner_name']) ?>

                </p>

                <p class="status">

                    Status:
                    <?= htmlspecialchars($r['status']) ?>

                </p>

                <a
                href="view.php?id=<?= $r['id'] ?>"
                class="view-btn">

                    View Details

                </a>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

</main>

</div>

<!-- ================= SIDEBAR SCRIPT ================= -->

<script>

const sidebar =
document.getElementById("sidebar");

const menuToggle =
document.getElementById("menuToggle");

const closeSidebar =
document.getElementById("closeSidebar");

const overlay =
document.getElementById("sidebarOverlay");

/* ================= OPEN ================= */

menuToggle.onclick = () => {

    sidebar.classList.add("active");

    overlay.classList.add("active");

    menuToggle.style.display = "none";
};

/* ================= CLOSE ================= */

function closeMenu(){

    sidebar.classList.remove("active");

    overlay.classList.remove("active");

    if(window.innerWidth <= 768){

        menuToggle.style.display = "flex";
    }
}

/* ================= BUTTON CLOSE ================= */

closeSidebar.onclick = () => {

    closeMenu();
};

/* ================= OVERLAY CLOSE ================= */

overlay.onclick = () => {

    closeMenu();
};

/* ================= RESIZE ================= */

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

/* ================= INITIAL ================= */

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