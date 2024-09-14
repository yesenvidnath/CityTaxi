<?php
// Import the header
include 'TemplateParts/Header/header.php';
// Import the right-side nav menu
include 'TemplateParts/Shared/NavMenu.php';
?>

<div class="container-fluid text-center">
    <div class="row align-items-center" style="height: 100vh;">
        <div class="col-md-12">
            <img src="<?php echo dirname($_SERVER['PHP_SELF']); ?>/Assets/Img/taxi-icon.png" alt="Taxi Icon" class="img-fluid" style="width: 150px;">
            <h1 class="display-4">Your Journey, Your Control</h1>
            <div class="mt-4">
                <button class="btn btn-warning btn-lg mr-2">Apply to Drive</button>
                <button class="btn btn-outline-secondary btn-lg">Sign up to Ride</button>
            </div>
            <center><p><a href="login.php">Have a Account ? Login in now</a></p></center>
        </div>
    </div>
</div>


<?php 
if (isset($_GET['status']) && isset($_GET['message'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showAlert('".$_GET['status']."', '".$_GET['message']."');
        });
    </script>";
}
?>

<?php
// Import the footer
include 'TemplateParts/Footer/footer.php';
?>
