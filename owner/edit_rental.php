<?php
session_start();
require "../config/Database.php";

if (
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'owner'
) {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$owner_id = $_SESSION['user']['id'];


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: edit_posts.php");
    exit();
}

$id = (int) $_GET['id'];


$stmt = $db->prepare("
    SELECT *
    FROM rentals
    WHERE id = ?
    AND owner_id = ?
");

$stmt->execute([$id, $owner_id]);

$rental = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rental) {
    header("Location: edit_posts.php");
    exit();
}


$galleryStmt = $db->prepare("
    SELECT *
    FROM rental_gallery
    WHERE rental_id = ?
");

$galleryStmt->execute([$id]);

$galleryImages = $galleryStmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $image = $rental['image'];
    $license_image = $rental['license_image'];


    if (!empty($_FILES['image']['name'])) {

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed)) {

            $image = time() . "_main_" . rand(1000, 9999) . "." . $ext;

            move_uploaded_file(
                $_FILES['image']['tmp_name'],
                "../assets/images/" . $image
            );
        }
    }


    if (!empty($_FILES['license_image']['name'])) {

        $ext = strtolower(pathinfo($_FILES['license_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed)) {

            $license_image = time() . "_license_" . rand(1000, 9999) . "." . $ext;

            move_uploaded_file(
                $_FILES['license_image']['tmp_name'],
                "../assets/images/" . $license_image
            );
        }
    }


    $update = $db->prepare("
        UPDATE rentals SET
            title = ?,
            location = ?,
            price = ?,
            description = ?,
            image = ?,
            license_image = ?
        WHERE id = ?
        AND owner_id = ?
    ");

    $update->execute([
        $title,
        $location,
        $price,
        $description,
        $image,
        $license_image,
        $id,
        $owner_id
    ]);

    header("Location: edit_posts.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Rental</title>

<link rel="stylesheet" href="../assets/css/edit_rentals.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="dashboard">

    <?php include "includes/sidebar.php"; ?>

    <div class="main">

        <div class="edit-page">

            <div class="edit-container">

   <div class="page-title">
     <h1>
           Saved Items
           
        </h1>
 <p>Edit Your rental items post</p>
</div>
 
                <form method="POST" enctype="multipart/form-data" class="edit-form">
<a href="edit_posts.php" class="back-btn">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                    <label>Title</label>
                    <input type="text" name="title"
                           value="<?= htmlspecialchars($rental['title']) ?>">

                    <label>Location</label>
                    <input type="text" name="location"
                           value="<?= htmlspecialchars($rental['location']) ?>">

                    <label>Price</label>
                    <input type="number" name="price"
                           value="<?= htmlspecialchars($rental['price']) ?>">

                    <label>Description</label>
                    <textarea name="description"><?= htmlspecialchars($rental['description']) ?></textarea>

                    <label>Main Image</label>

                    <img src="../assets/images/<?= htmlspecialchars($rental['image']) ?>"
                         class="preview-image">

                    <input type="file" name="image" accept="image/*">

                    <label>License / Ownership Image</label>

                    <?php if (!empty($rental['license_image'])): ?>
                        <img src="../assets/images/<?= htmlspecialchars($rental['license_image']) ?>"
                             class="preview-image">
                    <?php endif; ?>

                    <input type="file" name="license_image" accept="image/*">

                    <button type="submit">
                        <i class="fa fa-save"></i> Update Rental
                    </button>

                </form>

                <div class="gallery-grid">

                    <?php foreach ($galleryImages as $g): ?>

                        <div class="gallery-card">

                            <img src="../assets/images/<?= htmlspecialchars($g['image']) ?>"
                                 class="gallery-photo">

                            <a href="delete_gallery.php?id=<?= $g['id'] ?>&rental=<?= $id ?>"
                               class="delete-gallery-btn"
                               onclick="return confirm('Delete this image?')">

                                <i class="fa fa-xmark"></i>

                            </a>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>