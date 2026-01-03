<?php
session_start();
require_once '../Backend/config/functions.php'; // Adjust path as needed
$conn = dbConnect();

// 1. Check Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/user/login_user.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Fetch User Details
$sql = "SELECT * FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// 3. Fetch Orders
$orders = getUserOrders($conn, $user_id);

// 4. Fetch Cart (for the sidebar)
$cartItems = getCartItems($conn, $user_id);
$cartTotal = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GearGo | User Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="../cart/cart.css">
</head>

<body>

    <div id="cart-overlay" class="cart-overlay" onclick="closeCart()"></div>

    <div id="cart-sidebar" class="cart-sidebar">
        <div class="cart-header">
            <h2>Your Cart</h2>
            <span class="close-cart" onclick="closeCart()">×</span>
        </div>

        <div class="cart-items" id="cartItems">
            <?php if (count($cartItems) > 0): ?>
                <?php foreach ($cartItems as $item): ?>
                    <?php 
                        $itemTotal = $item['price'] * $item['quantity'];
                        $cartTotal += $itemTotal;
                        $imgSrc = !empty($item['image']) ? "../" . $item['image'] : "../headphone1.png";
                    ?>
                    <div class="cart-item">
                        <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="cart-item-details">
                            <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                            <p>Rs. <?php echo number_format($item['price']); ?></p>
                            <input type="number" value="<?php echo $item['quantity']; ?>" min="1" readonly>
                        </div>
                        <form action="../cart/remove_cart_item.php" method="POST" style="display:inline;">
                            <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                            <button type="submit" class="remove-btn">&times;</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-cart" style="display:block;">Your cart is empty</p>
            <?php endif; ?>
        </div>

        <div class="cart-footer">
            <div class="cart-total">
                <span>Total:</span>
                <strong id="cartTotal">Rs. <?php echo number_format($cartTotal); ?></strong>
            </div>
            <?php if (count($cartItems) > 0): ?>
                <form action="../cart/checkout_action.php" method="POST">
                    <button type="submit" class="checkout-btn">Checkout</button>
                </form>
            <?php else: ?>
                <button class="checkout-btn">Checkout</button>
            <?php endif; ?>
        </div>
    </div>
    <header>
        <nav>
            <div class="menu-icon" id="menuIcon">☰</div>

            <div class="left-section">
                <div class="nav-links-container" id="navContainer">
                    <ul class="nav-links">
                        <li><a href="../index.php">Home</a></li>
                        <li><a href="../category/category.php">Products</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="../orders/orders.html">My Orders</a></li>
                    </ul>
                    <div class="mobile-menu-icons">
                        <a href="#" class="mobile-icon-link">
                            <img height="25px" src="../assets/svg/user.svg" alt="User">
                            <span>My Account</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="center-section">
                <div class="logo">
                    <img src="../assets/logo/logo_blue_bgr.png" alt="Logo" />
                </div>
            </div>

            <div class="right-section">
                <form action="../category/category.php" method="GET" class="search-box desktop-search">
                    <input name="search" id="searchInput" placeholder="Search">
                    <button type="button" id="clearBtn"><span class="material-symbols-outlined">close</span></button>
                    <div class="vline"></div>
                    <button type="submit"><span class="material-symbols-outlined search-icon">search</span></button>
                </form>

                <span class="material-symbols-outlined mobile-search-icon">search</span>

                <a class="nav-svg no-show-svg" href="#"><img src="../assets/svg/user.svg" alt=""></a>
                <a class="nav-svg" href="javascript:void(0)" onclick="openCart()">
                    <img src="../assets/svg/cart.svg" alt="Cart">
                </a>
            </div>
        </nav>

        <div class="mobile-search-bar">
            <div class="search-box">
                <input id="mobileSearchInput" placeholder="Search" />
                <button id="mobileClearBtn"><span class="material-symbols-outlined">close</span></button>
                <div class="vline"></div>
                <button><span class="material-symbols-outlined search-icon">search</span></button>
            </div>
        </div>
    </header>

    <section class="profile-page">
        <div class="container">

            <div class="sidebar">
                <img src="user.png" alt="User">
                <h2 id="displayName"><?php echo htmlspecialchars($user['name']); ?></h2>
                <p class="email" id="displayEmail"><?php echo htmlspecialchars($user['email']); ?></p>

                <button class="btn-primary" onclick="openProfileEdit()">Edit Profile</button>
            </div>

            <div class="content">

                <div class="section" id="viewProfile">
                    <h3>Account Information</h3>
                    <div class="row">
                        <span>Name</span>
                        <span id="username"><?php echo htmlspecialchars($user['name']); ?></span>
                    </div>
                    <div class="row">
                        <span>Phone</span>
                        <span id="phone"><?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'Not set'; ?></span>
                    </div>
                    <div class="row">
                        <span>Member Since</span>
                        <span><?php echo date("F Y", strtotime($user['created_at'])); ?></span>
                    </div>
                </div>

                <div class="section" id="editProfile" style="display:none;">
                    <h3>Edit Profile</h3>
                    <form action="update_profile_action.php" method="POST">
                        <label>Name</label>
                        <input type="text" name="name" id="editUsername" value="<?php echo htmlspecialchars($user['name']); ?>" required>

                        <label>Phone</label>
                        <input type="text" name="phone" id="editPhone" value="<?php echo htmlspecialchars($user['phone']); ?>">

                        <br><br>
                        <button type="submit" class="btn-primary">Save</button>
                        <button type="button" class="btn-outline" onclick="cancelProfile()">Cancel</button>
                    </form>
                </div>

                <div class="section" id="viewAddress">
                    <h3>Shipping Address</h3>
                    <p id="address">No saved address found (Address is saved per Order)</p>
                    <button class="btn-link" onclick="openAddressEdit()">Edit Address</button>
                </div>

                <div class="section" id="editAddress" style="display:none;">
                    <h3>Edit Address</h3>
                    <input type="text" id="editAddressInput">
                    <br><br>
                    <button class="btn-primary" onclick="saveAddress()">Save</button>
                    <button class="btn-outline" onclick="cancelAddress()">Cancel</button>
                </div>

                <div class="section">
                    <h3>Order History</h3>
                    <div class="table-wrapper">
                        <?php if (count($orders) > 0): ?>
                            <table>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                </tr>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#GG<?php echo $order['order_id']; ?></td>
                                        <td><?php echo date("d M Y", strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <span style="
                                                <?php 
                                                    if($order['status']=='pending') echo 'color:orange;';
                                                    elseif($order['status']=='delivered' || $order['status']=='success') echo 'color:green;';
                                                    else echo 'color:red;';
                                                ?>
                                            ">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td>Rs. <?php echo number_format($order['total_amount']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php else: ?>
                            <p>No orders found.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="section" id="securitySection">
                    <h3>Security</h3>
                    <button class="btn-outline" onclick="openPassword()">Change Password</button>
                    
                    <form action="logout.php" method="post" style="margin-top:10px;">
                        <button type="submit" class="btn-danger">Logout</button>
                    </form>
                </div>

                <div class="section" id="changePassword" style="display:none;">
                    <h3>Change Password</h3>
                    <form action="change_password_action.php" method="POST">
                        <label>Current Password</label>
                        <input type="password" name="current_password" id="currentPassword" required>

                        <label>New Password</label>
                        <input type="password" name="new_password" id="newPassword" required>

                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" id="confirmPassword" required>

                        <br><br>
                        <button type="submit" class="btn-primary">Update Password</button>
                        <button type="button" class="btn-outline" onclick="cancelPassword()">Cancel</button>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <script src="user.js"></script>
    <script src="../cart/cart.js"></script>
</body>
</html>