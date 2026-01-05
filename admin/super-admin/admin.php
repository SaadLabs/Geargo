<?php
// 1. Session & Security
session_start();
require_once '../../Backend/config/functions.php'; // Adjust path if needed
$conn = dbConnect();

// Security: Check if user is logged in and is an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../Login/admin/login_admin.php?error=" . urlencode("Unauthorized access"));
    exit();
}

// 2. Dashboard Logic (Fetch Counts)
// Orders Count
$orderCountResult = mysqli_query($conn, "SELECT COUNT(*) as count FROM `order`");
$orderCount = mysqli_fetch_assoc($orderCountResult)['count'];

// Sales Total
$salesResult = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM `order` WHERE order_status != 'Cancelled'");
$salesData = mysqli_fetch_assoc($salesResult);
$totalSales = $salesData['total'] ? $salesData['total'] : 0;

// Products Count
$prodCountResult = mysqli_query($conn, "SELECT COUNT(*) as count FROM product");
$productCount = mysqli_fetch_assoc($prodCountResult)['count'];

// Users Count (Customers only)
$userCountResult = mysqli_query($conn, "SELECT COUNT(*) as count FROM user  ");
$userCount = mysqli_fetch_assoc($userCountResult)['count'];


// 3. Fetch Data for Tables
// Fetch Products
$productsQuery = "SELECT * FROM product ORDER BY product_id ASC";
$productsResult = mysqli_query($conn, $productsQuery);

// Fetch Orders (Join with User table to get names)
$ordersQuery = "SELECT o.*, u.name 
                FROM `order` o 
                JOIN user u ON o.user_id = u.user_id 
                ORDER BY o.order_date DESC";
$ordersResult = mysqli_query($conn, $ordersQuery);

// Fetch Users (Customers)
$usersQuery = "SELECT * FROM user";
$usersResult = mysqli_query($conn, $usersQuery);

// Fetch Staff
$staffQuery = "SELECT * FROM user WHERE role = 'staff'"; // Assuming staff are in user table
$staffResult = mysqli_query($conn, $staffQuery);

