<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

function safe_get($array, $key, $default = null) {
    return isset($array[$key]) ? $array[$key] : $default;
}

try {
    $rawData = file_get_contents('php://input');
    error_log("Raw POST data: " . $rawData);
    $data = json_decode($rawData, true);

    // Handle payment status updates
    $paymentStatus = safe_get($data, 'paymentStatus');
    $orderId = safe_get($data, 'orderId');

    if ($paymentStatus && $orderId !== null) {
        // Update your database with the payment status
        // Example:
        // $stmt = $db->prepare("UPDATE payments SET status = ? WHERE order_id = ?");
        // $stmt->execute([$paymentStatus, $orderId]);

        error_log("Payment status updated: " . $paymentStatus . " for Order ID: " . $orderId);

        echo json_encode(['success' => true, 'message' => 'Payment status updated']);
        exit;
    }

    // Proceed with generating payment data
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$paymentStatus) {
        $amount = safe_get($data, 'amount', 0);
        $merchant_id = "1228396";
        $order_id = uniqid();
        $currency = "LKR";
        $merchant_secret = "MTExMDc1NjMyMjM1NDcwMDQ2NjMzNjMyMTk2MTI5Mzc0NzMwMjQ3MQ==";

        // Generating the hash value for payment security
        $hash = strtoupper(
            md5(
                $merchant_id . 
                $order_id . 
                number_format($amount, 2, '.', '') . 
                $currency . 
                strtoupper(md5($merchant_secret))
            )
        );

        // Prepare the array with payment details, including rideID and driverID
        $paymentArray = [
            "first_name" => safe_get($data, 'firstName'),
            "last_name" => safe_get($data, 'lastName'),
            "email" => safe_get($data, 'email'),
            "phone" => safe_get($data, 'phone'),
            "address" => safe_get($data, 'address'),
            "city" => safe_get($data, 'city', 'Colombo'),
            "country" => "Sri Lanka",
            "amount" => $amount,
            "merchant_id" => $merchant_id,
            "order_id" => $order_id,
            "currency" => $currency,
            "hash" => $hash,
            "rideID" => safe_get($data, 'rideID'),  // Include rideID in the response
            "driverID" => safe_get($data, 'driverID') // Include driverID in the response
        ];

        // Return the payment data to be used by JavaScript
        echo json_encode([
            'success' => true,
            'paymentData' => $paymentArray
        ]);
    } else {
        throw new Exception('Invalid request method. POST expected.');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
