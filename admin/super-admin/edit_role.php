<?php
// 1. Start Session & Connect to DB
session_start();
require_once '../../Backend/config/functions.php';
$conn = dbConnect();

// 2. Security: Check if the person requesting this IS an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized Access");
}

// 3. Check if form data was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Get inputs
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    // VALIDATION: Ensure the role is one of the allowed options
    $allowed_roles = ['customer', 'staff', 'admin'];
    
    if (!in_array($new_role, $allowed_roles)) {
        header("Location: admin.php?msg=" . urlencode("Invalid role selected."));
        exit();
    }

    if ($user_id == $_SESSION['user_id']) {
        header("Location: admin.php?msg=" . urlencode("Security Warning: You cannot change your own role."));
        exit();
    }

    // 4. Update the Database
    $sql = "UPDATE user SET role = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("si", $new_role, $user_id); // 's' for string (role), 'i' for integer (id)
        
        if ($stmt->execute()) {
            // Success
            header("Location: admin.php?msg=" . urlencode("User role updated successfully to $new_role"));
        } else {
            // SQL Error
            header("Location: admin.php?msg=" . urlencode("Error updating role: " . $conn->error));
        }
        $stmt->close();
    } else {
        // Preparation Error
        header("Location: admin.php?msg=" . urlencode("Database error."));
    }
} else {
    // If someone tries to open this file directly without submitting the form
    header("Location: admin.php");
}

$conn->close();
?>