<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 'on');

include_once 'apiCaller.php';

/**
 * Variables
 */
define("MAPS_API_KEY", "YOUR_API_KEY_GOES_HERE"); // Replace with your Microsoft Maps API key

// Get the origin and destination addresses from the $_GET array
$originAddress = $_GET['origin_address']; //'420 9th Ave, New York, NY 10001';
$destinationAddress = $_GET['destination_address']; //'1602 Avenue J, Brooklyn, NY 11230';

// Get the arrival date from the $_GET array
$arrivalDate = $_GET['arrival_date']; // date('Y-m-d'); Gets the current date in the "YYYY-MM-DD" format


/**
 * Get Sunset time
*/
/* Make a request to the Microsoft Maps API to get destination geo coordinates */
$url = 'https://dev.virtualearth.net/REST/v1/Locations?query=' . urlencode($destinationAddress) . '&key=' . MAPS_API_KEY;

$response = makeApiRequest($url);

$data = json_decode($response, true);

$destination_latitude = $data['resourceSets'][0]['resources'][0]['point']['coordinates'][0];
$destination_longitude = $data['resourceSets'][0]['resources'][0]['point']['coordinates'][1];


/* Make a request to the sunrise-sunset.org API to get the sunset time for the arrival date */
$url = "https://api.sunrise-sunset.org/json?lat=$destination_latitude&lng=$destination_longitude&date=$arrivalDate&formatted=0";
$response = makeApiRequest($url);

$sunset_data = json_decode($response, true);

// Extract the UTC sunset time from the response
$sunset_utc = new DateTime($sunset_data['results']['sunset']);

/* Make a request to the Microsoft Maps API to get timezone for the destination address */
$timezone_url = "https://dev.virtualearth.net/REST/v1/TimeZone/?query=$destination_latitude,$destination_longitude&key=". MAPS_API_KEY;

$response = makeApiRequest($timezone_url);

$timezone_data = json_decode($response, true);

// Get the local timezone from Maps API response
$local_destination_timezone = new DateTimeZone($timezone_data['resourceSets'][0]['resources'][0]['timeZoneAtLocation'][0]['timeZone'][0]['ianaTimeZoneId']);

// Convert UTC sunset time to local time using the timezone from the Maps API
$sunset_local_time = new DateTime($sunset_utc->format('Y-m-d H:i:s'), new DateTimeZone("UTC"));
$sunset_local_time->setTimezone($local_destination_timezone);



/**
 * * Get Origin to Destination travel info
 */
// Desired arrival time (in UTC)
$arrivalTime = $sunset_utc->format('Y-m-d\TH:i:s\Z'); //'2023-05-11T18:00:00Z'; //'2023-05-04T18:00:00Z';

/* Make maps api Travel Info request */
$url = 'https://dev.virtualearth.net/REST/v1/Routes/Driving?' . 
'wp.0=' . urlencode($originAddress) . 
'&wp.1=' . urlencode($destinationAddress) . 
'&travelMode=Driving' . 
'&timeType=Arrival' . 
'&dateTime=' . urlencode($arrivalTime) . 
'&routeAttributes=excludeItinerary'. 
'&optimize=timeWithTraffic' . 
'&distanceUnit=Mile'. 
'&key=' . MAPS_API_KEY;

$response = makeApiRequest($url);
 
$data = json_decode($response, true);
 
//get travel info
$travel_time_response = $data['resourceSets'][0]['resources'][0]['travelDurationTraffic'];
//$arrival_time = $data['resourceSets'][0]['resources'][0]['routeLegs'][0]['endTime'];
$departure_time_response = $data['resourceSets'][0]['resources'][0]['routeLegs'][0]['startTime'];
 
//get origin lat and long geo coords
$origin_latitude = $data['resourceSets'][0]['resources'][0]['routeLegs'][0]['startLocation']['geocodePoints'][0]['coordinates'][0];
$Origin_longitude = $data['resourceSets'][0]['resources'][0]['routeLegs'][0]['startLocation']['geocodePoints'][0]['coordinates'][1];
 

//Convert travel time to minutes.
$travel_time_response_minutes = round($travel_time_response / 60);//$travel_time_response / 60;
 
//Convert Date and Time from result.
$departure_timestamp = intval(substr($departure_time_response, 6, -7)); // Extract the milliseconds value and convert it to an integer
$departure_dateTime = date_create_from_format('U.u', sprintf('%.3F', $departure_timestamp / 1000)); // Convert milliseconds to seconds and create DateTime object


/* Make a request to the Microsoft Maps API to get time zone for the Origin address */
$url = "https://dev.virtualearth.net/REST/v1/TimeZone/?query=$origin_latitude,$Origin_longitude&key=". MAPS_API_KEY;

$response = makeApiRequest($url);
 
$timezone_data = json_decode($response, true);
 
// Get the local timezone from Maps API response
$local_timezone = new DateTimeZone($timezone_data['resourceSets'][0]['resources'][0]['timeZoneAtLocation'][0]['timeZone'][0]['ianaTimeZoneId']);
 
// Convert UTC departure time to local time using the timezone from the Maps API
//$origin_local_time = new DateTime($departure_dateTime->format('Y-m-d H:i:s'), new DateTimeZone("UTC"));
$departure_dateTime->setTimezone($local_timezone);

 /**
  * Format and Respond
  */
/* Formatting */
// Format the local sunset time as a string in the format "7:30 PM"
$sunset_local_formatted = $sunset_local_time->format('g:i A');

// Format the datetime as desired (e.g. YYYY-MM-DD HH:MM:SS)
$departureDateTime_formatted = $departure_dateTime->format('Y-m-d H:i:s'); // Format the date as desired (e.g. YYYY-MM-DD HH:MM:SS)

// Format the departure time as a string in the format "7:30 PM"
$departureTime_formatted = $departure_dateTime->format('g:i A');

// Format the departure date as a string in the format "2023-05-15"
$departureDate_formatted = $departure_dateTime->format('Y-m-d');

/* Set up Response and Respond */
// Create an associative array with the response data
$responseData = array(
  'origin_address' => $originAddress,
  'destination_address' => $destinationAddress,
  'arrival_date' => $arrivalDate,
  'sunset_local_formatted' => $sunset_local_formatted,
  'departureDateTime_formatted' => $departureDateTime_formatted,
  'departureTime_formatted' => $departureTime_formatted,
  'departureDate_formatted' => $departureDate_formatted,
  'travel_time_response_minutes' => $travel_time_response_minutes
);

// Encode the response data as a JSON string
$dataResponse = json_encode($responseData);

// Send the JSON-encoded response back to the AJAX request
echo $dataResponse;
