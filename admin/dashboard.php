<?php
session_start();

require "../config/Database.php";

if(
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'admin'
){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$totalUsers = $db->query("
    SELECT COUNT(*) FROM users
")->fetchColumn();

$totalOwners = $db->query("
    SELECT COUNT(*) FROM users
    WHERE role='owner'
")->fetchColumn();

$totalRenters = $db->query("
    SELECT COUNT(*) FROM users
    WHERE role='renter'
")->fetchColumn();

$totalAdmins = $db->query("
    SELECT COUNT(*) FROM users
    WHERE role='admin'
")->fetchColumn();

$totalRentals = $db->query("
    SELECT COUNT(*) FROM rentals
")->fetchColumn();

?>

<!DOCTYPE html>
<html>
<head>

<link rel="stylesheet"
href="../assets/css/admin_dashboard.css">

</head>
<body>

<div class="dashboard">

<?php include "includes/sidebar.php"; ?>

<div class="main">

    <div class="page-top">

        <div class="welcome-box">
            <h1>System Reports</h1>
            <p>Welcome back, Admin!</p>
        </div>

    </div>

    <div class="stats">

        <div class="stat-box">
            <h2><?= $totalUsers; ?></h2>
            <p>Total Users</p>
        </div>

        <div class="stat-box">
            <h2><?= $totalOwners; ?></h2>
            <p>Owners</p>
        </div>

        <div class="stat-box">
            <h2><?= $totalRenters; ?></h2>
            <p>Renters</p>
        </div>

        <div class="stat-box">
            <h2><?= $totalAdmins; ?></h2>
            <p>Admins</p>
        </div>

        <div class="stat-box">
            <h2><?= $totalRentals; ?></h2>
            <p>Total Rentals</p>
        </div>

    </div>

</div>

</div>

</body>
</html>