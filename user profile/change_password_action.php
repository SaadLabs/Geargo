<?php
session_start();
require_once '../Backend/config/functions.php'; // Adjust path if needed

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/user/login_user.php");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $conn = dbConnect();
    $user_id = $_SESSION['user_id'];
    
    // Capture Inputs
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Basic Validation
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error'] = "All password fields are required.";
        header("Location: user.php");
        exit();
    }

    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "New passwords do not match.";
        header("Location: user.php");
        exit();
    }

    // Verify Current Password
    // Fetch the stored hash from the database
    $sql = "SELECT password FROM user WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($current_password, $user['password'])) {
        //Current Password is Correct, Hash and Update New Password
        
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $updateSql = "UPDATE user SET password = ? WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("si", $new_hashed_password, $user_id);
        
        if ($updateStmt->execute()) {
            $_SESSION['success'] = "Password changed successfully!";
        } else {
            $_SESSION['error'] = "Error updating password: " . $conn->error;
        }
        $updateStmt->close();
        
    } else {
        // Current password was wrong
        $_SESSION['error'] = "Incorrect current password.";
    }
    
    $conn->close();
    header("Location: user.php");
    exit();

} else {
    header("Location: user.php");
    exit();
}
?>