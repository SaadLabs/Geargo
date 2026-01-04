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
            <li class="logout" onclick="window.location.href='login.html'">
                <i class="bi bi-box-arrow-right"></i> Logout
            </li>
        </ul>
    </div>

    <div class="main">

        <section id="staff-dashboard" class="content-section">
            <h1>Staff Overview</h1>
            <div class="cards">
                <div class="card">Pending Orders<br><span id="pendingOrders">1</span></div>
                <div class="card">Low Stock Items<br><span id="lowStockCount">0</span></div>
                <div class="card">Today's Tasks<br><span id="tasksCount">8</span></div>
            </div>
        </section>


        <!-- inventory Section -->
        <section id="products" class="content-section hidden">
            <div class="section-header">
                <h1>Inventory</h1>
            </div>
            <input type="text" id="searchInput" placeholder="Search inventory..." onkeyup="searchProduct()">
            <table id="productTable">
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Current Stock</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Wireless Headphones</td>
                    <td class="stock">50</td>
                    <td class="status"><span style="color: green;">In Stock</span></td>
                    <td>
                        <button class="btn-edit"  onclick="updateStock(this)">Update</button>
                    </td>
                </tr>

            </table>
        </section>

        <section id="orders" class="content-section hidden">
            <h1>Active Orders</h1>
            <table id="ordersTable">
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Action</th>
                </tr>
                <tr>
                    <td>#1001</td>
                    <td>Ali Khan</td>
                    <td>2x Smart Watch</td>
                    <td><button class="btn-edit" onclick="markShipped(this)">Mark Shipped</button></td>
                </tr>
            </table>
        </section>

        <section id="profile" class="content-section hidden">
            <h1>My Profile</h1>
            <div class="card" style="width: 100%; max-width: 400px;">
                <p><strong>Name:</strong> Usman</p>
                <p><strong>Role:</strong> Inventory Manager</p>
                <p><strong>Email:</strong> usman@geargo.com</p>
                <br>
                <button class="btn-primary" onclick="openPasswordModal()">Change Password</button>
            </div>
        </section>

    </div>

    <div class="modal" id="passwordModal">
        <div class="modal-content">
            <h2>Change Password</h2>

            <input type="password" id="currentPassword" placeholder="Current Password">
            <input type="password" id="newPassword" placeholder="New Password">
            <input type="password" id="confirmPassword" placeholder="Confirm New Password">

            <div class="modal-actions">
                <button class="btn-primary" onclick="changePassword()">Update</button>
                <button class="btn-delete" onclick="closePasswordModal()">Cancel</button>
            </div>
        </div>
    </div>
    <script src="staff.js"></script>
</body>

</html>