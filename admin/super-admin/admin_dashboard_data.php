<?php
require_once '../../Backend/config/functions.php';
$conn = dbConnect();

header('Content-Type: application/json');

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

//Define Time Conditions
$dateCondition = ""; 

switch ($filter) {
    case 'today':
        $dateCondition = " AND order_date >= NOW() - INTERVAL 1 DAY";
        break;
    case 'week':
        $dateCondition = " AND order_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        break;
    case 'month':
        $dateCondition = " AND order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        break;
    case 'year':
        $dateCondition = " AND order_date >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
        break;
    case 'all':
    default:
        $dateCondition = ""; // No restrictions
        break;
}

// Orders Count (Filtered by time)
$orderSql = "SELECT COUNT(*) as count FROM `order` WHERE 1=1 $dateCondition";
$orderResult = mysqli_query($conn, $orderSql);
$orders = mysqli_fetch_assoc($orderResult)['count'];

//Sales Total
$salesSql = "SELECT COALESCE(SUM(total_amount), 0) as total FROM `order` WHERE 1=1 $dateCondition";
$salesResult = mysqli_query($conn, $salesSql);
$sales = mysqli_fetch_assoc($salesResult)['total'];

//Products
$prodSql = "SELECT COUNT(*) as count FROM product";
$prodResult = mysqli_query($conn, $prodSql);
$products = mysqli_fetch_assoc($prodResult)['count'];

//Users
$userSql = "SELECT COUNT(*) as count FROM user"; 
$userResult = mysqli_query($conn, $userSql);
$users = mysqli_fetch_assoc($userResult)['count'];

//Send JSON Response
echo json_encode([
    'orders' => $orders,
    'sales' => $sales,
    'products' => $products,
    'users' => $users
]);
exit();
?>