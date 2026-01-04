<?php
// Path: cart/add_to_cart.php
require_once '../Backend/config/session_manager.php';
require_once '../Backend/config/functions.php';

// 1. Check Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/user/login_user.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

if ($product_id <= 0) {
    die("Invalid product.");
}

$conn = dbConnect();

try {
    // 2. Check/Create Cart
    // Check if user has a cart
    $cartSql = "SELECT cart_id FROM Cart WHERE user_id = ?";
    $stmt = $conn->prepare($cartSql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $cart_id = $row['cart_id'];
    } else {
        // Create new cart
        $createSql = "INSERT INTO Cart (user_id, created_at) VALUES (?, NOW())";
        $stmtCreate = $conn->prepare($createSql);
        $stmtCreate->bind_param("i", $user_id);
        $stmtCreate->execute();
        $cart_id = $stmtCreate->insert_id;
    }

    // 3. Add Item to Cart
    // Check if this product is already in the cart
    $itemSql = "SELECT cart_item_id, quantity FROM CartItem WHERE cart_id = ? AND product_id = ?";
    $stmtItem = $conn->prepare($itemSql);
    $stmtItem->bind_param("ii", $cart_id, $product_id);
    $stmtItem->execute();
    $resultItem = $stmtItem->get_result();

    if ($resultItem->num_rows > 0) {
        // Update existing quantity
        $itemRow = $resultItem->fetch_assoc();
        $new_qty = $itemRow['quantity'] + $quantity;

        $updateSql = "UPDATE CartItem SET quantity = ? WHERE cart_item_id = ?";
        $stmtUpdate = $conn->prepare($updateSql);
        $stmtUpdate->bind_param("ii", $new_qty, $itemRow['cart_item_id']);
        $stmtUpdate->execute();
    } else {
        // Insert new item
        $insertSql = "INSERT INTO CartItem (cart_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmtInsert = $conn->prepare($insertSql);
        $stmtInsert->bind_param("iii", $cart_id, $product_id, $quantity);
        $stmtInsert->execute();
    }

    if (isset($_POST['buy_now']) && $_POST['buy_now'] == 'true') {
        header("Location: ../checkout/checkout.php");
        exit();
    }

    // 4. Redirect back
    // This sends the user back to the page they clicked the button from (Home or Product page)
    if (isset($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: ../index.php");
    }
    exit();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>