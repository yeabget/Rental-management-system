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
    SELECT rentals.*, users.fullname
    FROM rentals
    JOIN users ON rentals.owner_id = users.id
    ORDER BY rentals.id DESC
");

$rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Rentals</title>

<link rel="stylesheet" href="../assets/css/rental.css?v=3">
</head>

<body>

<div class="dashboard">

<?php include "includes/sidebar.php"; ?>

<div class="main">

    <div class="page-top">
        <div class="welcome-box">
            <h1>Manage Rentals</h1>
            <p>View and manage all rental listings.</p>
        </div>
    </div>

    <div class="table-wrapper">

        <table>

            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Category</th>
                <th>Price</th>
                <th>Owner</th>
                <th>Action</th>
            </tr>

            <?php foreach($rentals as $r): ?>

            <tr>

                <td>
                    <img class="image"
                        src="../assets/images/<?= htmlspecialchars($r['image']); ?>">
                </td>

                <td><?= htmlspecialchars($r['title']); ?></td>

                <td><?= htmlspecialchars($r['category']); ?></td>

                <td>$<?= htmlspecialchars($r['price']); ?></td>

                <td><?= htmlspecialchars($r['fullname']); ?></td>

                <td>
                    <a href="delete_rental.php?id=<?= $r['id']; ?>" class="delete-btn">
                        Delete
                    </a>
                </td>

            </tr>

            <?php endforeach; ?>

        </table>

    </div>

</div>

</div>

</body>
</html>