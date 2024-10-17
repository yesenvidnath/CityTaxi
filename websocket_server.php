<?php
require __DIR__ . '/vendor/autoload.php'; // Adjust the path if needed

// Include the Rides class to access the methods __DIR__ .
require __DIR__ . '/Functions/Common/Rides.php';
require __DIR__ . '/Functions/Common/Texts.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class WebSocketServer implements MessageComponentInterface {
    protected $clients;
    protected $driverConnections; // To store Driver ID to connection mapping
    protected $passengerConnections; // To store Passenger connections
    private $pendingRides = []; // To store pending ride requests

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->driverConnections = []; // Initialize the array
        $this->passengerConnections = []; // Initialize the array for passengers
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        
        // Parse the query parameters to get the Passenger or Driver ID from the connection request
        parse_str($conn->httpRequest->getUri()->getQuery(), $queryParams);

        $passengerID = $queryParams['passengerID'] ?? null;
        $driverID = $queryParams['driverID'] ?? null;
        
        if ($passengerID) {
            // Store the passenger connection with the Passenger ID as the key
            $this->passengerConnections[$passengerID] = $conn;
            echo "New passenger connection: {$conn->resourceId} for Passenger ID: {$passengerID}\n";
        } elseif ($driverID) {
            // Store the driver connection with the Driver ID as the key
            $this->driverConnections[$driverID] = $conn;
            echo "New driver connection: {$conn->resourceId} for Driver ID: {$driverID}\n";
        } else {
            echo "No Passenger or Driver ID found in the connection request.\n";
        }


        // For the rides 
        // Get Driver ID from request
        $driverID = $this->getDriverIDFromRequest($conn);
        if ($driverID) {
            $this->driverConnections[$driverID] = $conn; // Map Driver ID to connection
            echo "New connection: {$conn->resourceId} for Driver ID: {$driverID}\n";
        } else {
            // Handle passenger connections
            $this->passengerConnections[$conn->resourceId] = $conn; // Map passenger connection
            echo "New passenger connection: {$conn->resourceId}\n";
        }
    }
    
    

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
    
        if (isset($data['action'])) {
            switch ($data['action']) {

                case 'sendMessage':
                    $this->handleRideBooking($data);
                    break;
    
                case 'acceptRide':
                    $driverInfo = [
                        'driverName' => $data['driverName'],
                        'driverLocation' => $data['driverLocation'],
                        'driverMobile' => $data['driverMobile']
                    ];
                    $this->handleDriverResponse($data['rideDetails']['rideID'], $data['driverID'], 'accepted', $driverInfo);
                    break;
    
                case 'rejectRide':
                    $this->handleDriverResponse($data['rideDetails']['rideID'], $data['driverID'], 'rejected');
                    break;


                case 'finishRide':
                    if (isset($data['passengerID']) && isset($this->passengerConnections[$data['passengerID']])) {
                        $this->passengerConnections[$data['passengerID']]->send(json_encode([
                            'status' => 'rideEndRequest',
                            'rideID' => $data['rideID'],
                            'driverID' => $data['driverID'],
                            'totalAmount' => $data['totalAmount']
                        ]));
                        echo "Ride end request sent to Passenger ID: {$data['passengerID']}\n";
                    } else {
                        echo "Error: Passenger connection not found for Passenger ID: {$data['passengerID']}\n";
                    }
                    break;
    
                case 'passengerPaymentMethod':
                    if (isset($data['driverID']) && isset($data['passengerID']) && isset($this->driverConnections[$data['driverID']])) {
                        $this->driverConnections[$data['driverID']]->send(json_encode([
                            'action' => 'passengerPaymentMethod',
                            'paymentMethod' => $data['paymentMethod'],
                            'rideID' => $data['rideID'],
                            'passengerID' => $data['passengerID'],
                            'totalAmount' => $data['totalAmount']
                        ]));
                        echo "Passenger payment method sent to Driver ID: {$data['driverID']}\n";
                    } else {
                        echo "Error: Driver connection not found for Driver ID: {$data['driverID']}\n";
                    }
                    break;

                case 'driverCashConfirmation':
                    if (isset($data['passengerID']) && isset($this->passengerConnections[$data['passengerID']])) {
                        $this->passengerConnections[$data['passengerID']]->send(json_encode([
                            'status' => 'driverCashConfirmation',
                            'confirmed' => $data['confirmed'],
                            'rideID' => $data['rideID'],
                            'totalAmount' => $data['totalAmount']
                        ]));
                        echo "Driver cash confirmation sent to Passenger ID: {$data['passengerID']}\n";
                    } else {
                        echo "Error: Passenger connection not found for Passenger ID: {$data['passengerID']}\n";
                    }
                    break;
                    
                case 'passengerRecheckedCash':
                    if (isset($data['driverID']) && isset($this->driverConnections[$data['driverID']])) {
                        // Send the recheck message to the driver
                        $this->driverConnections[$data['driverID']]->send(json_encode([
                            'action' => 'passengerRecheckedCash',
                            'rideID' => $data['rideID'],
                            'passengerID' => $data['passengerID'],
                            //'totalAmount' => $data['totalAmount']
                        ]));
                        echo "Passenger rechecked cash message sent to Driver ID: {$data['driverID']}\n";
                    } else {
                        echo "Error: Driver connection not found for Driver ID: " . ($data['driverID'] ?? 'undefined') . "\n";
                    }
                    break;
                    
                    
                case 'passengerSwitchToOnline':
                    if (isset($data['driverID']) && isset($data['passengerID']) && isset($this->driverConnections[$data['driverID']])) {
                        $this->driverConnections[$data['driverID']]->send(json_encode([
                            'action' => 'passengerSwitchToOnline',
                            'rideID' => $data['rideID'],
                            'passengerID' => $data['passengerID'],
                            'totalAmount' => $data['totalAmount']
                        ]));
                        echo "Passenger switched to online payment, message sent to Driver ID: {$data['driverID']}\n";
                    } else {
                        echo "Error: Driver or Passenger connection not found in passengerSwitchToOnline action.\n";
                    }
                    break;

                case 'passengerOnlinePaymentSuccess':
                    // Ensure driverID is provided
                    if (!isset($data['rideID'])) {
                        echo "Error: rideID not provided in passengerOnlinePaymentSuccess action.\n";
                        break; // Stop further processing if rideID is missing
                    }

                    // Ensure driverID and passengerID are present and driver connection exists
                    if (isset($this->driverConnections[$data['driverID']])) {
                        // Validate if rideID is provided
                        if (!isset($data['rideID'])) {
                            echo "Error: rideID not provided in passengerOnlinePaymentSuccess action.\n";
                            break; // Stop further processing if rideID is missing
                        }
                
                        // Debugging log to track the incoming data
                        echo "Processing passengerOnlinePaymentSuccess for Driver ID: {$data['driverID']}, Ride ID: {$data['rideID']}\n";
                
                        // Get driver details
                        $ride = new Ride();
                        $driverInfo = $ride->getDriverDetailsByDriverID($data['driverID']);
                
                        // Ensure driver details are retrieved
                        if (!$driverInfo) {
                            echo "Error: Could not retrieve driver details for Driver ID: {$data['driverID']}\n";
                            break;
                        }
                
                        $driverMobile = $driverInfo['mobile_number'];
                        $driverName = $driverInfo['First_name'] . ' ' . $driverInfo['Last_name'];
                
                        // Get passenger details
                        $passengerInfo = $ride->getPassengerDetailsByID($data['passengerID']);
                
                        // Ensure passenger details are retrieved
                        if (!$passengerInfo) {
                            echo "Error: Could not retrieve passenger details for Passenger ID: {$data['passengerID']}\n";
                            break;
                        }
                
                        $passengerMobile = $passengerInfo['mobile_number'] ?? 'Unknown';
                        $passengerFirstName = $passengerInfo['First_name'] ?? 'Unknown';
                        $passengerLastName = $passengerInfo['Last_name'] ?? '';
                        $passengerName = trim("$passengerFirstName $passengerLastName");
                
                        // Get ride locations
                        $rideLocations = $ride->getRideLocationsByID($data['rideID']);
                
                        // Ensure ride locations are retrieved
                        if (!$rideLocations) {
                            echo "Error: Could not retrieve ride locations for Ride ID: {$data['rideID']}\n";
                            break;
                        }
                
                        $startLocation = $rideLocations['Start_Location'] ?? 'Unknown';
                        $endLocation = $rideLocations['End_Location'] ?? 'Unknown';
                        $totalAmount = $data['totalAmount'] ?? 0;
                
                        // Notify the driver about the payment success via WebSocket
                        if (isset($this->driverConnections[$data['driverID']])) {
                            $this->driverConnections[$data['driverID']]->send(json_encode([
                                'action' => 'passengerOnlinePaymentSuccess',
                                'rideID' => $data['rideID'],  // Ensure rideID is present here
                                'driverID' => $data['driverID'],
                                'totalAmount' => $totalAmount
                            ]));
                            echo "Message sent to Driver ID: {$data['driverID']} about online payment success.\n";
                        } else {
                            echo "Error: Driver connection not found for Driver ID: {$data['driverID']}\n";
                        }
                
                        // Send SMS to the driver about payment success
                        $texts = new Texts();
                        $driverSmsMessage = "The passenger {$passengerName} has completed the payment for Ride ID: {$data['rideID']}.\n"
                            . "Start Location: {$startLocation}\n"
                            . "End Location: {$endLocation}\n"
                            . "Total Amount: LKR {$totalAmount}.";
                        $texts->sendPaymentSuccessSms($driverMobile, $data['rideID'], $totalAmount, $startLocation, $endLocation, 'driver', $passengerName, $driverName);
                
                        // Send SMS to the passenger if the mobile number is valid
                        if (!empty($passengerMobile) && $passengerMobile !== 'Unknown') {
                            $passengerSmsMessage = "Thank you, {$passengerName}, for completing the payment for your ride.\n"
                                . "Driver: {$driverName}\n"
                                . "Start Location: {$startLocation}\n"
                                . "End Location: {$endLocation}\n"
                                . "Total Amount: LKR {$totalAmount}.";
                            $texts->sendPaymentSuccessSms($passengerMobile, $data['rideID'], $totalAmount, $startLocation, $endLocation, 'passenger', $passengerName, $driverName);
                        } else {
                            echo "Error: Passenger mobile number is missing or invalid for Passenger ID: {$data['passengerID']}\n";
                        }
                
                        echo "Payment success messages sent to Driver ID: {$data['driverID']} and Passenger ID: {$data['passengerID']}\n";
                
                    } else {
                        // Additional logging to identify which value is missing
                        if (!isset($data['driverID'])) {
                            echo "Error: driverID not provided in passengerOnlinePaymentSuccess action.\n";
                        } elseif (!isset($this->driverConnections[$data['driverID']])) {
                            echo "Error: Driver connection not found for Driver ID: {$data['driverID']}\n";
                        }
                    }
                    break;        
                    
                case 'cashPaymentSuccess':
                    // Ensure rideID is provided
                    if (!isset($data['rideID'])) {
                        echo "Error: rideID not provided in cashPaymentSuccess action.\n";
                        break; // Stop further processing if rideID is missing
                    }
                
                    // Get driver details using the rideID
                    $ride = new Ride();
                    $driverInfo = $ride->getDriverDetailsByRideID($data['rideID']);
                
                    // Ensure driver details are retrieved
                    if (!$driverInfo) {
                        echo "Error: Could not retrieve driver details for Ride ID: {$data['rideID']}\n";
                        break;
                    }
                
                    $driverID = $driverInfo['Driver_ID'];
                    $driverMobile = $driverInfo['mobile_number'];
                    $driverName = $driverInfo['First_name'] . ' ' . $driverInfo['Last_name'];
                
                    // Get passenger details
                    $passengerInfo = $ride->getPassengerDetailsByID($data['passengerID']);
                
                    // Ensure passenger details are retrieved
                    if (!$passengerInfo) {
                        echo "Error: Could not retrieve passenger details for Passenger ID: {$data['passengerID']}\n";
                        break;
                    }
                
                    $passengerMobile = $passengerInfo['mobile_number'] ?? 'Unknown';
                    $passengerFirstName = $passengerInfo['First_name'] ?? 'Unknown';
                    $passengerLastName = $passengerInfo['Last_name'] ?? '';
                    $passengerName = trim("$passengerFirstName $passengerLastName");
                
                    // Get ride locations
                    $rideLocations = $ride->getRideLocationsByID($data['rideID']);
                
                    // Ensure ride locations are retrieved
                    if (!$rideLocations) {
                        echo "Error: Could not retrieve ride locations for Ride ID: {$data['rideID']}\n";
                        break;
                    }
                
                    $startLocation = $rideLocations['Start_Location'] ?? 'Unknown';
                    $endLocation = $rideLocations['End_Location'] ?? 'Unknown';
                    $totalAmount = $data['totalAmount'] ?? 0;
                
                    // Notify the passenger about the cash payment success via WebSocket
                    if (isset($this->passengerConnections[$data['passengerID']])) {
                        $this->passengerConnections[$data['passengerID']]->send(json_encode([
                            'status' => 'cashPaymentSuccess',
                            'rideID' => $data['rideID'],
                            'totalAmount' => $totalAmount
                        ]));
                        echo "Cash payment success message sent to Passenger ID: {$data['passengerID']}\n";
                    } else {
                        echo "Error: Passenger connection not found for Passenger ID: {$data['passengerID']}\n";
                    }
                
                    // Send SMS to the driver about cash payment success
                    $texts = new Texts();
                    $driverSmsMessage = "The passenger {$passengerName} has completed the cash payment for Ride ID: {$data['rideID']}.\n"
                        . "Start Location: {$startLocation}\n"
                        . "End Location: {$endLocation}\n"
                        . "Total Amount: LKR {$totalAmount}.";
                    $texts->sendPaymentSuccessSms($driverMobile, $data['rideID'], $totalAmount, $startLocation, $endLocation, 'driver', $passengerName, $driverName);
                
                    // Send SMS to the passenger if the mobile number is valid
                    if (!empty($passengerMobile) && $passengerMobile !== 'Unknown') {
                        $passengerSmsMessage = "Thank you, {$passengerName}, for completing the cash payment for your ride.\n"
                            . "Driver: {$driverName}\n"
                            . "Start Location: {$startLocation}\n"
                            . "End Location: {$endLocation}\n"
                            . "Total Amount: LKR {$totalAmount}.";
                        $texts->sendPaymentSuccessSms($passengerMobile, $data['rideID'], $totalAmount, $startLocation, $endLocation, 'passenger', $passengerName, $driverName);
                    } else {
                        echo "Error: Passenger mobile number is missing or invalid for Passenger ID: {$data['passengerID']}\n";
                    }
                
                    echo "Cash payment success messages sent to Driver ID: {$driverID} and Passenger ID: {$data['passengerID']}\n";
                    break;
                        
            }
        } else {
            echo "Error: No action defined in the received message.\n";
        }
    }
    
    
    public function onClose(ConnectionInterface $conn) {

        // Remove the connection when it closes
        foreach ($this->driverConnections as $driverID => $client) {
            if ($client === $conn) {
                unset($this->driverConnections[$driverID]);
                break;
            }
        }

        // Remove passenger connection
        if (isset($this->passengerConnections[$conn->resourceId])) {
            unset($this->passengerConnections[$conn->resourceId]);
        }

        // Search for and remove the passenger connection
        foreach ($this->passengerConnections as $passengerID => $passengerConn) {
            if ($passengerConn === $conn) {
                unset($this->passengerConnections[$passengerID]);
                echo "Passenger connection with ID: {$passengerID} has disconnected.\n";
                break;
            }
        }
    
        // Search for and remove the driver connection
        foreach ($this->driverConnections as $driverID => $driverConn) {
            if ($driverConn === $conn) {
                unset($this->driverConnections[$driverID]);
                echo "Driver connection with ID: {$driverID} has disconnected.\n";
                break;
            }
        }
    
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }
    
    

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    private function getDriverIDFromRequest($conn) {
        // Get the request URI
        $requestUri = $conn->httpRequest->getUri();
        
        // Parse the URI to get query parameters
        $queryParams = [];
        parse_str($requestUri->getQuery(), $queryParams);
        
        // Return the Driver ID from the query parameters
        return isset($queryParams['driverID']) ? $queryParams['driverID'] : null;
    }

    private function handleRideBooking($data) {
        $driverID = $data['driverID'];
        $rideDetails = $data['rideDetails']; // Include ride details

        // Store the pending ride
        $this->pendingRides[$rideDetails['rideID']] = [
            'details' => $rideDetails,
            'responses' => [] // To store driver responses
        ];

        // Send the message to the specific driver connection
        if (isset($this->driverConnections[$driverID])) {
            $this->driverConnections[$driverID]->send(json_encode([
                'status' => 'rideOffer',
                'message' => "New ride available from {$rideDetails['startLocation']} to {$rideDetails['endLocation']}. Price: {$rideDetails['tripPrice']}. Do you accept this ride?",
                'rideDetails' => $rideDetails
            ]));
        } else {
            echo "Driver ID: {$driverID} not connected.\n";
        }
    }

    private function handleDriverResponse($rideID, $driverID, $response, $driverInfo = null) {
        $this->pendingRides[$rideID]['responses'][$driverID] = $response;
    
        if (count($this->pendingRides[$rideID]['responses']) === count($this->driverConnections)) {
            $acceptedDriver = array_search('accepted', $this->pendingRides[$rideID]['responses']);
            
            if ($acceptedDriver !== false) {
                $this->notifyPassenger($this->pendingRides[$rideID]['details'], $acceptedDriver, $driverInfo);
            } else {
                foreach ($this->passengerConnections as $passengerConnection) {
                    $passengerConnection->send(json_encode([
                        'status' => 'rejected',
                        'message' => "All drivers are busy. Please select another vehicle type."
                    ]));
                }
            }
            unset($this->pendingRides[$rideID]);
        }
    }

    private function notifyPassenger($rideDetails, $driverID, $driverInfo) {
        // Fetch the vehicle information for the driver
        $ride = new Ride();
        $vehicleInfo = $ride->getDriverVehicleById($driverID); // This now returns a single associative array
        
        // Check if vehicle information is returned
        if ($vehicleInfo) {
            // Access properties from the associative array
            $plateNumber = $vehicleInfo['Plate_number']; 
            $taxiID = $vehicleInfo['Taxi_ID']; 
            $taxiType = $vehicleInfo['Taxi_type']; 
        } else {
            // Default values in case no vehicle info is found
            $plateNumber = 'N/A';
            $taxiID = null; // Set taxiID to null if not found
            $taxiType = 'N/A';
        }
    
        // Notify the passenger that the ride has been accepted
        foreach ($this->passengerConnections as $passengerConnection) {
            $passengerConnection->send(json_encode([
                'status' => 'confirmed',
                'message' => "Your ride has been accepted by Driver: {$driverInfo['driverName']} (ID: {$driverID}) from {$rideDetails['startLocation']} to {$rideDetails['endLocation']}.\n"
                            . "Vehicle Type: {$taxiType}\n"
                            . "Plate Number: {$plateNumber}."
            ]));
    
            // Send SMS to the passenger
            $passengerUserID = $rideDetails['PassengerUserID']; // Extract passenger ID
            $mobileNumber = $rideDetails['mobileNumber']; // Extract mobile number
    
            // Create an instance of the Texts class and send SMS
            $texts = new Texts();
            $texts->sendSms($mobileNumber, $driverID, $rideDetails, $taxiType, $plateNumber, $driverInfo['driverName']); // Pass the additional parameters
    
            // Add the new ride to the rides table
            $startTime = date('Y-m-d H:i:s'); // Current time as start time
            $endTime = null; // End time is null for now
            $startDate = date('Y-m-d'); // Current date as start date
            $endDate = null; // End date is null for now
            $totalDistance = $rideDetails['totalDistance']; // Get total distance from ride details
            $amount = $rideDetails['tripPrice']; // Assuming trip price is the amount
            
            // Add the ride to the rides table
            $ride->addRide($taxiID, $driverID, $passengerUserID, $taxiType, $rideDetails['startLocation'], $rideDetails['endLocation'], $startTime, $endTime, $startDate, $endDate, $totalDistance, $amount, 'Accepted');
    
            // Update the driver's availability
            $ride->updateDriverAvailability($driverID, 0); // Set availability to 0 (unavailable)
        }

    }
    
    private function notifyPassengerRejection($driverID) {
        // Notify the passenger that the ride has been rejected
        foreach ($this->passengerConnections as $passengerConnection) {
            $passengerConnection->send(json_encode([
                'status' => 'rejected',
                'message' => "Driver ID: {$driverID} has rejected your ride request. Please try another vehicle type."
            ]));
        }
    }
}

// Create the WebSocket server and run it
$server = new Ratchet\App('localhost', 8080);
$server->route('/ws', new WebSocketServer);
$server->run();