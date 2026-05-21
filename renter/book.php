<?php
session_start();

require "../config/Database.php";

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

/* CURRENT USER */
$user_id = $_SESSION['user']['id'];

/* VALIDATE RENTAL ID */
if(
    !isset($_GET['id']) ||
    !is_numeric($_GET['id'])
){
    die("Invalid rental");
}

$rental_id = $_GET['id'];

/* CHECK RENTAL EXISTS */
$check = $db->prepare("
    SELECT * FROM rentals
    WHERE id = ?
");

$check->execute([$rental_id]);

$rental = $check->fetch(PDO::FETCH_ASSOC);

if(!$rental){
    die("Rental not found");
}

/* PREVENT DUPLICATE BOOKINGS */
$exists = $db->prepare("
    SELECT * FROM bookings
    WHERE user_id = ?
    AND rental_id = ?
");

$exists->execute([
    $user_id,
    $rental_id
]);

if($exists->rowCount() > 0){

    header("Location: dashboard.php?error=Already booked");
    exit();
}

/* INSERT BOOKING */
$stmt = $db->prepare("
    INSERT INTO bookings
    (user_id, rental_id, status)
    VALUES (?, ?, 'Pending')
");

$stmt->execute([
    $user_id,
    $rental_id
]);

header("Location: dashboard.php?success=Booking request sent");
exit();
?>