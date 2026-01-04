<?php
session_start();
require_once '../../Backend/config/functions.php';
$conn = dbConnect();

if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

$product_id = $_GET['id'];

// 1. Fetch Product Data
$sql = "SELECT * FROM product WHERE product_id = '$product_id'";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

// 2. Fetch Categories (for dropdown)
$catResult = mysqli_query($conn, "SELECT * FROM category");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product | GearGo</title>
    <link rel="stylesheet" href="admin.css"> <style>
        /* Simple centering for the edit form */
        body { background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .edit-container { background: white; padding: 30px; border-radius: 8px; width: 500px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, select, textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        .btn-save { background: #28a745; color: white; padding: 10px; border: none; cursor: pointer; width: 100%; }
        .current-img { width: 100px; height: 100px; object-fit: cover; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="edit-container">
    <h2>Edit Product</h2>
    
    <form action="edit_product_action.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
        
        <label>Title</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" required>

        <label>Category</label>
        <select name="category_id" required>
            <?php while ($cat = mysqli_fetch_assoc($catResult)): ?>
                <option value="<?php echo $cat['category_id']; ?>" 
                    <?php if($cat['category_id'] == $product['category_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Brand</label>
        <input type="text" name="brand" value="<?php echo htmlspecialchars($product['brand']); ?>" required>

        <label>Color</label>
        <input type="text" name="color" value="<?php echo htmlspecialchars($product['color']); ?>" required>

        <label>Price</label>
        <input type="number" name="price" value="<?php echo $product['price']; ?>" required>

        <label>Stock</label>
        <input type="number" name="quantity" value="<?php echo $product['stock_quantity']; ?>" required>

        <label>Description</label>
        <textarea name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>

        <label>Current Image:</label><br>
        <img src="../../<?php echo $product['image']; ?>" class="current-img"><br>
        <label>Change Image (Optional):</label>
        <input type="file" name="product_image">

        <button type="submit" class="btn-save">Update Product</button>
        <a href="admin.php" style="display:block; text-align:center; margin-top:10px; text-decoration:none; color:#333;">Cancel</a>
    </form>
</div>

</body>
</html>