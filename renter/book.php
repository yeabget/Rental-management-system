<?php
session_start();
require "../config/Database.php";

/* ================= CHECK LOGIN ================= */
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->connect();
$user = $_SESSION['user'];

/* ================= GET RENTAL ================= */
if (!isset($_GET['id'])) {
    die("Invalid Rental");
}

$rental_id = intval($_GET['id']);

$stmt = $db->prepare("SELECT * FROM rentals WHERE id = ?");
$stmt->execute([$rental_id]);
$rental = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rental) {
    die("Rental not found");
}

/* ================= CHAPA CONFIG ================= */
$secret_key = "CHASECK_TEST-vw8wSfJc7cw1fgY6LiYyNldQ5gewME6V"; // your TEST secret key

$tx_ref = "rentflow_" . time() . rand(1000, 9999);
$amount = $rental['price'];

/* ================= SAVE PAYMENT (PENDING) ================= */
$stmt = $db->prepare("
    INSERT INTO payments (rental_id, user_id, tx_ref, amount, status)
    VALUES (?, ?, ?, ?, 'pending')
");

$stmt->execute([
    $rental_id,
    $user['id'],
    $tx_ref,
    $amount
]);

/* ================= CHAPA REQUEST ================= */
$callback_url = "http://localhost/rental-management-system/renter/verify_payment.php";
$return_url   = "http://localhost/rental-management-system/renter/payment_success.php";

$data = [
    "amount" => $amount,
    "currency" => "ETB",
    "email" => $user['email'],
    "first_name" => $user['fullname'],
    "tx_ref" => $tx_ref,
    "callback_url" => $callback_url,
    "return_url" => $return_url,
    "customization" => [
        "title" => "RentFlow Booking",
        "description" => "Rental payment"
    ]
];

$curl = curl_init();

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

/* ================= DEBUG (IMPORTANT) ================= */
if (curl_errno($curl)) {
    die("Curl Error: " . curl_error($curl));
}

curl_close($curl);

$result = json_decode($response, true);

/* ================= CHECK RESPONSE ================= */
if (isset($result['status']) && $result['status'] === "success") {

    header("Location: " . $result['data']['checkout_url']);
    exit();

} else {

    echo "<pre>";
    echo "Payment Failed:\n";
    print_r($result);
    echo "</pre>";
}
?>