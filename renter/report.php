<?php
session_start();

require "../config/Database.php";

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

if(
    !isset($_GET['id']) ||
    !is_numeric($_GET['id'])
){
    die("Invalid rental");
}

$rental_id = $_GET['id'];
$user_id = $_SESSION['user']['id'];

$checkRental = $db->prepare("
    SELECT * FROM rentals
    WHERE id = ?
");

$checkRental->execute([$rental_id]);

$rental = $checkRental->fetch(PDO::FETCH_ASSOC);

if(!$rental){
    die("Rental not found");
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $reason = trim($_POST['reason']);

    if(empty($reason)){
        die("Reason is required");
    }

    $check = $db->prepare("
        SELECT * 
        FROM reports 
        WHERE reporter_id = ? 
        AND rental_id = ?
    ");

    $check->execute([
        $user_id,
        $rental_id
    ]);

    if($check->rowCount() > 0){
        header("Location: dashboard.php?error=Already reported");
        exit();
    }

    $stmt = $db->prepare("
        INSERT INTO reports
        (reporter_id, rental_id, reason)
        VALUES (?, ?, ?)
    ");

    $stmt->execute([
        $user_id,
        $rental_id,
        $reason
    ]);

    header("Location: dashboard.php?success=Report submitted");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Report Rental</title>

<link rel="stylesheet" href="../assets/css/report.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


</head>

<body>

<div class="report-wrapper">

    <div class="report-box">
 <a href="dashboard.php" class="back-link">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <h2>Report Rental</h2>

        <p>
            Help us keep RentFlow safe by reporting spam,
            fake listings, scams, or inappropriate content.
        </p>

        <form method="POST">

            <textarea
                name="reason"
                placeholder="Describe the problem..."
                required
            ></textarea>

            <div class="report-actions">

                <button type="submit" class="report-btn">
                    Submit Report
                </button>

                <a href="dashboard.php" class="cancel-btn">
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

</body>
</html>