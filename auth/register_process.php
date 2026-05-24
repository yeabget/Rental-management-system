<?php
session_start();
require "../config/Database.php";

$db = (new Database())->connect();

$errors = [];

$fullname = trim($_POST['fullname'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$password = trim($_POST['password'] ?? '');
$role     = trim($_POST['role'] ?? '');


if(empty($fullname)){
    $errors['fullname'] = "Full name is required";
}else{

    $nameParts = array_filter(explode(" ", $fullname));

    if(count($nameParts) < 2){
        $errors['fullname'] = "Please enter full name (first and last name)";
    }
}


if(empty($email)){

    $errors['email'] = "Email is required";

}else{

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors['email'] = "Invalid email format";
    }
    elseif(!preg_match("/@gmail\.com$/", $email)){
        $errors['email'] = "Email must end with @gmail.com";
    }
}


if(empty($phone)){

    $errors['phone'] = "Phone number is required";

}else{

    if(!preg_match('/^[0-9]{10}$/', $phone)){
        $errors['phone'] = "Phone number must contain exactly 10 digits";
    }
}


if(empty($password)){

    $errors['password'] = "Password is required";

}else{

    if(strlen($password) < 6){
        $errors['password'] = "Password must be at least 6 characters";
    }
}


if(empty($role)){
    $errors['role'] = "Please select a role";
}


if(empty($errors)){

    $stmt = $db->prepare("
        SELECT id
        FROM users
        WHERE email = ?
    ");

    $stmt->execute([$email]);

    if($stmt->rowCount() > 0){
        $errors['email'] = "Email already exists";
    }
}


if(!empty($errors)){

    $_SESSION['errors'] = $errors;

    $_SESSION['old'] = [
        'fullname' => $fullname,
        'email' => $email,
        'phone' => $phone,
        'role' => $role
    ];

    header("Location: register.php");
    exit();
}


$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $db->prepare("
    INSERT INTO users
    (fullname,email,phone,password,role)
    VALUES (?,?,?,?,?)
");

$stmt->execute([
    $fullname,
    $email,
    $phone,
    $hashed,
    $role
]);

$_SESSION['user'] = [
    "id" => $db->lastInsertId(),
    "fullname" => $fullname,
    "email" => $email,
    "role" => $role
];

header("Location: ../index.php");
exit();
?>