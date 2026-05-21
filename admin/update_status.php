<?php
session_start();

require "../config/Database.php";

/* ADMIN CHECK */

if(
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'admin'
){
    header("Location: ../auth/login.php");
    exit();
}

if(
    !isset($_GET['id']) ||
    !isset($_GET['status'])
){
    die("Missing data");
}

$id = $_GET['id'];

$status = $_GET['status'];

$allowed = ['approved','rejected'];

if(!in_array($status,$allowed)){

    die("Invalid status");
}

$db = (new Database())->connect();

/* UPDATE STATUS */

$stmt = $db->prepare("

UPDATE rentals

SET status = ?

WHERE id = ?

");

$stmt->execute([

    $status,
    $id

]);

header("Location: pending_posts.php");

exit();
?>