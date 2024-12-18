<?php
require 'vendor/autoload.php'; // Include the Twilio SDK
require 'apiKeys.php';
use Twilio\Rest\Client;

function sendMessage($phoneNumber, $billing_address, $city, $state, $zip_code, $move_date) {
    try {
        // Twilio credentials
        $account_sid = ACCOUNT_SID;
        $auth_token = AUTH_TOKEN;
        $twilio_phone_number = TWILIO_PHONE_NUMBER;

        // Create a Twilio client
        $client = new Client($account_sid, $auth_token);

        // Send the SMS
        $client->messages->create(
            $phoneNumber, // Recipient's phone number
            [
                'from' => $twilio_phone_number, // Twilio phone number
                'body' => "Your move request has been submitted. Move Date: $move_date. Address: $billing_address, $city, $state, $zip_code."
            ]
        );

        // Return confirmation
        return "<p class='success'>✅ Successfully sent the message.</p>";
    } catch (Exception $e) {
        // Return error message
        return "<p class='error'>❌ Unable to send SMS: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
