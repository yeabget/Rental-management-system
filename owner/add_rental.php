<?php
session_start();
require "../config/Database.php";

/* ================= AUTH ================= */

if(
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'owner'
){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Add Rental</title>

<link rel="stylesheet"
href="../assets/css/add_rentals.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<div class="dashboard">

    <?php include "includes/sidebar.php"; ?>

    <!-- MAIN -->

    <div class="container">

        <!-- PAGE TOP -->

        <div class="page-top">

            <div class="welcome-box">

                <h1>
                    Add New Rental
                </h1>

                <p>
                    Add and manage your rental listings easily
                </p>

            </div>

        </div>

        <!-- FORM -->

        <div class="form-box">
<a href="owner_dashboard.php" class="back-btn">

                <i class="fa fa-arrow-left"></i>

                Back 

            </a>

            <form
            action="save_rental.php"
            method="POST"
            enctype="multipart/form-data">

                <!-- CATEGORY -->

                <div>

                    <label>Choose Category</label>

                    <select
                    name="category"
                    id="category"
                    onchange="showForm()"
                    required>

                        <option value="">Select</option>

                        <option value="house">
                            House
                        </option>

                        <option value="shop">
                            Shop
                        </option>

                        <option value="car">
                            Car
                        </option>

                        <option value="motorcycle">
                            Motorcycle
                        </option>

                    </select>

                </div>

                <!-- BASIC INFO -->

                <div class="grid">

                    <div>

                        <label>Rental Title</label>

                        <input
                        type="text"
                        name="title"
                        placeholder="Enter title"
                        required>

                    </div>

                    <div>

                        <label>Location</label>

                        <input
                        type="text"
                        name="location"
                        placeholder="Enter location"
                        required>

                    </div>

                </div>

                <!-- LICENSE -->

                <div>

                    <label>
                        Upload Ownership / License Document
                    </label>

                    <input
                    type="file"
                    name="license_image"
                    accept="image/*"
                    required>

                </div>

                <!-- HOUSE -->

                <div id="house" class="section">

                    <h3>House Details</h3>

                    <div class="grid">

                        <input
                        type="number"
                        name="house_bedrooms"
                        placeholder="Bedrooms">

                        <input
                        type="number"
                        name="house_bathrooms"
                        placeholder="Bathrooms">

                    </div>

                    <input
                    type="number"
                    name="house_size"
                    placeholder="Size">

                    <input
                    type="number"
                    name="house_price"
                    placeholder="Monthly Price">

                    <textarea
                    name="house_description"
                    placeholder="Description"></textarea>

                    <input
                    type="file"
                    name="house_image"
                    accept="image/*">

                </div>

                <!-- SHOP -->

                <div id="shop" class="section">

                    <h3>Shop Details</h3>

                    <input
                    type="number"
                    name="shop_size"
                    placeholder="Size">

                    <input
                    type="number"
                    name="shop_price"
                    placeholder="Monthly Price">

                    <textarea
                    name="shop_description"
                    placeholder="Description"></textarea>

                    <input
                    type="file"
                    name="shop_image"
                    accept="image/*">

                </div>

                <!-- CAR -->

                <div id="car" class="section">

                    <h3>Car Details</h3>

                    <div class="grid">

                        <input
                        type="text"
                        name="car_brand"
                        placeholder="Brand">

                        <input
                        type="text"
                        name="car_model"
                        placeholder="Model">

                    </div>

                    <div class="grid">

                        <input
                        type="number"
                        name="car_year"
                        placeholder="Year">

                        <input
                        type="text"
                        name="car_fuel"
                        placeholder="Fuel">

                    </div>

                    <input
                    type="text"
                    name="car_transmission"
                    placeholder="Transmission">

                    <input
                    type="number"
                    name="car_seats"
                    placeholder="Seats">

                    <input
                    type="number"
                    name="car_price"
                    placeholder="Daily Price">

                    <textarea
                    name="car_description"
                    placeholder="Description"></textarea>

                    <input
                    type="file"
                    name="car_image"
                    accept="image/*">

                </div>

                <!-- MOTOR -->

                <div id="motorcycle" class="section">

                    <h3>Motorcycle Details</h3>

                    <div class="grid">

                        <input
                        type="text"
                        name="motor_brand"
                        placeholder="Brand">

                        <input
                        type="text"
                        name="motor_model"
                        placeholder="Model">

                    </div>

                    <div class="grid">

                        <input
                        type="number"
                        name="motor_year"
                        placeholder="Year">

                        <input
                        type="number"
                        name="motor_mileage"
                        placeholder="Mileage">

                    </div>

                    <input
                    type="text"
                    name="motor_fuel"
                    placeholder="Fuel">

                    <input
                    type="text"
                    name="motor_transmission"
                    placeholder="Transmission">

                    <input
                    type="number"
                    name="motor_price"
                    placeholder="Daily Price">

                    <textarea
                    name="motor_description"
                    placeholder="Description"></textarea>

                    <input
                    type="file"
                    name="motor_image"
                    accept="image/*">

                </div>

                <!-- STATUS -->

                <div>

                    <label>Rental Status</label>

                    <select name="status" required>

                        <option value="available">
                            Available
                        </option>

                        <option value="booked">
                            Booked
                        </option>

                    </select>

                </div>

                <!-- SUBMIT -->

                <button type="submit">

                    <i class="fa fa-paper-plane"></i>

                    Post Rental

                </button>

            </form>

        </div>

    </div>

</div>

<script>

function showForm(){

    let cat =
    document.getElementById("category").value;

    document
    .querySelectorAll(".section")
    .forEach(section => {

        section.style.display = "none";
    });

    if(cat){

        document
        .getElementById(cat)
        .style.display = "block";
    }
}

</script>

</body>
</html>