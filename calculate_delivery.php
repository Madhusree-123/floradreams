<?php
// Enable CORS for local development
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

// Get the POST data
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['city']) || !isset($data['pincode'])) {
    echo json_encode(["error" => "City and pincode are required"]);
    exit;
}

// Flower shop location (latitude, longitude)
$shopLocation = "11.0168,76.9558";

// Customer location
$city = urlencode($data['city']);
$pincode = urlencode($data['pincode']);
$customerLocation = "{$city} {$pincode}";

// Google Maps API key
$apiKey = "AIzaSyDe46hnAt2Dbhit-kMKE7vmIJvW__JX1h0";

// Distance Matrix API URL
$url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins={$shopLocation}&destinations={$customerLocation}&key={$apiKey}";

// Fetch distance data from Google Maps API
$response = file_get_contents($url);
$responseData = json_decode($response, true);

if ($responseData['status'] !== "OK") {
    echo json_encode(["error" => "Failed to fetch distance data"]);
    exit;
}

$distanceText = $responseData['rows'][0]['elements'][0]['distance']['text'];
$distanceValue = $responseData['rows'][0]['elements'][0]['distance']['value'] / 1000; // Convert to km

// Calculate delivery charge (e.g., ₹10 per km)
$deliveryCharge = round($distanceValue * 10);

echo json_encode([
    "distance" => $distanceText,
    "deliveryCharge" => $deliveryCharge
]);
