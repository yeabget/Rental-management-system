<?php
session_start();

require "../config/Database.php";

if(
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'owner'
){
    header("Location: ../auth/login.php");
    exit();
}

if(
    !isset($_GET['id']) ||
    !isset($_GET['rental'])
){
    exit();
}

$db = (new Database())->connect();

$image_id = $_GET['id'];
$rental_id = $_GET['rental'];
$owner_id = $_SESSION['user']['id'];


$stmt = $db->prepare("
    SELECT rental_gallery.*
    FROM rental_gallery

    JOIN rentals
    ON rentals.id = rental_gallery.rental_id

    WHERE rental_gallery.id = ?
    AND rentals.owner_id = ?
");

$stmt->execute([
    $image_id,
    $owner_id
]);

$image = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$image){
    die("Image not found");
}


$file = "../assets/images/" . $image['image'];

if(file_exists($file)){
    unlink($file);
}

$delete = $db->prepare("
    DELETE FROM rental_gallery
    WHERE id = ?
");

$delete->execute([$image_id]);

header("Location: edit_rental.php?id=" . $rental_id);
exit();
?>