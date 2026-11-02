<?php
session_start();
require_once '../../Backend/config/functions.php';
$conn = dbConnect();

if ($_SESSION['role'] !== 'staff') die("Unauthorized");

$user_id = $_SESSION['user_id'];
$current = $_POST['current_password'];
$new = $_POST['new_password'];
$confirm = $_POST['confirm_password'];

if ($new !== $confirm) {
    header("Location: staff.php?error=Passwords do not match");
    exit();
}

// 1. Verify Current Password
$result = mysqli_query($conn, "SELECT password FROM user WHERE user_id = '$user_id'");
$user = mysqli_fetch_assoc($result);

if (password_verify($current, $user['password'])) {
    // 2. Hash New Password
    $new_hashed = password_hash($new, PASSWORD_DEFAULT);
    
    // 3. Update DB
    $stmt = $conn->prepare("UPDATE user SET password = ? WHERE user_id = ?");
    $stmt->bind_param("si", $new_hashed, $user_id);
    $stmt->execute();
    
    header("Location: staff.php?msg=Password changed successfully");
} else {
    header("Location: staff.php?error=Incorrect current password");
}
?>