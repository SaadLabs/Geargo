<?php
//Database connection
function dbConnect(){
    if ($conn = mysqli_connect("localhost", "root", "", "geargo")){
        return $conn;
    }
    else{
        die("Connection failed: " . $conn->connect_error);
    }
}

// Function to fetch all categories for the dropdown
function getCategories($conn) {
    // Note: Check if your table is named 'Category' or 'Categories' in your DB
    $sql = "SELECT * FROM Category ORDER BY name ASC"; 
    $result = $conn->query($sql);
    
    // fetch_all returns an array of all rows
    return $result->fetch_all(MYSQLI_ASSOC); 
}

//Hot products
function Random_products($num, $conn){
    // $query = "SELECT * FROM `product` order by rand() limit $num";
    $query = "SELECT * FROM `product` WHERE `is_active` = 1 ORDER BY rand() limit $num";
    $result = mysqli_query($conn, $query);

    $products = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }

    return $products;
}

// Function to search/filter products with pagination
function search_by_category($conn, $category_id = 'all', $sort = 'default', $page = 1, $limit = 5) {
    $offset = ($page - 1) * $limit;
    
    // 1. Build Base SQL
    $sql = "SELECT * FROM product WHERE is_active = 1";
    
    // We need variables to track types and values for binding
    $types = "";
    $params = [];

    // 2. Apply Category Filter
    // Only add the placeholder (?) if we actually have a category
    if ($category_id !== 'all' && $category_id !== null) {
        $sql .= " AND category_id = ?";
        $types .= "i";            // 'i' for integer
        $params[] = $category_id; // Add the value to our list
    }

    // 3. Apply Sorting
    // (No parameters needed here, just string appending)
    switch ($sort) {
        case 'price_low_high':
            $sql .= " ORDER BY price ASC";
            break;
        case 'price_high_low':
            $sql .= " ORDER BY price DESC";
            break;
        default:
            $sql .= " ORDER BY stock_quantity DESC"; 
            break;
    }

    // 4. Get Total Count (for pagination)
    // We modify the query to count rows instead of selecting data
    $countSql = "SELECT COUNT(*) as total " . substr($sql, strpos($sql, "FROM"));
    // Remove ORDER BY for the count query (it causes errors in some SQL versions and is slow)
    $countSql = preg_replace('/ORDER BY.*$/', '', $countSql);
    
    $stmt = $conn->prepare($countSql);

    // Only bind if we have parameters (e.g. if a category was selected)
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $totalRecords = $row['total'];
    $totalPages = ceil($totalRecords / $limit);
    $stmt->close(); // Close this statement to free up the connection

    // 5. Apply Pagination Limit to Main Query
    $sql .= " LIMIT ? OFFSET ?";
    
    // Add the LIMIT and OFFSET to our binding parameters
    $types .= "ii";       // Add two more integers to the type string
    $params[] = $limit;   // Add limit value
    $params[] = $offset;  // Add offset value

    // 6. Execute Final Query
    $stmt = $conn->prepare($sql);
    
    // Bind ALL parameters (Category if exists + Limit + Offset)
    $stmt->bind_param($types, ...$params);
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    // fetch_all(MYSQLI_ASSOC) returns an associative array (similar to PDO fetchAll)
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return [
        'products' => $products,
        'totalPages' => $totalPages,
        'currentPage' => $page,
        'totalRecords' => $totalRecords
    ];
}

//get product using id
function getProductBy_id($conn, $id){
    $query = "SELECT * FROM `product` WHERE `product_id` = $id";
    $result = mysqli_query($conn, $query);

        return mysqli_fetch_assoc($result);
}

//Cart items for suer
function getCartItems($conn, $user_id) {
    // This joins the Cart, CartItem, and Product tables to get the full details
    // Ensure table names (cart, cartitem, product) match your database exactly (check if singular or plural)
    $sql = "SELECT ci.cart_item_id, ci.quantity, p.title, p.price, p.product_id, p.image 
            FROM cart c
            JOIN cartitem ci ON c.cart_id = ci.cart_id
            JOIN product p ON ci.product_id = p.product_id
            WHERE c.user_id = ?";

    $stmt = $conn->prepare($sql);

    // 2. Error handling if query fails to prepare (e.g., wrong table names)
    if (!$stmt) {
        die("Error preparing query: " . $conn->error);
    }

    // 3. Bind parameters and execute
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    // 4. Return data
    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return []; // Return empty array if cart is empty
    }
}

//search suggestions for ajax
function getSearchSuggestions($conn, $query) {
    // Sanitize input
    $searchTerm = "%" . $query . "%";
    
    // SQL Query: Get ID, Title, Image, Price (Limit 5 results)
    $sql = "SELECT product_id, title, image, price FROM Product WHERE is_active = 1 AND title LIKE ? LIMIT 5";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return [];
    }
    
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $suggestions = [];
    while($row = $result->fetch_assoc()) {
        $suggestions[] = $row;
    }
    
    return $suggestions;
}

function search_products_by_term($conn, $searchTerm, $sort, $page, $limit) {
    $offset = ($page - 1) * $limit;
    $term = "%" . $searchTerm . "%";

    // Search in Title OR Description
    $sql = "SELECT * FROM Product WHERE is_active = 1 AND (title LIKE ? OR description LIKE ?)";

    // Add Sorting
    if ($sort == 'price_low_high') {
        $sql .= " ORDER BY price ASC";
    } 
    elseif ($sort == 'price_high_low') {
        $sql .= " ORDER BY price DESC";
    } 
    else {
        $sql .= " ORDER BY title ASC"; // Default sort
    }

    $sql .= " LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $term, $term, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);

    // Calculate Total Pages
    $totalResult = $conn->query("SELECT FOUND_ROWS() as count");
    $totalRows = $totalResult->fetch_assoc()['count'];
    $totalPages = ceil($totalRows / $limit);

    return ['products' => $products, 'totalPages' => $totalPages];
}

// Order history
function getUserOrders($conn, $user_id) {
    $orders = [];
    
    // 1. Fetch the main Orders
    $sql = "SELECT order_id, total_amount, order_date, order_status, shipping_address 
            FROM `order`    
            WHERE user_id = ? 
            ORDER BY order_date DESC";
            
    $stmt = $conn->prepare($sql);
    if (!$stmt) return [];
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $order_id = $row['order_id'];
        
        // 2. For each order, fetch the Items + Product Details
        $itemSql = "SELECT oi.quantity, oi.price_at_purchase, p.product_id, p.title, p.image 
                    FROM orderitem oi 
                    JOIN product p ON oi.product_id = p.product_id 
                    WHERE oi.order_id = ?";
                    
        $itemStmt = $conn->prepare($itemSql);
        $itemStmt->bind_param("i", $order_id);
        $itemStmt->execute();
        $itemResult = $itemStmt->get_result();
        
        // Add items array to the order row
        $row['items'] = $itemResult->fetch_all(MYSQLI_ASSOC);
        
        $orders[] = $row;
    }
    
    return $orders;
}

function getUserPaymentMethods($conn, $user_id) {
    $sql = "SELECT * FROM usercard WHERE user_id = ? AND is_active = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


?>