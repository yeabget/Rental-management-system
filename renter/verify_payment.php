<?php
require "../config/Database.php";

$db = (new Database())->connect();

$success = false;
$message = "";
$data = null;

if(!isset($_GET['tx_ref'])){
    $message = "Payment completed but transaction reference is missing.";
} else {

    $tx_ref = $_GET['tx_ref'];
    $secret_key = "CHASECK_TEST-vw8wSfJc7cw1fgY6LiYyNldQ5gewME6V";

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.chapa.co/v1/transaction/verify/" . $tx_ref,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $secret_key"
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    $result = json_decode($response, true);

    if(isset($result['status']) && $result['status'] === "success"){

        $success = true;
        $amount = $result['data']['amount'];

        $stmt = $db->prepare("
            UPDATE payments 
            SET status = 'success'
            WHERE tx_ref = ?
        ");
        $stmt->execute([$tx_ref]);

        $stmt = $db->prepare("SELECT * FROM payments WHERE tx_ref = ?");
        $stmt->execute([$tx_ref]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        if($payment){

            $admin = $payment['amount'] * 0.02;
            $owner = $payment['amount'] - $admin;

            $stmt = $db->prepare("
                UPDATE payments
                SET admin_amount = ?, owner_amount = ?
                WHERE tx_ref = ?
            ");

            $stmt->execute([$admin, $owner, $tx_ref]);
        }

        $message = "Payment Verified Successfully!";
        $data = $result['data'];

    } else {
        $message = "Payment Verification Failed!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Status</title>

<style>
body{
    margin:0;
    font-family:Arial;
    background:#f1f5f9;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.card{
    background:#fff;
    padding:30px;
    border-radius:20px;
    box-shadow:0 15px 40px rgba(0,0,0,0.1);
    text-align:center;
    width:350px;
    animation:fade 0.3s ease;
}

.icon{
    font-size:50px;
    margin-bottom:10px;
}

.success{
    color:#16a34a;
}

.error{
    color:#dc2626;
}

h2{
    margin:10px 0;
    font-size:22px;
}

p{
    color:#64748b;
    font-size:14px;
}

.btn{
    display:inline-block;
    margin-top:15px;
    padding:10px 16px;
    border-radius:10px;
    text-decoration:none;
    font-weight:600;
    background:#2563eb;
    color:white;
    transition:0.3s;
}

.btn:hover{
    background:#1d4ed8;
}

@keyframes fade{
    from{transform:translateY(10px);opacity:0;}
    to{transform:translateY(0);opacity:1;}
}
</style>
</head>

<body>

<div class="card">

    <?php if($success): ?>
        <div class="icon success">
            <i class="fa fa-circle-check"></i>
        </div>
    <?php else: ?>
        <div class="icon error">
            <i class="fa fa-circle-xmark"></i>
        </div>
    <?php endif; ?>

    <h2><?= $message ?></h2>

    <?php if($success): ?>
        <p>Transaction completed and saved successfully.</p>
    <?php else: ?>
        <p>Please try again or contact support.</p>
    <?php endif; ?>

    <a href="../renter/dashboard.php" class="btn">
        Go to Dashboard
    </a>

</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</body>
</html>