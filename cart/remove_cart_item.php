<?php
// Path: cart/remove_cart_item.php
require_once '../Backend/config/session_manager.php';
require_once '../Backend/config/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/user/login_user.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_item_id'])) {
    $cart_item_id = intval($_POST['cart_item_id']);
    $user_id = $_SESSION['user_id'];
    
    $conn = dbConnect();

    // Security check: Ensure this cart item belongs to the logged-in user's cart
    $checkSql = "SELECT ci.cart_item_id 
                 FROM CartItem ci
                 JOIN Cart c ON ci.cart_id = c.cart_id
                 WHERE ci.cart_item_id = ? AND c.user_id = ?";
                 
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("ii", $cart_item_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // It's safe to delete
        $deleteSql = "DELETE FROM CartItem WHERE cart_item_id = ?";
        $delStmt = $conn->prepare($deleteSql);
        $delStmt->bind_param("i", $cart_item_id);
        $delStmt->execute();
    }
}

// Redirect back
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: ../index.php");
}
exit();
?>