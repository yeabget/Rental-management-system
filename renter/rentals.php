<?php
session_start();
require "../config/database.php";

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();
$user_id = $_SESSION['user']['id'];

$stmt = $db->prepare("
SELECT *
FROM rentals
WHERE status = 'approved'
ORDER BY id DESC
");
$stmt->execute([$user_id]);
$saved = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>

<div class="main">

<h2>Saved Items</h2>

<?php if(empty($saved)): ?>

    <div class="empty-state">
        <h3>No saved items yet</h3>
        <p>Click ❤️ on rentals to save them.</p>
        <a href="dashboard.php" class="btn">Browse Rentals</a>
    </div>

<?php else: ?>

<div class="grid">

<?php foreach($saved as $r): ?>

    <div class="card">
        <img src="../assets/images/<?= htmlspecialchars($r['image']) ?>">
        <h3><?= htmlspecialchars($r['title']) ?></h3>
        <p>$<?= htmlspecialchars($r['price']) ?>/day</p>

        <a href="view.php?id=<?= $r['id'] ?>">
            <button>View Details</button>
        </a>
    </div>

<?php endforeach; ?>

</div>

<?php endif; ?>

</div>

</body>
</html>