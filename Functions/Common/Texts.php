<?php

class Texts {
    private $apiUrl;

    public function __construct() {
        // Set the API URL for sending SMS
        $this->apiUrl = "https://smsserver-ff1bf5b1a1d5.herokuapp.com/send_sms"; 
    }

    public function sendSms($mobileNumber, $driverID, $rideDetails, $taxiType, $plateNumber, $driverName) {
        // Prepare the data payload
        $msgData = [
            "phone_number" => $mobileNumber,
            "message" => "Ride Accepted!\nDriver Name: $driverName\nDriver ID: $driverID\nFrom: {$rideDetails['startLocation']}\nTo: {$rideDetails['endLocation']}\nVehicle Type: $taxiType\nPlate Number: $plateNumber"
        ];
    
        // Make the API request to the Heroku endpoint
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($msgData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Accept: application/json"
        ]);
    
        // Execute the request
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
    
        // Handle the response
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo "SMS sent response: " . $response; // Optional: Log the response for debugging
        }
    }
}
