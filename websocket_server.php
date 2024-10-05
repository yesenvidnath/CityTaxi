<?php
require __DIR__ . '/vendor/autoload.php'; // Adjust the path if needed

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
        // Store the new connection
        $this->clients->attach($conn);
        
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
            }
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
        foreach ($this->passengerConnections as $passengerConnection) {
            $passengerConnection->send(json_encode([
                'status' => 'confirmed',
                'message' => "Your ride has been accepted by {$driverInfo['driverName']} (Driver ID: {$driverID}).",
                'driverDetails' => [
                    'name' => $driverInfo['driverName'],
                    'location' => $driverInfo['driverLocation'],
                    'mobile' => $driverInfo['driverMobile']
                ],
                'rideDetails' => $rideDetails
            ]));
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