<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Products</title>
</head>

<?php
// 1. Connect to the database
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "geargo";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Query to fetch all products
$sql = "SELECT * FROM product";
$result = $conn->query($sql);
?>
<body>
    <h1>All Products</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Brand</th>
                <th>Color</th>
                <th>Price</th>
                <th>Stock Quantity</th>
                <th>Active</th>
                <th>Image</th>
                <th>Category ID</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // 3. Loop through the results and display each row
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['product_id'] . "</td>";
                    echo "<td>" . $row['title'] . "</td>";
                    echo "<td>" . $row['description'] . "</td>";
                    echo "<td>" . $row['brand'] . "</td>";
                    echo "<td>" . $row['color'] . "</td>";
                    echo "<td>" . $row['price'] . "</td>";
                    echo "<td>" . $row['stock_quantity'] . "</td>";
                    echo "<td>" . $row['is_active'] . "</td>";
                    echo "<td><img src='" . $row['image'] . "' alt='" . $row['title'] . "' width = 80px></td>";
                    echo "<td>" . $row['category_id'] . "</td>";
                    echo "<td>" . $row['created_by'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='11'>No products found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
