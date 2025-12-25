<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GearGo | Product Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="product.css">
</head>

<body>
    <?php
    require_once '../backend/Config/functions.php';
    $conn = dbConnect();
    $id = $_GET['id'];
    $display = getProductBy_id($conn, $id);
    ?>
    <header>
        <nav>
            <div class="menu-icon" id="menuIcon">☰</div>

            <div class="left-section">
                <div class="nav-links-container" id="navContainer">
                    <ul class="nav-links">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Products</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Contact</a></li>
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
                <!-- Desktop search bar -->
                <div class="search-box desktop-search">
                    <input id="searchInput" placeholder="Search">
                    <button id="clearBtn"><span class="material-symbols-outlined">close</span></button>
                    <div class="vline"></div>
                    <button><span class="material-symbols-outlined search-icon">search</span></button>
                </div>

                <!-- Mobile search icon -->
                <span class="material-symbols-outlined mobile-search-icon">search</span>

                <a class="nav-svg no-show-svg" href=""><img src="../assets/svg/user.svg" alt=""></a>
                <a class="nav-svg" href=""><img src="../assets/svg/cart.svg" alt=""></a>
            </div>
        </nav>

        <!-- Mobile search bar -->
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
                <!-- Left column: Image -->
                <div class="product-images">
                    <img src="<?php echo '../' . $display['image'] ?>" class="main-image" id="mainImage" alt="Product Image">
                </div>

                <!-- Right column: Title + Info -->
                <div class="product-info">
                    <h1 class="product-title"><?php echo $display['title'] ?></h1>
                    <div class="price">Rs. <?php echo $display['price'] ?></div>

                    <p class="description">
                        <?php echo $display['description'] ?>
                    </p>

                    <div class="actions">
                        <button class="btn btn-cart">Add to Cart</button>
                        <button class="btn btn-buynow">Buy Now</button>
                    </div>
                </div>
            </div>

            <div class="related-products-section">
                <h2>You Might Also Like</h2>

                <div class="related-products-grid">
                    <?php
                    $products = Random_products(8, $conn);
                    $product = [];

                    foreach ($products as $product) {
                        ?>
                        <a href="../product page/product.php?id=<?php echo $product['product_id'] ?>">
                            <div class="product-card-premium">
                                <div class="product-image-container-premium">
                                    <img src="<?php echo '../' . $product['image'] ?>" height="200px"
                                        alt="<?php echo $product['title'] ?>" class="product-image-premium">
                                </div>
                                <div class="product-info-section">
                                    <h3 class="product-title-premium"><?php echo $product['title'] ?></h3>
                                    <div class="price-comparison-section">
                                        <span
                                            class="current-price-premium"><small>Rs.</small><?php echo $product['price'] ?></span>
                                        <span
                                            class="original-price-premium"><small>Rs.</small><?php echo $product['price'] + ($product['price'] * 0.1) ?></span>
                                    </div>
                                    <button class="add-to-cart-btn-premium">ADD TO CART</button>
                                </div>
                            </div>
                        </a>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
</body>

</html>