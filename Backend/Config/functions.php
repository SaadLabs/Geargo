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

//get product using id
function getProductBy_id($conn, $id){
    $query = "SELECT * FROM `product` WHERE `product_id` = $id";
    $result = mysqli_query($conn, $query);

        return mysqli_fetch_assoc($result);
}
?>