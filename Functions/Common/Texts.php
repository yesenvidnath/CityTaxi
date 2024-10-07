<?php

class Texts {
    private $authToken;
    private $senderId;

    public function __construct() {
        // Load environment variables
        $dotenv = parse_ini_file(__DIR__ . '/../../env/.env'); // Adjust path to your .env file
        $this->authToken = $dotenv['SEND_LK_AUTH'];
        $this->senderId = $dotenv['SEND_LK_SENDER_ID'];
    }

    public function sendSms($mobileNumber, $driverID, $rideDetails) {
        $message = "Ride Accepted!\nDriver ID: $driverID\nFrom: {$rideDetails['startLocation']}\nTo: {$rideDetails['endLocation']}\nPrice: {$rideDetails['tripPrice']}";

        $msgdata = array("recipient" => $mobileNumber, "sender_id" => $this->senderId, "message" => $message);

        $curl = curl_init();

        // If running locally without SSL, uncomment the following two lines
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://sms.send.lk/api/v3/sms/send",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($msgdata),
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Bearer {$this->authToken}",
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
    }
}