<?php
session_start();

require_once "../config/Database.php";

/* DATABASE CONNECTION */

$db = (new Database())->connect();

/* ONLY ALLOW POST REQUEST */

if($_SERVER["REQUEST_METHOD"] !== "POST"){

    header("Location: login.php");
    exit();
}

/* VALIDATE INPUTS */

if(
    empty($_POST['email']) ||
    empty($_POST['password'])
){

    header("Location: login.php?error=All fields are required");
    exit();
}

/* GET FORM DATA */

$email = trim($_POST['email']);
$password = trim($_POST['password']);

/* FIND USER */

$stmt = $db->prepare("
    SELECT * FROM users
    WHERE email = ?
");

$stmt->execute([$email]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

/* USER NOT FOUND */

if(!$user){

    header("Location: login.php?error=User not found");
    exit();
}

/* VERIFY PASSWORD */

if(!password_verify($password, $user['password'])){

    header("Location: login.php?error=Wrong password");
    exit();
}

/* ACCOUNT STATUS */

if($user['status'] !== 'active'){

    header("Location: login.php?error=Account suspended");
    exit();
}

/* NORMALIZE ROLE */

$role = strtolower(trim($user['role']));

/* CREATE SESSION */

$_SESSION['user'] = [

    'id' => $user['id'],
    'fullname' => $user['fullname'],
    'email' => $user['email'],
    'role' => $role
];

/* ROLE REDIRECT */

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