<?php

/**
 * Function to make an API request using curl
 * 
 * @param string $url The URL to make the API request to
 * @return string The response from the API
 */
function makeApiRequest($url) {
    // Initialize curl handle
    $curl = curl_init();

    // Set the URL to make the request to
    curl_setopt($curl, CURLOPT_URL, $url);

    // Set curl options
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // Disable SSL host verification
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL peer verification

    // Execute the curl request and store the response
    $response = curl_exec($curl);

    // Close the curl handle to free up any resources used by it
    curl_close($curl);

    // Return the API response
    return $response;
}
