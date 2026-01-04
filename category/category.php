<?php
// 1. Start Session and Include Functions
// Adjust paths if your folder structure is different
require_once '../Backend/config/session_manager.php';
require_once '../Backend/config/functions.php';

$conn = dbConnect();

// === NEW: AJAX HANDLER ===
// If 'ajax_query' is in the URL, return JSON data and STOP loading the rest of the page.
if (isset($_GET['ajax_query'])) {
    $query = $_GET['ajax_query'];
    $suggestions = getSearchSuggestions($conn, $query);

    // Set header to JSON so JS understands it
    header('Content-Type: application/json');
    echo json_encode($suggestions);
    exit(); // Important: Stop here so we don't load the HTML!
}
// =========================

// 2. Check Login Status
$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $isLoggedIn ? $_SESSION['user_id'] : 0;

// 3. Define Paths
// IMPORTANT: Update this to the actual path of your login file
$loginPagePath = "../Login/user/login_user.php";
$profilePagePath = "../user profile/user.php";

// Determine where links should go
$accountLink = $isLoggedIn ? $profilePagePath : $loginPagePath;

// 4. Fetch Cart Data (If logged in)
$cartItems = [];
$cartTotal = 0;
if ($isLoggedIn) {
    // We use the function we created earlier
    $cartItems = getCartItems($conn, $user_id);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - GearGo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="category.css">
    <link rel="stylesheet" href="../cart/cart.css">
</head>
<style>

</style>

<body>
    <!-- ================= CART START ================= -->
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
                    // Placeholder image logic
                    $imgSrc = !empty($item['image']) ? "../" . $item['image'] : "../headphone1.png";
                    ?>
                    <div class="cart-item">
                        <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">

                        <div class="cart-item-details">
                            <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                            <p>Rs. <?php echo number_format($item['price']); ?></p>

                            <form action="../cart/update_quantity.php" method="POST" style="display:inline-block;">
                                <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1"
                                    onchange="this.form.submit()" style="width: 50px; padding: 5px;">
                            </form>
                        </div>

                        <form action="../cart/remove_cart_item.php" method="POST" style="display:inline;">
                            <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                            <button type="submit" class="remove-btn">&times;</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php elseif (!$isLoggedIn): ?>
                <p class="empty-cart" style="display:block;">Please <a href="<?php echo $loginPagePath; ?>">login</a> to
                    view your cart.</p>
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
                <form action="../checkout/checkout.php" method="POST">
                    <button type="submit" class="checkout-btn">Checkout</button>
                </form>
            <?php else: ?>
                <button class="checkout-btn" onclick="window.location.href='<?php echo $loginPagePath; ?>'">
                    <?php echo $isLoggedIn ? 'Checkout' : 'Login to Checkout'; ?>
                </button>
            <?php endif; ?>
        </div>
    </div>
    <!-- ================= CART END ================= -->
    <!-- header section -->
    <header>
        <nav>
            <div class="menu-icon" id="menuIcon">☰</div>

            <div class="left-section">
                <div class="nav-links-container" id="navContainer">
                    <ul class="nav-links">
                        <li><a href="../index.php">Home</a></li>
                        <li><a href="category.php">Products</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="<?php echo $isLoggedIn ? '../orders/orders.php' : $loginPagePath; ?>">My Orders</a>
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
                <form action="../search/search.php" method="GET" class="search-box desktop-search"
                    style="position:relative;">
                    <input name="search" id="searchInput" placeholder="Search" autocomplete="off">

                    <button type="button" id="clearBtn"><span class="material-symbols-outlined">close</span></button>
                    <div class="vline"></div>
                    <button type="submit"><span class="material-symbols-outlined search-icon">search</span></button>

                    <div id="searchResultsList" class="search-suggestions-box"></div>
                </form>

                <span class="material-symbols-outlined mobile-search-icon">search</span>

                <a class="nav-svg no-show-svg" href="<?php echo $accountLink; ?>">
                    <img src="../assets/svg/user.svg" alt="User Profile">
                </a>

                <a class="nav-svg" href="javascript:void(0)"
                    onclick="<?php echo $isLoggedIn ? 'openCart()' : "window.location.href='$loginPagePath'"; ?>">
                    <img src="../assets/svg/cart.svg" alt="Cart">
                </a>
            </div>
        </nav>

        <div class="mobile-search-bar">
            <form action="../search/search.php" method="GET" class="search-box" style="position:relative;">
                <input name="search" id="mobileSearchInput" placeholder="Search" autocomplete="off" />

                <button type="button" id="mobileClearBtn"><span class="material-symbols-outlined">close</span></button>
                <div class="vline"></div>
                <button type="submit"><span class="material-symbols-outlined search-icon">search</span></button>

                <div id="mobileSearchResultsList" class="search-suggestions-box"></div>
            </form>
        </div>
    </header>

    <!--  ================= header end ================= -->
    <?php

    // 1. Capture Inputs
    $category_id = isset($_GET['category']) ? $_GET['category'] : 'all';
    $sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'default';
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $limit = 10; // As requested: 5 records per page
    
    // 2. Fetch Data
    $categories = getCategories($conn);
    $data = search_by_category($conn, $category_id, $sort_by, $page, $limit);
    $products = $data['products'];
    $totalPages = $data['totalPages'];
    ?>


    <div class="page-container">
        <div class="shop-top-bar">

            <div class="filter-box">
                <label>Category</label>
                <select id="categoryFilter" onchange="applyFilters()">
                    <option value="all" <?php if ($category_id == 'all')
                        echo 'selected'; ?>>All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['category_id']; ?>" <?php if ($category_id == $cat['category_id'])
                               echo 'selected'; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-box">
                <label>Sort By</label>
                <select id="sortFilter" onchange="applyFilters()">
                    <option value="default" <?php if ($sort_by == 'default')
                        echo 'selected'; ?>>Default</option>
                    <option value="price_low_high" <?php if ($sort_by == 'price_low_high')
                        echo 'selected'; ?>>Price: Low
                        to High</option>
                    <option value="price_high_low" <?php if ($sort_by == 'price_high_low')
                        echo 'selected'; ?>>Price: High
                        to Low</option>
                </select>
            </div>

        </div>

        <section class="products">
            <div class="products-grid">

                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <?php
                        $imgSrc = !empty($product['image']) ? "../" . $product['image'] : "../headphone1.png";
                        $productLink = "../product page/product.php?id=" . $product['product_id'];
                        ?>

                        <div class="product-card-premium big-card">
                            <a href="<?php echo $productLink; ?>" style="text-decoration:none; color:inherit;">
                                <div class="product-image-container-premium">
                                    <img src="<?php echo $imgSrc; ?>" height="200px"
                                        alt="<?php echo htmlspecialchars($product['title']); ?>" class="product-image-premium">
                                </div>

                                <div class="product-info-section">
                                    <h3 class="product-title-premium"><?php echo htmlspecialchars($product['title']); ?></h3>

                                    <div class="price-comparison-section">
                                        <span
                                            class="current-price-premium"><small>Rs.</small><?php echo number_format($product['price']); ?></span>
                                    </div>
                                </div>
                            </a>

                            <div class="product-info-section" style="padding-top:0;">
                                <form action="../cart/add_to_cart.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                    <input type="hidden" name="quantity" value="1">

                                    <?php if ($isLoggedIn): ?>
                                        <button type="submit" class="add-to-cart-btn-premium">ADD TO CART</button>
                                    <?php else: ?>
                                        <button type="button" class="add-to-cart-btn-premium"
                                            onclick="window.location.href='<?php echo $loginPagePath; ?>'">
                                            ADD TO CART
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="grid-column: 1/-1; text-align: center;">No products found.</p>
                <?php endif; ?>

            </div>
        </section>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?category=<?php echo $category_id; ?>&sort=<?php echo $sort_by; ?>&page=<?php echo $page - 1; ?>">«
                        Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?category=<?php echo $category_id; ?>&sort=<?php echo $sort_by; ?>&page=<?php echo $i; ?>" class="<?php if ($i == $page)
                                 echo 'active'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?category=<?php echo $category_id; ?>&sort=<?php echo $sort_by; ?>&page=<?php echo $page + 1; ?>">Next
                        »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function applyFilters() {
            const category = document.getElementById('categoryFilter').value;
            const sort = document.getElementById('sortFilter').value;
            // Reset to page 1 when filter changes
            window.location.href = `?category=${category}&sort=${sort}&page=1`;
        }
    </script>

    <script src="category.js"></script>
    <script src="../cart/cart.js"></script>

</body>

</html>