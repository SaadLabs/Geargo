<?php
session_start();
require_once '../../Backend/config/functions.php'; // Adjust path as needed
$conn = dbConnect();

// 1. Security Check: Staff Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../../Login/admin/login_admin.php?error=" . urlencode("Unauthorized access"));
    exit();
}

$staff_id = $_SESSION['user_id'];

// 2. Fetch Dashboard Stats
// Pending Orders
$pendingSql = "SELECT COUNT(*) as count FROM `order` WHERE order_status = 'Pending'";
$pendingCount = mysqli_fetch_assoc(mysqli_query($conn, $pendingSql))['count'];

$processSql = "SELECT COUNT(*) as count FROM `order` WHERE order_status = 'Processing'";
$processCount = mysqli_fetch_assoc(mysqli_query($conn, $processSql))['count'];

$shippedSql = "SELECT COUNT(*) as count FROM `order` WHERE order_status = 'Shipped'";
$shippedCount = mysqli_fetch_assoc(mysqli_query($conn, $shippedSql))['count'];

// Low Stock (< 10 items)
$lowStockSql = "SELECT COUNT(*) as count FROM product WHERE stock_quantity < 10";
$lowStockCount = mysqli_fetch_assoc(mysqli_query($conn, $lowStockSql))['count'];

// 3. Fetch Data
// Inventory
$inventoryResult = mysqli_query($conn, "SELECT * FROM product ORDER BY stock_quantity ASC");

// Active Orders (Pending or Shipped, but not Delivered)
$ordersSql = "SELECT o.*, u.name 
              FROM `order` o 
              JOIN user u ON o.user_id = u.user_id 
              WHERE o.order_status NOT IN ('Delivered')
              ORDER BY o.order_date ASC";
$ordersResult = mysqli_query($conn, $ordersSql);

