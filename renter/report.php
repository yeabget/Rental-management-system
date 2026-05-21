<?php
session_start();

require "../config/Database.php";

/* LOGIN CHECK */
if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

/* VALIDATE RENTAL ID */
if(
    !isset($_GET['id']) ||
    !is_numeric($_GET['id'])
){
    die("Invalid rental");
}

$rental_id = $_GET['id'];
$user_id = $_SESSION['user']['id'];

/* CHECK IF RENTAL EXISTS */
$checkRental = $db->prepare("
    SELECT * FROM rentals
    WHERE id = ?
");

$checkRental->execute([$rental_id]);

$rental = $checkRental->fetch(PDO::FETCH_ASSOC);

if(!$rental){
    die("Rental not found");
}

/* SUBMIT REPORT */
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $reason = trim($_POST['reason']);

    /* EMPTY REASON CHECK */
    if(empty($reason)){
        die("Reason is required");
    }

    /* PREVENT DUPLICATE REPORTS */
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

    /* INSERT REPORT */
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

<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body{
    background: #f4f7fb;
    font-family: Arial, sans-serif;
}

.report-wrapper{
    width: 100%;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 30px;
}

.report-box{
    width: 100%;
    max-width: 500px;
    background: white;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 10px 35px rgba(0,0,0,0.08);
}

.report-box h2{
    margin-bottom: 10px;
    color: #111827;
}

.report-box p{
    color: #6b7280;
    margin-bottom: 25px;
    line-height: 1.6;
}

.report-box textarea{
    width: 100%;
    height: 160px;
    resize: none;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    padding: 15px;
    font-size: 15px;
    outline: none;
    transition: 0.3s;
}

.report-box textarea:focus{
    border-color: #0ea5e9;
    box-shadow: 0 0 0 4px rgba(14,165,233,0.15);
}

.report-actions{
    margin-top: 20px;
    display: flex;
    gap: 15px;
}

.report-btn{
    flex: 1;
    border: none;
    height: 50px;
    border-radius: 12px;
    background: #ef4444;
    color: white;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

.report-btn:hover{
    background: #dc2626;
}

.cancel-btn{
    flex: 1;
    height: 50px;
    border-radius: 12px;
    text-decoration: none;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #e5e7eb;
    color: #111827;
    font-weight: 600;
}

.cancel-btn:hover{
    background: #d1d5db;
}
</style>

</head>

<body>

<div class="report-wrapper">

    <div class="report-box">

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