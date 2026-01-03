<?php
// 1. Start Session and Include Functions
require_once '../Backend/config/session_manager.php';
require_once '../Backend/config/functions.php';

$conn = dbConnect();

// 2. Check Login Status
$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $isLoggedIn ? $_SESSION['user_id'] : 0;

// 3. Define Paths
$loginPagePath = "../Login/user/login_user.php";
$profilePagePath = "../user profile/user.php";
$accountLink = $isLoggedIn ? $profilePagePath : $loginPagePath;

// 4. Fetch Cart Data (If logged in)
$cartItems = [];
$cartTotal = 0;
if ($isLoggedIn) {
    $cartItems = getCartItems($conn, $user_id);
}

// 5. Fetch Product Data
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$display = getProductBy_id($conn, $id);

if (!$display) {
    echo "Product not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($display['title']); ?> | GearGo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="product.css">
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
            <?php if ($isLoggedIn && count($cartItems) > 0): ?>
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
                            <form action="../cart/update_quantity.php" method="POST" style="display:inline-block;">
                                <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" onchange="this.form.submit()" style="width: 50px; padding: 5px;">
                            </form>
                        </div>

                        <form action="../cart/remove_cart_item.php" method="POST" style="display:inline;">
                            <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                            <button type="submit" class="remove-btn">&times;</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php elseif (!$isLoggedIn): ?>
                <p class="empty-cart" style="display:block;">Please <a href="<?php echo $loginPagePath; ?>">login</a> to view your cart.</p>
            <?php else: ?>
                <p class="empty-cart" style="display:block;">Your cart is empty</p>
            <?php endif; ?>
        </div>

        <div class="cart-footer">
            <div class="cart-total">
                <span>Total:</span>
                <strong id="cartTotal">Rs. <?php echo number_format($cartTotal); ?></strong>
            </div>

            <?php if ($isLoggedIn && count($cartItems) > 0): ?>
                <form action="../cart/checkout_action.php" method="POST">
                    <button type="submit" class="checkout-btn">Checkout</button>
                </form>
            <?php else: ?>
                <button class="checkout-btn" onclick="window.location.href='<?php echo $loginPagePath; ?>'">
                    <?php echo $isLoggedIn ? 'Checkout' : 'Login to Checkout'; ?>
                </button>
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
                        <li><a href="../orders/orders.php">My Orders</a></li>
                    </ul>
                    <div class="mobile-menu-icons">
                        <a href="<?php echo $accountLink; ?>" class="mobile-icon-link">
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

                <a class="nav-svg no-show-svg" href="<?php echo $accountLink; ?>"><img src="../assets/svg/user.svg" alt=""></a>
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

    <section class="products">
        <div class="container">
            <div class="product-page">
                <div class="product-images">
                    <?php $mainImg = !empty($display['image']) ? '../' . $display['image'] : '../headphone1.png'; ?>
                    <img src="<?php echo $mainImg; ?>" class="main-image" id="mainImage" alt="Product Image">
                </div>

                <div class="product-info">
                    <h1 class="product-title"><?php echo htmlspecialchars($display['title']); ?></h1>
                    <div class="price">Rs. <?php echo number_format($display['price']); ?></div>

                    <p class="description">
                        <?php echo nl2br(htmlspecialchars($display['description'])); ?>
                    </p>

                    <div class="actions" style="display: flex; gap: 10px;">
                        
                        <form action="../cart/add_to_cart.php" method="POST" style="flex: 1;">
                            <input type="hidden" name="product_id" value="<?php echo $display['product_id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            
                            <?php if ($isLoggedIn): ?>
                                <button type="submit" class="btn btn-cart" style="width: 100%;">Add to Cart</button>
                            <?php else: ?>
                                <button type="button" class="btn btn-cart" style="width: 100%;" onclick="window.location.href='<?php echo $loginPagePath; ?>'">Add to Cart</button>
                            <?php endif; ?>
                        </form>

                        <button class="btn btn-buynow" style="flex: 1;">Buy Now</button>
                    </div>
                </div>
            </div>

            <div class="related-products-section">
                <h2>You Might Also Like</h2>

                <div class="related-products-grid">
                    <?php
                    $products = Random_products(8, $conn);
                    foreach ($products as $product) {
                        $relImg = !empty($product['image']) ? '../' . $product['image'] : '../headphone1.png';
                        $productLink = "../product page/product.php?id=" . $product['product_id'];
                    ?>

                        <div class="product-card-premium">
                            <div class="product-image-container-premium">
                                <a href="<?php echo $productLink; ?>">
                                    <img src="<?php echo $relImg; ?>" height="200px" alt="<?php echo htmlspecialchars($product['title']); ?>" class="product-image-premium">
                                </a>
                            </div>

                            <div class="product-info-section">
                                <a href="<?php echo $productLink; ?>" style="text-decoration:none; color:inherit;">
                                    <h3 class="product-title-premium"><?php echo htmlspecialchars($product['title']); ?></h3>
                                    <div class="price-comparison-section">
                                        <span class="current-price-premium"><small>Rs.</small><?php echo number_format($product['price']); ?></span>
                                        <span class="original-price-premium"><small>Rs.</small><?php echo number_format($product['price'] + ($product['price'] * 0.1)); ?></span>
                                    </div>
                                </a>
                                
                                <form action="../cart/add_to_cart.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    
                                    <?php if ($isLoggedIn): ?>
                                        <button type="submit" class="add-to-cart-btn-premium">ADD TO CART</button>
                                    <?php else: ?>
                                        <button type="button" class="add-to-cart-btn-premium" onclick="window.location.href='<?php echo $loginPagePath; ?>'">ADD TO CART</button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>

                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</body>

<script src="../cart/cart.js"></script>

</html>