// Staff Profile
$profileSql = "SELECT * FROM user WHERE user_id = '$staff_id'";
$profile = mysqli_fetch_assoc(mysqli_query($conn, $profileSql));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GearGo Staff Portal</title>
    <link rel="stylesheet" href="staff.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body>

    <div class="sidebar">
        <h2 class="logo">GearGo Staff</h2>
        <ul>
            <li class="active" onclick="showSection('staff-dashboard', this)">
                <i class="bi bi-house-door"></i> Overview
            </li>
            <li onclick="showSection('products', this)">
                <i class="bi bi-box-seam"></i> Inventory
            </li>
            <li onclick="showSection('orders', this)">
                <i class="bi bi-cart-check"></i> Orders
            </li>
            <hr>
            <li onclick="showSection('profile', this)">
                <i class="bi bi-person-circle"></i> My Profile
            </li>
            <li class="logout" onclick="window.location.href='logout.php'">
                <i class="bi bi-box-arrow-right"></i> Logout
            </li>
        </ul>
    </div>

    <div class="main">

        <section id="staff-dashboard" class="content-section">
            <h1>Staff Overview</h1>
            <div class="cards">
                <div class="card">Pending Orders<br><span id="pendingOrders"><?php echo $pendingCount; ?></span></div>

                <div class="card">Processing Orders<br><span id="pendingOrders"><?php echo $processCount; ?></span>
                </div>

                <div class="card">Shipped Orders<br><span id="pendingOrders"><?php echo $shippedCount; ?></span></div>
                <div class="card">Low Stock Items<br><span id="lowStockCount"><?php echo $lowStockCount; ?></span></div>
            </div>
        </section>

        <section id="products" class="content-section hidden">
            <div class="section-header">
                <h1>Inventory</h1>
            </div>
            <input type="text" id="searchInput" placeholder="Search inventory..." onkeyup="searchProduct()">

            <table id="productTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Current Stock</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($prod = mysqli_fetch_assoc($inventoryResult)): ?>
                        <tr>
                            <td><?php echo $prod['product_id']; ?></td>
                            <td><?php echo htmlspecialchars($prod['title']); ?></td>
                            <td class="stock"><?php echo $prod['stock_quantity']; ?></td>
                            <td class="status">
                                <?php if ($prod['stock_quantity'] > 10): ?>
                                    <span style="color: green;">In Stock</span>
                                <?php elseif ($prod['stock_quantity'] > 0 && $prod['stock_quantity'] <= 10): ?>
                                    <span style="color: orange;">Low Stock</span>
                                <?php else: ?>
                                    <span style="color: red;">Out of Stock</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn-edit"
                                    onclick="openStockModal('<?php echo $prod['product_id']; ?>', '<?php echo $prod['stock_quantity']; ?>')">
                                    Update
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <section id="orders" class="content-section hidden">
            <h1>Active Orders</h1>
            <table id="ordersTable">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($ordersResult) > 0): ?>
                        <?php while ($order = mysqli_fetch_assoc($ordersResult)): ?>
                            <tr>
                                <td>#<?php echo $order['order_id']; ?></td>
                                <td><?php echo htmlspecialchars($order['name']); ?></td>
                                <td>Rs. <?php echo number_format($order['total_amount']); ?></td>

                                <td><?php echo ucfirst($order['order_status']); ?></td>

                                <td>
                                    <form action="staff_update_order.php" method="POST"
                                        style="display: flex; align-items: center; gap: 5px;">
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">

                                        <select name="status" style="padding: 5px; border-radius: 4px; border: 1px solid #ccc;">
                                            <option value="Pending" <?php echo ($order['order_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Processing" <?php echo ($order['order_status'] == 'Processing') ? 'selected' : ''; ?>>Processing</option>
                                            <option value="Shipped" <?php echo ($order['order_status'] == 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="Delivered" <?php echo ($order['order_status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                                        </select>

                                        <button type="submit" class="btn-edit"
                                            style="padding: 5px 10px; cursor: pointer;">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No active orders.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <section id="profile" class="content-section hidden">
            <h1>My Profile</h1>
            <div class="card" style="width: 100%; max-width: 400px;">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($profile['name']); ?></p>
                <p><strong>Role:</strong> Staff</p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($profile['email']); ?></p>
                <br>
                <button class="btn-primary" onclick="openPasswordModal()">Change Password</button>
            </div>
        </section>

    </div>

    <div class="modal" id="passwordModal">
        <div class="modal-content">
            <h2>Change Password</h2>
            <form action="staff_change_password.php" method="POST">
                <input type="password" name="current_password" placeholder="Current Password" required>
                <input type="password" name="new_password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                <div class="modal-actions">
                    <button type="submit" class="btn-primary">Update</button>
                    <button type="button" class="btn-delete" onclick="closePasswordModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="stockModal">
        <div class="modal-content">
            <h2>Update Stock</h2>
            <form action="staff_update_stock.php" method="POST">
                <input type="hidden" name="product_id" id="modalProductId">
                <label>New Quantity:</label>
                <input type="number" name="quantity" id="modalStockQty" required min="0">
                <div class="modal-actions">
                    <button type="submit" class="btn-primary">Save</button>
                    <button type="button" class="btn-delete" onclick="closeStockModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="staff.js"></script>
    <script>
        // Extra JS for the Stock Modal
        function openStockModal(id, currentQty) {
            document.getElementById('stockModal').style.display = 'flex';
            document.getElementById('modalProductId').value = id;
            document.getElementById('modalStockQty').value = currentQty;
        }

        function closeStockModal() {
            document.getElementById('stockModal').style.display = 'none';
        }
    </script>

    <?php if (isset($_GET['msg'])): ?>
        <script>
            // Display Success Message
            alert("<?php echo htmlspecialchars($_GET['msg']); ?>");

            // Clean the URL (remove ?msg=...) so it doesn't show again on refresh
            if (window.history.replaceState) {
                const url = new URL(window.location.href);
                url.searchParams.delete('msg');
                window.history.replaceState(null, '', url.toString());
            }
        </script>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <script>
            // Display Error Message
            alert("Error: <?php echo htmlspecialchars($_GET['error']); ?>");

            // Clean the URL (remove ?error=...)
            if (window.history.replaceState) {
                const url = new URL(window.location.href);
                url.searchParams.delete('error');
                window.history.replaceState(null, '', url.toString());
            }
        </script>
    <?php endif; ?>

</body>

</html>