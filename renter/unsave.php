<?php
session_start();
require "../config/Database.php";

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$user_id = $_SESSION['user']['id'];
$rental_id = $_GET['id'] ?? 0;

if ($rental_id) {

    $stmt = $db->prepare("
        DELETE FROM saved_items
        WHERE user_id = ? AND rental_id = ?
    ");

    $stmt->execute([$user_id, $rental_id]);
}

header("Location: saved.php");
exit();
?>