<?php
session_start();
require_once '../Backend/config/functions.php';

// Check Login
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: user.php");
    exit();
}

$conn = dbConnect();
$user_id = $_SESSION['user_id'];
$card_id = $_POST['card_id'];

// Delete the card only if it belongs to the logged-in user
$sql = "UPDATE usercard SET is_active = 0 WHERE card_id = ?";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $card_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Payment method removed successfully.";
    } else {
        $_SESSION['error'] = "Error removing card.";
    }
    $stmt->close();
} else {
    $_SESSION['error'] = "Database error.";
}

$conn->close();
header("Location: user.php");
exit();
?>