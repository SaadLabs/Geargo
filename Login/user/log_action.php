<?php
require_once("../../Backend/config/functions.php");
// Include the session manager to handle the 7-day logic
require_once("../../Backend/config/session_manager.php");

$conn = dbConnect();

$email = $_POST["email"];
$password = $_POST["password"];

if ($conn) {
    // Check if email exists
    $sql = "SELECT * FROM user WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        header("Location: login_user.php?error=" . urlencode("No account found with this email"));
        $conn->close();
        exit;
    }

    $record = $result->fetch_assoc();

    if (!password_verify($password, $record["password"])) {
        header("Location: login_user.php?error=" . urlencode("Invalid Password"));
        $conn->close();
        exit;
    }

    // --- LOGIN SUCCESSFUL ---

    // Prevent session fixation attacks
    session_regenerate_id(true);

    // Store user data in Session
    $_SESSION['user_id'] = $record['user_id'];
    $_SESSION['email'] = $record['email'];
    $_SESSION['name'] = $record['name']; // Assuming you have a name column
    $_SESSION['role'] = $record['role']; // Useful for admin/customer checks

    // Redirect to homepage
    header("Location: ../../index.php");
    $conn->close();
    exit;
} 
else {
    $error = urlencode("Database connection failed.");
    header("Location: login_user.php?error=$error");
    exit;
}
?>