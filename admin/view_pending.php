<?php
session_start();
require "../config/Database.php";

/* ================= ADMIN CHECK ================= */

if(
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'admin'
){
    header("Location: ../auth/login.php");
    exit();
}

if(!isset($_GET['id'])){

    die("Rental ID missing");
}

$db = (new Database())->connect();

$id = $_GET['id'];

/* ================= GET RENTAL ================= */

$stmt = $db->prepare("

SELECT rentals.*,
users.fullname

FROM rentals

JOIN users
ON users.id = rentals.owner_id

WHERE rentals.id = ?

");

$stmt->execute([$id]);

$rental = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$rental){

    die("Rental not found");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Rental Details</title>

<link rel="stylesheet"
href="../assets/css/view_pendings.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<div class="dashboard">

    <!-- SIDEBAR -->
    <?php include "includes/sidebar.php"; ?>

    <!-- MAIN -->
    <div class="main">

        <!-- HEADER -->
        <div class="page-top">

            <div>
                <h1>Rental Details</h1>

                <p>
                    Review rental information before approval
                </p>
                <a href="pending_posts.php" class="back-btn">
    <i class="fas fa-arrow-left"></i>
    Back
</a>
            </div>

        </div>

        <!-- DETAILS CARD -->
        <div class="details-card">

            <!-- IMAGE -->
            <div class="image-box">

                <img
                src="../assets/images/<?= htmlspecialchars($rental['image']) ?>"
                class="main-image"
                alt="Rental Image">

            </div>

            <!-- CONTENT -->
            <div class="details-content">

                <h2>
                    <?= htmlspecialchars($rental['title']) ?>
                </h2>

                <div class="info-grid">

                    <div class="info-box">

                        <span>Owner</span>

                        <p>
                            <?= htmlspecialchars($rental['fullname']) ?>
                        </p>

                    </div>

                    <div class="info-box">

                        <span>Category</span>

                        <p>
                            <?= htmlspecialchars($rental['category']) ?>
                        </p>

                    </div>

                    <div class="info-box">

                        <span>Location</span>

                        <p>
                            <?= htmlspecialchars($rental['location']) ?>
                        </p>

                    </div>

                    <div class="info-box">

                        <span>Price</span>

                        <p class="price">

                            ETB <?= number_format($rental['price']) ?>

                        </p>

                    </div>

                </div>

                <!-- DESCRIPTION -->
                <div class="description">

                    <h3>Description</h3>

                    <p>
                        <?= nl2br(htmlspecialchars($rental['description'])) ?>
                    </p>

                </div>

                <!-- LICENSE -->
                <div class="license-section">

                    <h3>License Document</h3>

                    <img
                    src="../assets/images/<?= htmlspecialchars($rental['license_image']) ?>"
                    class="license-image"
                    onclick="openPreview(this.src)"
                    alt="License">

                </div>

                <!-- BUTTONS -->
                <div class="actions">

                    <a
                    href="update_status.php?id=<?= $rental['id'] ?>&status=approved"
                    class="approve-btn">

                        <i class="fas fa-check"></i>

                        Approve

                    </a>

                    <a
                    href="update_status.php?id=<?= $rental['id'] ?>&status=rejected"
                    class="reject-btn">

                        <i class="fas fa-xmark"></i>

                        Reject

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- IMAGE PREVIEW -->

<div class="preview-box" id="previewBox">

    <span
    class="close-preview"
    onclick="closePreview()">

        ×

    </span>

    <img id="previewImage">

</div>

<script>

function openPreview(src){

    document
    .getElementById("previewBox")
    .style.display = "flex";

    document
    .getElementById("previewImage")
    .src = src;
}

function closePreview(){

    document
    .getElementById("previewBox")
    .style.display = "none";
}

</script>

</body>
</html>