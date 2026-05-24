<?php
require "../config/Database.php";

$db = (new Database())->connect();

if(!isset($_GET['tx_ref'])){
    echo "Payment Successful<br>But no transaction reference found.";
    exit();
}

$tx_ref = $_GET['tx_ref'];

$stmt = $db->prepare("
    UPDATE payments 
    SET status = 'success'
    WHERE tx_ref = ?
");

$stmt->execute([$tx_ref]);

echo "
<h2>Payment Successful</h2>
<p>Thank you for booking.</p>
";
?>