<?php
// user profile/delete_payment_action.php
session_start();
require_once '../Backend/config/functions.php';

// 1. Check Login
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: user.php");
    exit();
}

$conn = dbConnect();
$user_id = $_SESSION['user_id'];
$card_id = $_POST['card_id'];

// 2. Delete the card ONLY if it belongs to the logged-in user
// This AND user_id = ? check is crucial for security!
$sql = "DELETE FROM usercard WHERE card_id = ? AND user_id = ?";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ii", $card_id, $user_id);
    
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