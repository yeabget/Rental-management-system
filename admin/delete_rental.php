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

$id = $_GET['id'];

$stmt = $db->prepare("
    DELETE FROM rentals
    WHERE id = ?
");

$stmt->execute([$id]);

header("Location: rentals.php");
exit();
?>