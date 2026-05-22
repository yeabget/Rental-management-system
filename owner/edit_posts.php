<?php
session_start();
require "../config/Database.php";

/* ================= AUTH CHECK ================= */

if(
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'owner'
){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$owner_id = $_SESSION['user']['id'];

/* ================= GET POSTS ================= */

$stmt = $db->prepare("
    SELECT *
    FROM rentals
    WHERE owner_id = ?
    ORDER BY id DESC
");

$stmt->execute([$owner_id]);

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Edit Posts</title>

<link rel="stylesheet"
href="../assets/css/edit_posts.css">

<link rel="stylesheet"
href="../assets/css/owners_sidebar.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<div class="dashboard">

    <!-- ================= SIDEBAR ================= -->

    <?php include "includes/sidebar.php"; ?>

    <!-- ================= MAIN ================= -->

    <main class="main">

        <!-- ================= TOPBAR ================= -->

        <div class="topbar">

            <div class="topbar-content">

                <h1>Edit Posts</h1>

                <p>
                    Manage, update and organize your rental listings
                </p>

            </div>

        </div>

        <!-- ================= PROPERTY GRID ================= -->

        <div class="property-grid">

        <?php if(count($posts) > 0): ?>

            <?php foreach($posts as $rental): ?>

                <?php

                $image = !empty($rental['image'])
                    ? $rental['image']
                    : "default.png";

                $priceType = '';

                if(
                    strtolower($rental['category']) == 'house' ||
                    strtolower($rental['category']) == 'shop'
                ){

                    $priceType = '/month';

                }else{

                    $priceType = '/day';
                }

                ?>

                <!-- PROPERTY CARD -->

                <div class="property-card">

                    <!-- IMAGE -->

                    <img
                    src="../assets/images/<?= htmlspecialchars($image) ?>"
                    class="property-image"
                    alt="Rental Image">

                    <!-- CONTENT -->

                    <div class="property-content">

                        <!-- TITLE + PRICE -->

                        <div class="title-price">

                            <h3>
                                <?= htmlspecialchars($rental['title']) ?>
                            </h3>

                            <span class="price">

                                $
                                <?= number_format($rental['price'],2) ?>

                                <?= $priceType ?>

                            </span>

                        </div>

                        <!-- CATEGORY -->

                        <div class="category-badge">

                            <?= ucfirst($rental['category']) ?>

                        </div>

                        <!-- LOCATION + STATUS -->

                        <div class="location-approval">

                            <p class="location">

                                <i class="fa fa-location-dot"></i>

                                <?= htmlspecialchars($rental['location']) ?>

                            </p>

                            <!-- STATUS -->

                            <div class="status-box">

                                <?php if($rental['status'] == 'pending'): ?>

                                    <p class="status pending">

                                        Pending Approval

                                    </p>

                                <?php elseif($rental['status'] == 'approved'): ?>

                                    <p class="status approved">

                                        Approved

                                    </p>

                                <?php elseif($rental['status'] == 'rejected'): ?>

                                    <p class="status rejected">

                                        Rejected

                                    </p>

                                <?php endif; ?>

                            </div>

                        </div>

                        <!-- REJECT REASON -->

                        <?php if(
                            $rental['status'] == 'rejected' &&
                            !empty($rental['reject_reason'])
                        ): ?>

                            <div class="reject-message">

                                <strong>Reason:</strong>

                                <?= htmlspecialchars($rental['reject_reason']) ?>

                            </div>

                        <?php endif; ?>

                        <!-- DESCRIPTION -->

                        <?php if(!empty($rental['description'])): ?>

                            <p class="description">

                                <?= nl2br(
                                    htmlspecialchars(
                                        substr(
                                            $rental['description'],
                                            0,
                                            100
                                        )
                                    )
                                ) ?>

                                ...

                            </p>

                        <?php endif; ?>

                        <!-- EXTRA INFO -->

                        <div class="extra-info">

                            <?php if(!empty($rental['bedrooms'])): ?>

                                <span>

                                    <i class="fa fa-bed"></i>

                                    <?= $rental['bedrooms'] ?>
                                    Bedrooms

                                </span>

                            <?php endif; ?>

                            <?php if(!empty($rental['bathrooms'])): ?>

                                <span>

                                    <i class="fa fa-bath"></i>

                                    <?= $rental['bathrooms'] ?>
                                    Bathrooms

                                </span>

                            <?php endif; ?>

                            <?php if(!empty($rental['brand'])): ?>

                                <span>

                                    <i class="fa fa-car"></i>

                                    <?= htmlspecialchars($rental['brand']) ?>

                                </span>

                            <?php endif; ?>

                            <?php if(!empty($rental['model'])): ?>

                                <span>

                                    <i class="fa fa-tag"></i>

                                    <?= htmlspecialchars($rental['model']) ?>

                                </span>

                            <?php endif; ?>

                        </div>

                        <!-- BUTTONS -->

                        <div class="property-buttons">

                            <a
                            href="edit_rental.php?id=<?= $rental['id'] ?>"
                            class="edit-btn">

                                Edit

                            </a>

                            <a
                            href="delete_rental.php?id=<?= $rental['id'] ?>"
                            class="delete-btn"
                            onclick="return confirm('Delete this rental?')">

                                Delete

                            </a>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <!-- EMPTY -->

            <div class="empty-box">

                <i class="fa fa-house"></i>

                <h3>No Rentals Added</h3>

                <p>
                    Start by adding your first rental property.
                </p>

            </div>

        <?php endif; ?>

        </div>

    </main>

</div>

</body>

</html>