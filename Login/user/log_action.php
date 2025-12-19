<?php
require_once("../../Backend/config/functions.php");

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
        header("Location: login_user.php?error=" . urlencode("No record found"));
        $conn->close();
        exit;
    }

    $record = $result->fetch_assoc();

    if (!password_verify($password, $record["password"])) {
        header("Location: login_user.php?error=" . urlencode("Invalid Password. Please try again."));
        $conn->close();
        exit;
    }

    // Redirect to homepage
    header("Location: ../../index.php");
    $conn->close();
    exit;
} 
else {
    $error = urlencode("Something went wrong. Please try again.");
    header("Location: login_user.php?error=$error");
    exit;
}
?>
