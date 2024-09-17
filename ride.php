<?php 
// Include the header part
include 'TemplateParts/Header/header.php'; 

// Fetch API keys from the .env file
$dotenv = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/env/.env');
$openRouteServiceApiKey = $dotenv['OpenRouteService_API_Key'];
$openCageApiKey = $dotenv['OpenCage_API_Key'];
?>

<!-- Main content -->
<section class="ride-info-section">
    <div class="driver-info">
        <h2>Buckle Up For The Ride</h2>

        <div class="form-group" id="manualLocationSection">
            <label for="startLocation">Start Location</label>
            <input type="text" class="form-control" id="startLocation" placeholder="Enter Start Location">
        </div>

        <div class="form-group">
            <label for="endLocation">End Location</label>
            <input type="text" class="form-control" id="endLocation" placeholder="Enter End Location">
        </div>

        <button class="btn btn-warning" onclick="showRoute()">Show Route</button>
    </div>

    <div class="map-info">
        <div id="map" style="width: 800px; height: 600px;"></div>
        <div id="details">
            <h4>Route Details</h4>
            <p id="routeDetails"></p>
        </div>
    </div>
</section>

<script>
    // Pass API keys to JavaScript
    const openRouteServiceApiKey = '<?php echo $openRouteServiceApiKey; ?>';
    const openCageApiKey = '<?php echo $openCageApiKey; ?>';
</script>


<?php 
include 'TemplateParts/Shared/NavMenu.php'; 
include 'TemplateParts/Footer/footer.php'; 
?>