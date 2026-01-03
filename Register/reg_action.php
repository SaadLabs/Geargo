<?php
// Include session manager to handle login immediately after registration
require_once("../Backend/config/session_manager.php");
require_once("../Backend/config/functions.php");

$conn = dbConnect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $password = $_POST["password"];
    $role = $_POST["role"];
    
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if ($conn) {
        // 1. Check if email exists
        $sql = "SELECT user_id FROM user WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            header("Location: register.php?error=" . urlencode("Email already exists"));
            $conn->close();
            exit;
        }
        $stmt->close();

        // 2. Register User
        $insertUserSql = "INSERT INTO `user` (`name`, `email`, `password`, `phone`, `role`, `created_at`) VALUES (?, ?, ?, ?, ?, NOW())";
        
        if ($stmt = $conn->prepare($insertUserSql)) {
            $stmt->bind_param("sssss", $name, $email, $hashedPassword, $phone, $role);
            
            if ($stmt->execute()) {
                // Get the ID of the new user
                $new_user_id = $conn->insert_id;
                
                // 3. Create a Cart for this new user
                $createCartSql = "INSERT INTO Cart (user_id, created_at) VALUES (?, NOW())";
                $cartStmt = $conn->prepare($createCartSql);
                $cartStmt->bind_param("i", $new_user_id);
                $cartStmt->execute();
                $cartStmt->close();

                // 4. Auto-Login the user (Set Session)
                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['email'] = $email;
                $_SESSION['name'] = $name;
                $_SESSION['role'] = $role;

                $conn->close();
                
                // Redirect to Home
                header("Location: ../index.php");
                exit;
            } else {
                // Insert failed
                header("Location: register.php?error=" . urlencode("Registration failed: " . $stmt->error));
            }
        }
    } else {
        $error = urlencode("Database connection failed.");
        header("Location: register.php?error=$error");
        exit;
    }
}
?>