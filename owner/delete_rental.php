<?php
session_start();
require "../config/Database.php";

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$id = $_GET['id'];

$stmt = $db->prepare("
    DELETE FROM rentals
    WHERE id = ? AND owner_id = ?
");

$stmt->execute([$id, $_SESSION['user']['id']]);

header("Location: dashboard.php");
exit();
?>