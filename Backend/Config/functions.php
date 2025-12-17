<?php
function dbConnect(){
    if ($conn = mysqli_connect("localhost", "root", "", "geargo")){
        return $conn;
    }
    else{
        die("Connection failed: " . $conn->connect_error);
    }
}

dbConnect();

function homePage_products($num, $conn){
    $querry = "SELECT * FROM `product` order by rand() limit $num";
    $result = mysqli_query($conn, $querry);

    $products = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }

    return $products;
}
?>