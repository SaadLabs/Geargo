<?php
session_start();
require_once '../Backend/config/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/user/login_user.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $conn = dbConnect();
    $user_id = $_SESSION['user_id'];
    
    // 1. Capture Inputs
    $name = $_POST['name'];
    $phone = $_POST['phone']; // Ensure this matches your DB column name exactly
    
    if (empty($name)) {
        $_SESSION['error'] = "Name cannot be empty.";
        header("Location: user.php");
        exit();
    }

    // 2. RETRIEVE CURRENT IMAGE (Important!)
    // We need the old image path so we don't delete it if the user didn't upload a new one.
    $query = "SELECT profile_pic FROM user WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $currentUser = $res->fetch_assoc();
    $stmt->close();

    // Default to the existing picture
    $final_profile_pic = $currentUser['profile_pic'];

    // 3. Handle File Upload (Only if a file was actually sent)
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        
        $filename = $_FILES['profile_pic']['name'];
        $tmp_name = $_FILES['profile_pic']['tmp_name'];
        $file_type = $_FILES['profile_pic']['type'];
        $file_size = $_FILES['profile_pic']['size'];

        // Clean the filename to prevent spaces/issues
        $clean_filename = preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);
        
        // Correct Destination with slash (/)
        $destination = "../assets/uploads/" . $user_id . "_" . $clean_filename;

        // Validation: Size (< 10MB) and Type (image/jpeg, image/png)
        if ($file_size < 10000000 && ($file_type == "image/jpeg" || $file_type == "image/png" || $file_type == "image/jpg")) {
            
            if (move_uploaded_file($tmp_name, $destination)) {
                // Upload successful, update the variable to the NEW path
                $final_profile_pic = $destination;
            } else {
                $_SESSION['error'] = "Failed to move uploaded file.";
                header("Location: user.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid file type (JPG/PNG only) or file too large.";
            header("Location: user.php");
            exit();
        }
    }

    // 4. Update Database
    $sql = "UPDATE user SET name = ?, phone = ?, profile_pic = ? WHERE user_id = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssi", $name, $phone, $final_profile_pic, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Profile updated successfully!";
            $_SESSION['user_name'] = $name; 
        } else {
            $_SESSION['error'] = "Error updating profile: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Database error: " . $conn->error;
    }
    
    $conn->close();
    header("Location: user.php");
    exit();

} else {
    header("Location: user.php");
    exit();
}
?>