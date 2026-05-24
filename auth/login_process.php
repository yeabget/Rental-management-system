<?php
session_start();

require_once "../config/Database.php";


$db = (new Database())->connect();

if($_SERVER["REQUEST_METHOD"] !== "POST"){

    header("Location: login.php");
    exit();
}


if(
    empty($_POST['email']) ||
    empty($_POST['password'])
){

    header("Location: login.php?error=All fields are required");
    exit();
}

$email = trim($_POST['email']);
$password = trim($_POST['password']);

$stmt = $db->prepare("
    SELECT * FROM users
    WHERE email = ?
");

$stmt->execute([$email]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);


if(!$user){

    header("Location: login.php?error=User not found");
    exit();
}
if(!password_verify($password, $user['password'])){

    header("Location: login.php?error=Wrong password");
    exit();
}
if($user['status'] !== 'active'){

    header("Location: login.php?error=Account suspended");
    exit();
}


$role = strtolower(trim($user['role']));


$_SESSION['user'] = [

    'id' => $user['id'],
    'fullname' => $user['fullname'],
    'email' => $user['email'],
    'role' => $role
];

if($role === 'admin'){

    header("Location: ../admin/dashboard.php");

}elseif($role === 'owner'){

    header("Location: ../owner/dashboard.php");

}elseif($role === 'renter'){

    header("Location: ../renter/dashboard.php");

}else{

    session_destroy();

    header("Location: login.php?error=Invalid role");
}

exit();
?>