    <?php
        $currentFile = basename($_SERVER['PHP_SELF']);
        
        if ($currentFile == 'index.php') {
            // Display the first footer
            ?>
            <!-- Footer -->
            <footer class="footer">
                <div class="container">
                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <h5>Company</h5>
                            <ul class="footer-links">
                                <li><a href="#">About us</a></li>
                                <li><a href="#">Careers</a></li>
                                <li><a href="#">Press</a></li>
                            </ul>
                        </div>
                        <div class="col-md-3 mb-4">
                            <h5>Products</h5>
                            <ul class="footer-links">
                                <li><a href="#">Ride</a></li>
                                <li><a href="#">Drive</a></li>
                                <li><a href="#">Business</a></li>
                            </ul>
                        </div>
                        <div class="col-md-3 mb-4">
                            <h5>Support</h5>
                            <ul class="footer-links">
                                <li><a href="#">Help Center</a></li>
                                <li><a href="#">Safety</a></li>
                                <li><a href="#">Terms</a></li>
                            </ul>
                        </div>
                        <div class="col-md-3 mb-4">
                            <h5>Follow Us</h5>
                            <div class="social-links">
                                <a href="#" class="mr-3"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="mr-3"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="mr-3"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-12 text-center">
                            <p class="mb-0">&copy; 2024 RideShare. All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </footer>
            <?php
        } else {
            // Display the second footer
            ?>
            <!-- Footer -->
            <footer class="footer bg-light text-center py-3">
                <p>© 2024 City Taxi. All rights reserved.</p>
            </footer>
            <?php
        }
    ?>


    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- SweetAlert JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

    <!-- jQuery Validation Plugin -->
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.19.3/additional-methods.min.js"></script>

    <!-- jQuery library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- jQuery Validation Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

    <!-- Custom JS -->
    <script src="/CityTaxi/Assets/Js/main.js"></script>


    <?php 
        showLinkOnRide('<script src="/CityTaxi/Assets/Js/ride.js"></script>');
        showLinkOnDriverProfilePage( '<script src="/CityTaxi/Assets/Js/driver.js"></script>');
    ?> 
</body>
</html>
