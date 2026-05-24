<?php
session_start();
require "../config/Database.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$stmt = $db->query("
    SELECT 
        COALESCE(SUM(amount),0) as total_revenue,
        COALESCE(SUM(admin_amount),0) as total_commission,
        COALESCE(SUM(owner_amount),0) as total_owner,
        COUNT(*) as total_payments
    FROM payments
    WHERE status = 'success'
");

$stats = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $db->query("
    SELECT 
        p.*,
        r.title,
        u.fullname
    FROM payments p
    JOIN rentals r ON r.id = p.rental_id
    JOIN users u ON u.id = p.user_id
    WHERE p.status = 'success'
    ORDER BY p.id DESC
    LIMIT 10
");

$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Earnings Dashboard</title>

<link rel="stylesheet" href="../assets/css/admin_earning.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="dashboard">

<?php include "includes/sidebar.php"; ?>

<div class="main">

    <div class="header">
        <h1><i class="fa-solid fa-coins"></i> Earnings Dashboard</h1>
        <p>Track platform revenue and commissions</p>
    </div>

    <div class="stats">

        <div class="card">
            <i class="fa fa-wallet"></i>
            <h2><?= number_format($stats['total_revenue'], 2) ?> ETB</h2>
            <p>Total Revenue</p>
        </div>

        <div class="card">
            <i class="fa fa-coins"></i>
            <h2><?= number_format($stats['total_commission'], 2) ?> ETB</h2>
            <p>Admin Commission (2%)</p>
        </div>

        <div class="card">
            <i class="fa fa-home"></i>
            <h2><?= number_format($stats['total_owner'], 2) ?> ETB</h2>
            <p>Owner Earnings</p>
        </div>

        <div class="card">
            <i class="fa fa-receipt"></i>
            <h2><?= $stats['total_payments'] ?></h2>
            <p>Total Successful Payments</p>
        </div>

    </div>

    <div class="table-box">

        <h2>Recent Transactions</h2>

        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Rental</th>
                    <th>Amount</th>
                    <th>Admin Cut</th>
                    <th>Owner Cut</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                <?php if(count($payments) > 0): ?>
                    <?php foreach($payments as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['fullname']) ?></td>
                        <td><?= htmlspecialchars($p['title']) ?></td>
                        <td><?= number_format($p['amount'],2) ?> ETB</td>
                        <td><?= number_format($p['admin_amount'],2) ?> ETB</td>
                        <td><?= number_format($p['owner_amount'],2) ?> ETB</td>
                        <td>
                            <span class="status success">
                                Success
                            </span>
                        </td>
                        <td><?= $p['created_at'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;">No successful payments yet</td>
                    </tr>
                <?php endif; ?>
            </tbody>

        </table>

    </div>

</div>
</div>

</body>
</html>