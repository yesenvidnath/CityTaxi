<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/vendor/autoload.php'; // Ensure you have installed the Gmail API package
include_once $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/Functions/Common/Database.php';

class Email {
    private $client;
    private $service;

    public function __construct() {
        $this->client = new Google_Client();
        $this->client->setAuthConfig($_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/env/client_secret.json');
        $this->client->addScope(Google_Service_Gmail::MAIL_GOOGLE_COM);
        $this->client->setAccessType('offline');

        $tokenPath = $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/env/token.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);
        }

        if ($this->client->isAccessTokenExpired()) {
            $refreshTokenSaved = $this->client->getRefreshToken();
            $this->client->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
        }

        $this->service = new Google_Service_Gmail($this->client);
    }

    // Method to send email
    public function sendRegistrationEmail($email, $password) {
        $subject = "Your CityTaxi Registration Details";
        $messageText = "Hello,\n\nThank you for registering with CityTaxi!\n\nHere are your login details:\nEmail: $email\nPassword: $password\n\nPlease keep these details safe.";
        
        $message = new Google_Service_Gmail_Message();
        $message->setRaw($this->base64UrlEncode("From: your-email@example.com\r\nTo: $email\r\nSubject: $subject\r\n\r\n$messageText"));
        
        try {
            $this->service->users_messages->send('me', $message);
            return true;
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $e->getMessage();
            return false;
        }
    }

    private function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
?>
