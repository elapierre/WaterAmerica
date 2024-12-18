<?php
function validate_address($address){

    $apiKey = "AIzaSyCcB5zmHwefOE3F0mOeo_kAs4OOV3bUa1k";
    $apiUrl = "https://addressvalidation.googleapis.com/v1:validateAddress?key=$apiKey";

    $data = [
        "address" => [
            "addressLines" => [
                $address['line1'], // Street address
                $address['line2'], // City, State, ZIP
            ],
            "regionCode" => $address['region_code'], // The country/region code
        ],
        "enableUspsCass" => true, // Enable USPS CASS compatible mode
    ];

    $headers = [
        "Content-Type: application/json"
    ];

    // Initialize cURL session
    $ch = curl_init($apiUrl);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute cURL request and capture the response
    $response = curl_exec($ch);

    // Close cURL session
    curl_close($ch);

    // Handle error in response
    if ($response === false) {
        return ['error' => 'API request failed'];
    }

    // Decode the response from JSON to an array
    $result = json_decode($response, true);

    // Check if USPS formatted address is available
    if (isset($result['result']['uspsData']['standardizedAddress']['firstAddressLine'])) {
        // Return the USPS formatted address as an associative array
        $address = $result['result']['uspsData']['standardizedAddress']['firstAddressLine'];
        $city = $result['result']['uspsData']['standardizedAddress']['city'];
        $state = $result['result']['uspsData']['standardizedAddress']['state'];
        $zipCode = $result['result']['uspsData']['standardizedAddress']['zipCode'];
        return [
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'zipCode' => $zipCode
        ];
    } else {
        return ['error' => 'USPS formatted address not available'];
    }
}
