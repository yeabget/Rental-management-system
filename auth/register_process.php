<?php
session_start();
require "../config/Database.php";

$db = (new Database())->connect();

$fullname = trim($_POST['fullname']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$password = trim($_POST['password']);
$role = trim($_POST['role']);

if(!$fullname || !$email || !$password || !$role){
    die("All fields required");
}

if(strlen($password) < 6){
    die("Password too short");
}

/* CHECK EMAIL */
$stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

if($stmt->rowCount() > 0){
    die("Email already exists");
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $db->prepare("
    INSERT INTO users(fullname,email,phone,password,role)
    VALUES (?,?,?,?,?)
");

$stmt->execute([$fullname,$email,$phone,$hashed,$role]);

$_SESSION['user'] = [
    "id" => $db->lastInsertId(),
    "fullname" => $fullname,
    "email" => $email,
    "role" => $role
];

header("Location: ../index.php");
exit();
?>