// Fetch Categories for the dropdown
$categoryQuery = "SELECT * FROM category";
$categoryResult = mysqli_query($conn, $categoryQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GearGo Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body>

    <div class="sidebar">
        <h2 class="logo">GearGo</h2>
        <ul>
            <li class="active" onclick="showSection('dashboard', this)">
                <i class="bi bi-speedometer2"></i> Dashboard
            </li>
            <li onclick="showSection('products', this)">
                <i class="bi bi-box-seam"></i> Products
            </li>
            <li onclick="showSection('orders', this)">
                <i class="bi bi-cart-check"></i> Orders
            </li>
            <li onclick="showSection('users', this)">
                <i class="bi bi-people"></i> Users
            </li>
            <li onclick="showSection('staff', this)">
                <i class="bi bi-person-badge"></i> Staff
            </li>

            <li class="logout" onclick="window.location.href='logout.php'">
                <i class="bi bi-box-arrow-right"></i> Logout
            </li>
        </ul>
    </div>

    <div class="main">

        <section id="dashboard" class="content-section">
            <div class="section-header">
                <h1>Dashboard</h1>
                <select id="dashboardFilter" onchange="updateDashboard()">
                    <option value="all">All Time</option>
                </select>
            </div>

            <div class="cards">
                <div class="card">Orders<br><span id="orderCount"><?php echo $orderCount; ?></span></div>
                <div class="card">Sales<br><span id="salesAmount">Rs. <?php echo number_format($totalSales); ?></span>
                </div>
                <div class="card">Products<br><span id="productCount"><?php echo $productCount; ?></span></div>
                <div class="card">Users<br><span id="userCount"><?php echo $userCount; ?></span></div>
            </div>
        </section>


        <section id="products" class="content-section hidden">
            <div class="section-header">
                <h1>Products</h1>
                <button class="btn-primary" onclick="openModal()">+ Add Product</button>
            </div>

            <input type="text" id="searchInput" placeholder="Search product..." onkeyup="searchProduct()">

            <table id="productTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($productsResult) > 0): ?>
                        <?php while ($prod = mysqli_fetch_assoc($productsResult)): ?>

                            <?php
                            // Determine class based on status
                            $rowClass = ($prod['is_active'] == 0) ? 'inactive-row' : '';

                            // Handle missing images safely
                            $imagePath = !empty($prod['image']) ? "../../" . $prod['image'] : "../../assets/no-image.png";
                            ?>

                            <tr class="<?php echo $rowClass; ?>">
                                <td><?php echo $prod['product_id']; ?></td>

                                <td>
                                    <img src="<?php echo $imagePath; ?>" alt="Img"
                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                                </td>

                                <td><?php echo htmlspecialchars($prod['title']); ?></td>
                                <td>Rs. <?php echo number_format($prod['price']); ?></td>
                                <td><?php echo $prod['stock_quantity']; ?></td>
                                <td>
                                    <a href="edit_product.php?id=<?php echo $prod['product_id']; ?>" class="btn-edit">Edit</a>

                                    <form action="delete_product_action.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="product_id" value="<?php echo $prod['product_id']; ?>">

                                        <?php if ($prod['is_active'] == 1): ?>
                                            <button type="submit" name="action" value="delete" class="btn-delete"
                                                onclick="return confirm('Soft delete this product?');">
                                                Delete
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" name="action" value="restore" class="btn-primary"
                                                style="background-color: #28a745;"
                                                onclick="return confirm('Restore this product?');">
                                                Restore
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>


        <section id="orders" class="content-section hidden">
            <div class="section-header">
                <h1>Orders</h1>
            </div>
            <input type="text" id="searchOrders" placeholder="Search orders..." onkeyup="searchOrders()">
            <table id="ordersTable">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
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
                                    <form action="admin_update_order.php" method="POST"
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

        <section id="users" class="content-section hidden">
            <div class="section-header">
                <h1>Users</h1>
            </div>
            <input type="text" id="searchUsers" placeholder="Search users..." onkeyup="searchUsers()">
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Created At</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($usersResult) > 0): ?>
                        <?php while ($user = mysqli_fetch_assoc($usersResult)): ?>
                            <tr>
                                <td><?php echo $user['user_id']; ?></td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                <form action="edit_role.php" method="POST">
                                    <td>
                                        <?php
                                        // Assuming $user['role'] contains the value from your database (e.g., 'staff')
                                        $roles = ['customer', 'staff', 'admin'];
                                        ?>
                                        <select name="role" id="role">
                                            <?php foreach ($roles as $role): ?>
                                                <option value="<?php echo $role; ?>" <?php echo ($user['role'] == $role) ? 'selected' : '';?>>
                                                    <?php echo ucfirst($role); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                        <button type="submit" class="btn-edit" style="background-color: #4d77ffff;">change role</button>
                                    </td>
                                </form>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>


        <section id="staff" class="content-section hidden">
            <div class="section-header">
                <h1>Staff</h1>
                <!-- <button class="btn-primary" onclick="openStaffModal()">+ Add Staff</button> -->
            </div>

            <table id="staffTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($staffResult) > 0): ?>
                        <?php while ($staff = mysqli_fetch_assoc($staffResult)): ?>
                            <tr>
                                <td><?php echo $staff['user_id']; ?></td>
                                <td><?php echo htmlspecialchars($staff['name']); ?></td>
                                <td><?php echo htmlspecialchars($staff['email']); ?></td>
                                <form action="edit_role.php" method="POST">
                                    <td>
                                        <?php
                                        // Assuming $user['role'] contains the value from your database (e.g., 'staff')
                                        $roles = ['customer', 'staff', 'admin'];
                                        ?>
                                        <select name="role" id="role">
                                            <?php foreach ($roles as $role): ?>
                                                <option value="<?php echo $role; ?>" <?php echo ($staff['role'] == $role) ? 'selected' : ''; ?>>
                                                    <?php echo ucfirst($role); // Capitalizes the first letter for display ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="hidden" name="user_id" value="<?php echo $staff['user_id']; ?>">
                                        <button type="submit" class="btn-edit" style="background-color: #4d77ffff;">change role</button>
                                    </td>
                                </form>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No staff members found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

    </div>

    <div class="modal" id="productModal">
        <div class="modal-content">
            <h2>Add Product</h2>
            <form action="add_product_action.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="title" placeholder="Product title" required>

                <select name="category_id" required
                    style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px;">
                    <option value="">Select Category</option>
                    <?php
                    if (mysqli_num_rows($categoryResult) > 0) {
                        // Reset the pointer just in case the result was used before
                        mysqli_data_seek($categoryResult, 0);
                        while ($cat = mysqli_fetch_assoc($categoryResult)) {
                            // CHANGE 'category_id' or 'name' here if your database columns are different
                            echo '<option value="' . $cat['category_id'] . '">' . htmlspecialchars($cat['name']) . '</option>';
                        }
                    }
                    ?>
                </select>

                <input type="text" name="brand" placeholder="Brand" required>
                <input type="text" name="color" placeholder="Color" required>
                <input type="number" name="price" placeholder="Price" required>
                <input type="number" name="quantity" placeholder="Stock Quantity" required>

                <input type="hidden" name="is_active" value="1" required>

                <textarea name="description" placeholder="Description"></textarea>
                <input type="file" name="product_image" required>

                <div class="modal-actions">
                    <button type="submit" class="btn-primary">Save</button>
                    <button type="button" class="btn-remove" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="staffModal">
        <div class="modal-content">
            <h2>Add Staff</h2>
            <form action="add_staff_action.php" method="POST">
                <input type="text" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>

                <input type="hidden" name="role" value="staff">
                <div class="modal-actions">
                    <button type="submit" class="btn-primary">Save</button>
                    <button type="button" class="btn-delete" onclick="closeStaffModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="admin.js"></script>
</body>

</html>