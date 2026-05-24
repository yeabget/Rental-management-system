<?php
session_start();
require "../config/Database.php";
require "../config/chapa.php";

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();

if(!isset($_GET['id'])){
    die("Invalid Rental");
}

$rental_id = $_GET['id'];

$stmt = $db->prepare("SELECT * FROM rentals WHERE id = ?");
$stmt->execute([$rental_id]);
$rental = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$rental){
    die("Rental not found");
}

$user = $_SESSION['user'];

$tx_ref = uniqid("rentflow_");
$amount = (float)$rental['price'];

$admin_percent = 2;
$admin_amount = ($amount * $admin_percent) / 100;
$owner_amount = $amount - $admin_amount;

$stmt = $db->prepare("
    INSERT INTO payments
    (rental_id, user_id, tx_ref, amount, admin_amount, owner_amount, status)
    VALUES (?, ?, ?, ?, ?, ?, 'pending')
");

$stmt->execute([
    $rental_id,
    $user['id'],
    $tx_ref,
    $amount,
    $admin_amount,
    $owner_amount
]);

$secret_key = "CHASECK_TEST-vw8wSfJc7cw1fgY6LiYyNldQ5gewME6V";

$curl = curl_init();

$data = [
    "amount" => $amount,
    "currency" => "ETB",
    "email" => $user['email'],
    "first_name" => $user['fullname'],
    "tx_ref" => $tx_ref,
    "callback_url" => "http://localhost/rental-management-system/renter/verify_payment.php",
    "return_url" => "http://localhost/rental-management-system/renter/verify_payment.php?tx_ref=".$tx_ref,
    "customization" => [
        "title" => "RentFlow Booking",
        "description" => "Rental Payment"
    ]
];

curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.chapa.co/v1/transaction/initialize",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $secret_key",
        "Content-Type: application/json"
    ],
]);

$response = curl_exec($curl);
curl_close($curl);

$result = json_decode($response, true);

if(isset($result['data']['checkout_url'])){
    header("Location: ".$result['data']['checkout_url']);
    exit();
}else{
    echo "Payment Initialization Failed";
    print_r($result);
}
?>