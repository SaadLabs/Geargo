<?php
session_start();
require_once '../Backend/config/functions.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: user.php");
    exit();
}

$conn = dbConnect();
$user_id = $_SESSION['user_id'];

// Capture Inputs
$holder_name = $_POST['card_name']; 
$number = $_POST['card_number'];    
$expiry = $_POST['expiry'];         
$cvv = $_POST['cvv'];               

// Process Expiry and card number
$expiry_parts = explode('/', $expiry);
$number = str_replace(' ', '', $_POST['card_number']); 

if (count($expiry_parts) == 2) {
    $exp_month = intval($expiry_parts[0]);
    $exp_year = intval('20' . $expiry_parts[1]); 
} else {
    $exp_month = 0; $exp_year = 0;
}

// Hashing and extracting Last 4 Digits
$last_four = substr($number, -4);

$hashed_number = password_hash($number, PASSWORD_DEFAULT);

// Hash the CVV
$hashed_cvv = password_hash($cvv, PASSWORD_DEFAULT);

$sql = "INSERT INTO usercard (user_id, card_number, last_four, cvv, exp_month, exp_year, card_holder_name) VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("isssiis", $user_id, $hashed_number, $last_four, $hashed_cvv, $exp_month, $exp_year, $holder_name);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Payment method added securely!";
    } else {
        $_SESSION['error'] = "Error adding card: " . $conn->error;
    }
    $stmt->close();
} else {
    $_SESSION['error'] = "Database error: " . $conn->error;
}

$conn->close();
header("Location: user.php");
exit();
?>