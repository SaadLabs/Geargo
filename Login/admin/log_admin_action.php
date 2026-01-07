<?php
session_start(); 
require_once("../../Backend/config/functions.php");

$conn = dbConnect();

if (!$conn) {
    $error = urlencode("Database connection failed.");
    header("Location: login_admin.php?error=$error");
    exit;
}

$email = trim($_POST["email"]);
$password = $_POST["password"];

$sql = "SELECT * FROM user WHERE email = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $record = $result->fetch_assoc();

        // Verify Password
        if (password_verify($password, $record["password"])) {
            
            // Check Role and Redirect Accordingly
            $role = strtolower(trim($record["role"])); // Normalize role string

            if ($role === 'admin') {
                // Handle Admin
                session_regenerate_id(true);
                $_SESSION['user_id'] = $record['user_id'];
                $_SESSION['role'] = 'admin';
                
                header("Location: ../../admin/super-admin/admin.php");
                exit;

            } elseif ($role === 'staff') {
                // Handle Staff
                session_regenerate_id(true);
                $_SESSION['user_id'] = $record['user_id'];
                $_SESSION['role'] = 'staff';
                
                header("Location: ../../admin/staff/staff.php");
                exit;

            } else {
                // Handle Unauthorized Users
                $error = urlencode("Access Denied: You are not authorized to access this panel.");
                header("Location: login_admin.php?error=$error");
                exit;
            }

        } else {
            // Wrong Password
            header("Location: login_admin.php?error=" . urlencode("Invalid Password."));
            exit;
        }
    } else {
        // Email not found
        header("Location: login_admin.php?error=" . urlencode("No account found with this email."));
        exit;
    }
    $stmt->close();
} else {
    header("Location: login_admin.php?error=" . urlencode("System error."));
    exit;
}

$conn->close();
?>