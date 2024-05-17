<?php
// Replace 'YOUR_GOOGLE_API_KEY' with your actual Google Maps API key
$apiKey = 'AIzaSyCAP41rsfjKCKORsVRuSM_4ff6f7YGV7kQ';

if (isset($_POST["origin"]) && isset($_POST["destination"]) && isset($_POST["deliveryType"])) {
    $origin = urlencode($_POST["origin"]);
    $destination = urlencode($_POST["destination"]);
    $deliveryType = $_POST["deliveryType"];

    $courier['distance'] = calculateDistance($origin, $destination, $apiKey);
    if ($courier['distance'] !== false) {
        // Calculate shipping price based on distance and delivery type
            // Determine base rate and additional rate per kilometer based on delivery type
    switch ($deliveryType) {
        case 'SAME DAY (1PM to 4PM)':
            $baseRate = 10.00;
            $additionalRatePerKm = 0.55;
            break;
        case 'SAME DAY (BEFORE 5PM)':
            $baseRate = 10.00;
            $additionalRatePerKm = 0.50;
            break;
        case 'RUSH (4 HOURS)':
            $baseRate = 10.00;
            $additionalRatePerKm = 0.55;
            break;
        case 'RUSH (3 HOURS)':
            $baseRate = 15.00;
            $additionalRatePerKm = 0.70;
            break;
        case 'RUSH (2 HOURS)':
            $baseRate = 20.00;
            $additionalRatePerKm = 0.75;
            break;
        case 'URGENT (90 MINUTES)':
            $baseRate = 25.00;
            $additionalRatePerKm = 0.75;
            break;
        case 'NEXT DAY (BEFORE 5PM)':
            $baseRate = 5.00;
            $additionalRatePerKm = 0.55;
            break;
        case 'NEXT DAY (BEFORE 2PM)':
            $baseRate = 7.00;
            $additionalRatePerKm = 0.55;
            break;
        case 'NEXT DAY (BEFORE 11:30AM)':
            $baseRate = 10.00;
            $additionalRatePerKm = 0.75;
            break;
        case 'NEXT DAY (BEFORE 10:30AM)':
            $baseRate = 15.00;
            $additionalRatePerKm = 0.75;
            break;
        default:
            return "Invalid delivery type";
    }
        $courier['baseRate'] = $baseRate;
        $courier['shipmentfee'] = calculateShippingPrice($courier['distance'], $deliveryType);
        // print_r($courier); die();
        echo json_encode($courier);
    } else {
        echo "<p>Error calculating distance.</p>";
    }
} else {
    echo "<p>Please fill in all fields.</p>";
}

// Function to calculate distance between two coordinates
function calculateDistance($origin, $destination, $apiKey) {
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$origin&destinations=$destination&key=$apiKey";
    $response = file_get_contents($url);
    // print_r($response); die();
    $data = json_decode($response, true);

    // Check if API request was successful
    if ($data['status'] == 'OK') {
        // Extract distance in meters
        $distance = $data['rows'][0]['elements'][0]['distance']['value'];
        // Convert meters to kilometers
        return $distance / 1000;
    } else {
        // Handle API error
        return false;
    }
}

// Function to calculate shipping price based on distance and delivery type
function calculateShippingPrice($distance, $deliveryType) {
    $baseRate = 0;
    $additionalRatePerKm = 0;

    // Determine base rate and additional rate per kilometer based on delivery type
    switch ($deliveryType) {
        case 'SAME DAY (1PM to 4PM)':
            $baseRate = 10.00;
            $additionalRatePerKm = 0.55;
            break;
        case 'SAME DAY (BEFORE 5PM)':
            $baseRate = 10.00;
            $additionalRatePerKm = 0.50;
            break;
        case 'RUSH (4 HOURS)':
            $baseRate = 10.00;
            $additionalRatePerKm = 0.55;
            break;
        case 'RUSH (3 HOURS)':
            $baseRate = 15.00;
            $additionalRatePerKm = 0.70;
            break;
        case 'RUSH (2 HOURS)':
            $baseRate = 20.00;
            $additionalRatePerKm = 0.75;
            break;
        case 'URGENT (90 MINUTES)':
            $baseRate = 25.00;
            $additionalRatePerKm = 0.75;
            break;
        case 'NEXT DAY (BEFORE 5PM)':
            $baseRate = 5.00;
            $additionalRatePerKm = 0.55;
            break;
        case 'NEXT DAY (BEFORE 2PM)':
            $baseRate = 7.00;
            $additionalRatePerKm = 0.55;
            break;
        case 'NEXT DAY (BEFORE 11:30AM)':
            $baseRate = 10.00;
            $additionalRatePerKm = 0.75;
            break;
        case 'NEXT DAY (BEFORE 10:30AM)':
            $baseRate = 15.00;
            $additionalRatePerKm = 0.75;
            break;
        default:
            return "Invalid delivery type";
    }

    // Calculate additional rate for distance beyond 10km
    $additionalDistance = max(0, ($distance - 10));
    $additionalCharge = $additionalDistance * $additionalRatePerKm;

    // Calculate total shipping price
    $totalPrice = $baseRate + $additionalCharge;
    return $totalPrice;
}
?>