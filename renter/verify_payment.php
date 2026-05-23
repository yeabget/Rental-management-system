<?php
require "../config/Database.php";

$db = (new Database())->connect();

if(!isset($_GET['tx_ref'])){
    die("No transaction");
}

$tx_ref = $_GET['tx_ref'];

$secret_key = "YOUR_SECRET_KEY";

/* VERIFY */

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL =>
    "https://api.chapa.co/v1/transaction/verify/".$tx_ref,

    CURLOPT_RETURNTRANSFER => true,

    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $secret_key"
    ],
]);

$response = curl_exec($curl);

curl_close($curl);

$result = json_decode($response, true);

if(
    isset($result['status']) &&
    $result['status'] == "success"
){

    $stmt = $db->prepare("
        UPDATE payments
        SET status='paid'
        WHERE tx_ref=?
    ");

    $stmt->execute([$tx_ref]);

    echo "Payment Verified";

}else{

    echo "Payment Failed";
}
?>