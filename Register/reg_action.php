<?php
require_once("../Backend/config/functions.php");

$conn = dbConnect();

$name = $_POST["name"];
$email = $_POST["email"];
$phone = $_POST["phone"];
$password = $_POST["password"];
$role = $_POST["role"];
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Check if email exists
$sql = "SELECT * FROM user WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    header("Location: register.php?error=" . urlencode("Email already exists"));
    $conn->close();
    exit;
}

if ($conn){
    $query = "INSERT INTO `user` (`name`, `email`, `password`, `phone`, `role`) VALUES (?, ?, ?, ?, ?);";

    if ($stmt = $conn->prepare( $query)) {
        $stmt->bind_param("sssss", $name, $email, $hashedPassword, $phone, $role);
        $stmt->execute();
        $conn->close();
        header("location:../index.php");
    }
}
else {
    $error = urlencode("Failed to register user. Please try again.");
    header("Location: register.php?error=$error");
    exit;
}
?>
