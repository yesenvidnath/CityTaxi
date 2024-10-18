<?php
// Import the header
include 'TemplateParts/Header/header.php';
// Import the right-side nav menu
include 'TemplateParts/Shared/NavMenu.php';


?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="row hero-content">
            <div class="col-lg-6">
                <h1>Go anywhere with confidence</h1>
                <p class="lead">Request a ride, hop in, and relax. Your destination awaits.</p>
                <div class="d-flex flex-wrap">
                    <button class="btn btn-primary mr-3 mb-3">Sign up to ride</button>
                    <button class="btn btn-outline-light mb-3">Become a driver</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="fas fa-map-marked-alt feature-icon"></i>
                    <h3>Easy booking</h3>
                    <p>Book your ride with just a few taps and get picked up by a nearby driver</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="fas fa-shield-alt feature-icon"></i>
                    <h3>Safe travels</h3>
                    <p>Know you're in good hands with professional drivers and 24/7 support</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="fas fa-clock feature-icon"></i>
                    <h3>On-time pickup</h3>
                    <p>Get to your destination on time, every time with reliable drivers</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Driver Section -->
<section class="driver-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2>Drive when you want, earn what you need</h2>
                <p class="lead mb-4">Set your own schedule, be your own boss, and achieve your goals.</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-primary mr-2"></i>Flexible schedule</li>
                    <li><i class="fas fa-check text-primary mr-2"></i>Weekly payments</li>
                    <li><i class="fas fa-check text-primary mr-2"></i>Sign up in minutes</li>
                </ul>
                <button class="btn btn-primary mt-4">Start earning</button>
            </div>
            <div class="col-lg-6">
                <img src="https://plus.unsplash.com/premium_photo-1681821679118-bb069eeb2d98?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Driver" class="img-fluid driver-img">
            </div>
        </div>
    </div>
</section>

<!-- Safety Section -->
<section class="safety-section">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2>Your safety matters</h2>
                <p class="lead">We're committed to helping keep you safe when you ride with us</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="safety-card">
                    <i class="fas fa-user-shield mb-3 text-primary fa-2x"></i>
                    <h4>Driver screening</h4>
                    <p>All drivers undergo thorough background checks</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="safety-card">
                    <i class="fas fa-phone-alt mb-3 text-primary fa-2x"></i>
                    <h4>24/7 support</h4>
                    <p>Help is always just a tap away</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="safety-card">
                    <i class="fas fa-route mb-3 text-primary fa-2x"></i>
                    <h4>Share your trip</h4>
                    <p>Let loved ones follow your route in real-time</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Download App Section -->
<!-- <section class="download-app">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2>Get the app</h2>
                <p class="lead mb-4">Download now for a better riding experience</p>
                <div class="app-badges">
                    <img src="/api/placeholder/200/60" alt="App Store" class="mr-3">
                    <img src="/api/placeholder/200/60" alt="Google Play">
                </div>
            </div>
            <div class="col-lg-6">
                <img src="/api/placeholder/400/600" alt="Mobile App" class="img-fluid">
            </div>
        </div>
    </div>
</section> -->



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


<?php
// Import the footer
include 'TemplateParts/Footer/footer.php';
?>
