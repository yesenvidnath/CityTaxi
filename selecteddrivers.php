<?php 
// Include the header part
include 'TemplateParts/Header/header.php'; 
?>

<!-- Main Section -->
<section class="register-page">
    <div class="container">
        <h1>Need a Ride? Select a Driver</h1>
        
        <!-- Driver Container -->
        <div class="driver-container">

            <!-- Driver Card 1 -->
            <div class="driver-card">
                <img src="Assets/Img/driver-image1.png" alt="Driver 1" width="100" height="100">
                <h3>Simon Alexander Downey</h3>
                <div class="vehicle-info">BMW - FZ 4565</div>
                <div class="driver-details">
                    <p>Time for Pickup: 5 minutes</p>
                    <p>Ride Fare: Rs. 750</p>
                </div>
                <div class="trusted">
                    Trusted 
                    <span class="trusted-icon">❤️</span>
                </div>
                <button class="btn-warning">Confirm Driver</button>
            </div>

            <!-- Driver Card 2 -->
            <div class="driver-card">
                <img src="Assets/Img/driver-image2.png" alt="Driver 2" width="100" height="100">
                <h3>Tommy Winston Shelby</h3>
                <div class="vehicle-info">Aqua - KV 8865</div>
                <div class="driver-details">
                    <p>Time for Pickup: 12 minutes</p>
                    <p>Ride Fare: Rs. 720</p>
                </div>
                <div class="trusted">
                    Trusted 
                    <span class="trusted-icon">❤️</span>
                </div>
                <button class="btn-warning">Confirm Driver</button>
            </div>

        </div>
    </div>
</section>

<!-- Include the navigation menu -->
<?php 
include 'TemplateParts/Shared/NavMenu.php'; 
?>

<!-- Include the footer -->
<?php 
include 'TemplateParts/Footer/footer.php'; 
?>
