<?php
// Include header
require_once('../app/views/shared/header.php');
?>

<!-- Homepage content starts here -->
<div class="container my-5">
    <div class="jumbotron text-center">
        <h1 class="display-4">Welcome to City Taxi</h1>
        <p class="lead">Your journey, your control. Book your ride now with City Taxi!</p>
        <hr class="my-4">
        <p>City Taxi offers you an easy and convenient way to book rides at your fingertips.</p>
        <a class="btn btn-primary btn-lg" href="/passenger/bookRide" role="button">Book a Ride</a>
        <a class="btn btn-secondary btn-lg" href="/driver/signup" role="button">Become a Driver</a>
    </div>
</div>

<!-- Taxi Service Features Section -->
<div class="container text-center my-5">
    <div class="row">
        <div class="col-md-4">
            <img src="/assets/images/fast.png" alt="Fast Service" class="img-fluid mb-3">
            <h3>Fast Service</h3>
            <p>Get to your destination quickly and efficiently with our taxi services.</p>
        </div>
        <div class="col-md-4">
            <img src="/assets/images/safe.png" alt="Safe Rides" class="img-fluid mb-3">
            <h3>Safe Rides</h3>
            <p>Your safety is our priority. Our drivers are vetted and well-trained.</p>
        </div>
        <div class="col-md-4">
            <img src="/assets/images/convenient.png" alt="Convenient Booking" class="img-fluid mb-3">
            <h3>Convenient Booking</h3>
            <p>Book your taxi ride easily from anywhere, anytime.</p>
        </div>
    </div>
</div>

<!-- Testimonials Section -->
<div class="container my-5">
    <h2 class="text-center mb-4">What Our Riders Say</h2>
    <div class="row">
        <div class="col-md-4">
            <blockquote class="blockquote">
                <p class="mb-0">City Taxi is so convenient and reliable. I use it every day!</p>
                <footer class="blockquote-footer">John Doe</footer>
            </blockquote>
        </div>
        <div class="col-md-4">
            <blockquote class="blockquote">
                <p class="mb-0">Fast and friendly service every time. Highly recommend!</p>
                <footer class="blockquote-footer">Jane Smith</footer>
            </blockquote>
        </div>
        <div class="col-md-4">
            <blockquote class="blockquote">
                <p class="mb-0">Safe and professional drivers. I feel secure using City Taxi.</p>
                <footer class="blockquote-footer">Mark Johnson</footer>
            </blockquote>
        </div>
    </div>
</div>

<!-- Footer -->
<?php
// Include footer
require_once('../app/views/shared/footer.php');
?>
