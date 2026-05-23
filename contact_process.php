<?php
session_start();
require "config/Database.php";

$db = (new Database())->connect();

/* ================= GET FORM DATA ================= */

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$subject = trim($_POST['subject']);
$message = trim($_POST['message']);

/* ================= VALIDATION ================= */

if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    header("Location: contact.php?error=All fields are required");
    exit();
}

/* ================= INSERT INTO DB ================= */

$stmt = $db->prepare("
    INSERT INTO contact_messages (name, email, subject, message)
    VALUES (?, ?, ?, ?)
");

$success = $stmt->execute([$name, $email, $subject, $message]);

if ($success) {
    header("Location: contact.php?success=Message sent successfully");
} else {
    header("Location: contact.php?error=Failed to send message");
}
exit();