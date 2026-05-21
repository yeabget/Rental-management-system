<?php
session_start();
require "../config/Database.php";

$db = (new Database())->connect();

$id = $_GET['id'];

$stmt = $db->prepare("
    UPDATE bookings SET status='rejected' WHERE id=?
");

$stmt->execute([$id]);

header("Location: bookings.php");
exit();
?>