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

$stmt = $db->query("
    SELECT 
        reports.*,
        rentals.title AS rental_title,
        users.fullname AS reporter_name
    FROM reports
    JOIN rentals ON reports.rental_id = rentals.id
    JOIN users ON reports.reporter_id = users.id
    ORDER BY reports.id DESC
");

$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/css/reports.css">
</head>

<body>

<div class="dashboard">

<?php include "includes/sidebar.php"; ?>

<div class="main">
<div class="page-top">
    <div class="welcome-box">
        <h1>Reported Rentals</h1>
        <p>Review and manage reported rental listings</p>
    </div>
</div>

<table border="1" cellpadding="10">

<tr>
    <th>Rental</th>
    <th>Reporter</th>
    <th>Reason</th>
    <th>Type</th>
    <th>Date</th>
</tr>

<?php foreach($reports as $r): ?>

<tr>

    <td><?= htmlspecialchars($r['rental_title']); ?></td>

    <td><?= htmlspecialchars($r['reporter_name']); ?></td>

    <td><?= htmlspecialchars($r['reason']); ?></td>

    <!-- SIMPLE SPAM DETECTION -->
    <td>
        <?php if(stripos($r['reason'], 'spam') !== false): ?>
            <span style="color:red;font-weight:bold;">SPAM</span>
        <?php else: ?>
            <span style="color:orange;">REPORT</span>
        <?php endif; ?>
    </td>

    <td><?= $r['created_at']; ?></td>

</tr>

<?php endforeach; ?>

</table>

</div>

</div>

</body>
</html>