<?php 
// Include the header part
include 'TemplateParts/Header/header.php'; 

// Fetch API keys from the .env file
$dotenv = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/env/.env');
$openRouteServiceApiKey = $dotenv['OpenRouteService_API_Key'];
$openCageApiKey = $dotenv['OpenCage_API_Key'];
$tomTomApiKey = $dotenv['TomTom_API_Key']; // Fetch TomTom API Key

// Include the ride functions
include_once 'Functions/Common/Ride.php';

// Fetch the taxi types and rates
$taxiTypes = getTaxiTypes();
$taxiRates = getTaxiRatesByType();

// Fetch available drivers
$availableDrivers = getAvailableDrivers();

// Map the rates to taxi types for easy access
$taxiRatesMap = [];
foreach ($taxiRates as $rate) {
    $taxiRatesMap[$rate['Taxi_type']] = $rate;
}
?>
<!-- Main content -->
<!-- Main content -->
<section class="ride-info-section">
    <div class="driver-info" id="step1">
        <h2>Buckle Up For The Ride</h2>
        <div class="form-group" id="manualLocationSection">
            <label for="startLocation">Start Location</label>
            <input type="text" class="form-control" id="startLocation" placeholder="Enter Start Location">
            <ul id="startLocationList" class="autocomplete-list"></ul>
            <button class="btn btn-info" onclick="useTomTomLocation()">Use Current Location</button>
        </div>
        <div class="form-group">
            <label for="endLocation">End Location</label>
            <input type="text" class="form-control" id="endLocation" placeholder="Enter End Location">
            <ul id="endLocationList" class="autocomplete-list"></ul> 
        </div>
        <button class="btn btn-warning" onclick="showRoute()">Show Route</button>
        <button class="btn btn-success" id="confirmRouteBtn" style="display: none;" onclick="confirmRoute()">Confirm Route</button>
    </div>

    <div class="taxi-selection-section" id="step2" style="display: none;">
        <h2>Select Your Taxi Type</h2>
        <div class="row">
            <?php
            $displayedTypes = [];
            foreach ($taxiTypes as $taxi):
                if (!in_array($taxi['Taxi_type'], $displayedTypes)):
                    $displayedTypes[] = $taxi['Taxi_type'];
                    $ratePerKM = isset($taxiRatesMap[$taxi['Taxi_type']]) ? $taxiRatesMap[$taxi['Taxi_type']]['Rate_per_Km'] : 'N/A';
            ?>
                <div class="col-lg-4">
                    <div class="taxi-card">
                        <img src="Assets/img/taxis/<?php echo $taxi['Taxi_type'] == 'Car' ? 'car.png' : $taxi['Vehicle_Img']; ?>" alt="<?php echo $taxi['Taxi_type']; ?>" class="img-fluid">
                        <h3><?php echo $taxi['Taxi_type']; ?></h3>
                        <p>Rate per KM: <?php echo $ratePerKM; ?></p>
                        <p>Total Price: <span id="price-<?php echo $taxi['Taxi_type']; ?>"></span></p>
                        <button class="btn btn-warning" onclick="selectTaxiType('<?php echo $taxi['Taxi_type']; ?>')">Select</button>
                    </div>
                </div>
            <?php
                endif;
            endforeach;
            ?>
        </div>
        <button class="btn btn-danger" id="changeRouteBtn" onclick="confirmChangeRoute()">Change Route</button>
    </div>

    <div class="selection-summary-section" id="step3" style="display: none;">
        <h2 class="text-center">Your Selection</h2>
        <p id="selectionDetails" class="text-center"></p>
        
        <h3 class="text-center">Available Drivers</h3>
        <div id="driverList" class="driver-list">
            <?php foreach ($availableDrivers as $driver): ?>
                <div class="driver-card card mb-4">
                    <div class="card-body">
                        <h4 class="card-title"><?php echo $driver['First_name'] . ' ' . $driver['Last_name']; ?></h4>
                        <p class="card-text">Location: <?php echo $driver['Current_Location']; ?></p>
                        <p class="card-text">Vehicle Type: <?php echo $driver['Taxi_type']; ?></p>
                        <button class="btn btn-success" onclick="confirmDriverSelection('<?php echo $driver['Driver_ID']; ?>')">Select Driver</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <button class="btn btn-primary" onclick="confirmBooking()">Confirm Booking</button>
        </div>
    </div>


    <div class="map-info">
        <div id="map" style="width: 800px; height: 600px;"></div>
        <div id="details">
            <h4>Route Details</h4>
            <p id="routeDetails"></p>
        </div>
    </div>
</section>

<!-- Display SweetAlert alerts if status is passed -->
<?php 
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
        $message = htmlspecialchars($_GET['message']);
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showAlert('$status', '$message');
            });
        </script>
        ";
    }
?>

<script>
    const openRouteServiceApiKey = '<?php echo $openRouteServiceApiKey; ?>';
    const openCageApiKey = '<?php echo $openCageApiKey; ?>';
    const tomTomApiKey = '<?php echo $tomTomApiKey; ?>';
    var totalDistance = 0; // Global variable to store the total distance

    // Function to update the taxi prices based on the total distance
    function updateTaxiPrices() {
        <?php foreach ($taxiRates as $rate): ?>
            var ratePerKM = <?php echo $rate['Rate_per_Km']; ?>;
            var totalPrice = (totalDistance * ratePerKM).toFixed(2);
            document.getElementById('price-<?php echo $rate['Taxi_type']; ?>').textContent = totalPrice + ' LKR';
        <?php endforeach; ?>
    }
</script>

<?php 
    include 'TemplateParts/Shared/NavMenu.php'; 
    include 'TemplateParts/Footer/footer.php'; 
?>
