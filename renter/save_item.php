<?php
session_start();
require "../config/Database.php";

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$user_id = $_SESSION['user']['id'];

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid item");
}

$rental_id = $_GET['id'];

/* CHECK IF EXISTS */
$stmt = $db->prepare("
    SELECT id FROM saved_items
    WHERE user_id = ? AND rental_id = ?
");
$stmt->execute([$user_id, $rental_id]);

if($stmt->rowCount() == 0){

    $insert = $db->prepare("
        INSERT INTO saved_items(user_id, rental_id)
        VALUES (?, ?)
    ");

    $insert->execute([$user_id, $rental_id]);
}

header("Location: saved.php");
exit();
?>