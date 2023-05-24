<?php

// Include the database connection file
require_once 'dbConnect.php';

// Get the JSON data sent from the AJAX POST request
$data = $_POST['data'];

// Decode the JSON data into an associative array
$response = json_decode($data, true);

$userID = 1;

// Extract the required data from the response
$originAddress = $response['origin_address'];
$destinationAddress = $response['destination_address'];
$arrivalDate = $response['arrival_date'];
$sunset_local_formatted = $response['sunset_local_formatted'];
$departureDateTime_formatted = $response['departureDateTime_formatted'];
$departureTime_formatted = $response['departureTime_formatted'];
$departureDate_formatted = $response['departureDate_formatted'];
$travel_time_response_minutes = $response['travel_time_response_minutes'];

// Prepare and bind the insert statement
$sql = "INSERT INTO trips (" .
    "user_id, ".
    "origin_address, " .
    "destination_address, " .
    "arrival_date, " .
    "sunset_local_formatted, " .
    "departure_datetime_formatted, " .
    "travel_time_response_minutes" .
    ") VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $mysqli->prepare($sql);

$stmt->bind_param(
    "isssssi",
    $userID,
    $originAddress,
    $destinationAddress,
    $arrivalDate,
    $sunset_local_formatted,
    $departureDateTime_formatted,
    $travel_time_response_minutes
);

// Execute the statement
if ($stmt->execute()) {
    // Data inserted successfully
    echo "Data saved successfully!";
} else {
    // Error in executing the statement
    echo "Error: " . $stmt->error;
}

// Close the statement and database connection
$stmt->close();
$mysqli->close();
