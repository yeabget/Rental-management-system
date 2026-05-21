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

if(!isset($_GET['id'])){
    header("Location: users.php");
    exit();
}

$db = (new Database())->connect();

$id = $_GET['id'];

$stmt = $db->prepare("
    UPDATE users
    SET status = 'active'
    WHERE id = ?
");

$stmt->execute([$id]);

header("Location: users.php");
exit();
?>