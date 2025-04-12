<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set headers for JSON response and CORS
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate received data
if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON data"]);
    exit;
}

$customer_name = $data['customer_name'] ?? '';
$product_id = $data['product_id'] ?? '';
$address = $data['address'] ?? '';
$city = $data['city'] ?? '';
$pincode = $data['pincode'] ?? '';
$phone = $data['phone'] ?? '';
$delivery_charge = $data['delivery_charge'] ?? 0;
$total_price = $data['total_price'] ?? 0;

// Simple validation
if (empty($customer_name) || empty($product_id) || empty($address) || empty($city) || empty($pincode) || empty($phone)) {
    echo json_encode(["status" => "error", "message" => "Please fill all required fields."]);
    exit;
}

// TODO: Database connection (replace with your DB credentials)
$host = "localhost";
$dbname = "flower_shop";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

// Check DB connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Insert order into database
$sql = "INSERT INTO orders (customer_name, product_id, address, city, pincode, phone, delivery_charge, total_price)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ssssssdd", $customer_name, $product_id, $address, $city, $pincode, $phone, $delivery_charge, $total_price);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Order placed successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to place order: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "SQL error: " . $conn->error]);
}

$conn->close();
?>
