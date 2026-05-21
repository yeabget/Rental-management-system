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

$owner_id = $_SESSION['user']['id'];

/* ================= BASIC ================= */

$category = trim($_POST['category']);
$title = trim($_POST['title']);
$location = trim($_POST['location']);
$status = "pending";
$imageName = "default.png";

$description = "";
$price = 0;

$bedrooms = null;
$bathrooms = null;
$size = null;

$brand = null;
$model = null;
$year = null;
$fuel = null;
$transmission = null;
$seats = null;
$mileage = null;

/* ================= CATEGORY ================= */

if($category == "house"){

    $bedrooms = $_POST['house_bedrooms'];
    $bathrooms = $_POST['house_bathrooms'];
    $size = $_POST['house_size'];

    $price = $_POST['house_price'];

    $description =
    $_POST['house_description'];

    $file = $_FILES['house_image'];
}

elseif($category == "shop"){

    $size = $_POST['shop_size'];

    $price = $_POST['shop_price'];

    $description =
    $_POST['shop_description'];

    $file = $_FILES['shop_image'];
}

elseif($category == "car"){

    $brand = $_POST['car_brand'];
    $model = $_POST['car_model'];
    $year = $_POST['car_year'];

    $fuel = $_POST['car_fuel'];

    $transmission =
    $_POST['car_transmission'];

    $seats = $_POST['car_seats'];

    $price = $_POST['car_price'];

    $description =
    $_POST['car_description'];

    $file = $_FILES['car_image'];
}

elseif($category == "motorcycle"){

    $brand = $_POST['motor_brand'];
    $model = $_POST['motor_model'];

    $year = $_POST['motor_year'];

    $mileage = $_POST['motor_mileage'];

    $fuel = $_POST['motor_fuel'];

    $transmission =
    $_POST['motor_transmission'];

    $price = $_POST['motor_price'];

    $description =
    $_POST['motor_description'];

    $file = $_FILES['motor_image'];
}

/* ================= LICENSE IMAGE ================= */

$license_image = "";

if(
    isset($_FILES['license_image']) &&
    $_FILES['license_image']['error'] === 0
){

    $license_image =
    time() . "_" .
    basename($_FILES['license_image']['name']);

    move_uploaded_file(

        $_FILES['license_image']['tmp_name'],

        "../assets/images/" . $license_image
    );
}

/* ================= MAIN IMAGE ================= */

$uploadDir = "../assets/images/";

if(!is_dir($uploadDir)){

    mkdir($uploadDir,0777,true);
}

if(
    isset($file) &&
    $file['error'] === 0
){

    $tmpName = $file['tmp_name'];

    $originalName = $file['name'];

    $ext = strtolower(
        pathinfo(
            $originalName,
            PATHINFO_EXTENSION
        )
    );

    $allowed = [
        'jpg',
        'jpeg',
        'png',
        'webp',
        'jfif'
    ];

    if(in_array($ext,$allowed)){

        $imageName =
        time() . "_" .
        rand(1000,9999) .
        "." . $ext;

        move_uploaded_file(
            $tmpName,
            $uploadDir . $imageName
        );

    }else{

        die("Invalid image type");
    }

}else{

    die("Please upload image");
}

/* ================= INSERT ================= */

$stmt = $db->prepare("

INSERT INTO rentals(

owner_id,
title,
category,
location,
price,
description,
image,
license_image,
bedrooms,
bathrooms,
size,
brand,
model,
year,
fuel,
transmission,
seats,
mileage,
status

)

VALUES(

?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?

)

");

$stmt->execute([

    $owner_id,
    $title,
    $category,
    $location,
    $price,
    $description,
    $imageName,
    $license_image,
    $bedrooms,
    $bathrooms,
    $size,
    $brand,
    $model,
    $year,
    $fuel,
    $transmission,
    $seats,
    $mileage,
    $status

]);

/* ================= REDIRECT ================= */

header("Location: dashboard.php?success=Rental added");

exit();

?>