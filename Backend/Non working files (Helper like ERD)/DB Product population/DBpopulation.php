<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "geargo";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Path to your CSV file
$csvFile = 'all products final.csv';

// Open CSV file
if (($handle = fopen($csvFile, "r")) !== false) {

    // Read header row
    $header = fgetcsv($handle);
    $ind=1;

    // Loop through each row
    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        // Map CSV columns (adjust index if your CSV columns order is different)
        $title = $data[0];
        $description = $data[1];
        $brand = $data[2];
        $color = $data[3];
        $price = $data[4];
        $stock_quantity = $data[5];
        $is_active = $data[6];
        $image = $data[7];
        $category_id = $data[8];
        $created_by = $data[9];

        // Prepare insert query
        $sql = "INSERT INTO `product` 
        (title, description, brand, color, price, stock_quantity, is_active, image, category_id, created_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        // Bind parameters
        // s = string, d = double/decimal, i = integer
        $stmt->bind_param(
            "ssssdiisii",
            $title,         // s
            $description,   // s
            $brand,         // s
            $color,         // s
            $price,         // d
            $stock_quantity,// i
            $is_active,     // s (or i if 0/1)
            $image,         // s
            $category_id,   // i
            $created_by     // i
        );

        // Execute statement
        if ($stmt->execute()) {
            echo "Inserted product: $title  ==> $ind <br>";
            $ind++;
        } else {
            echo "Error inserting $title: " . $stmt->error . "\n";
        }

        $stmt->close();
    }

    fclose($handle);
} else {
    die("Cannot open CSV file: $csvFile");
}

$conn->close();
?>
