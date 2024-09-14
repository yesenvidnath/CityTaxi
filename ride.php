<?php 
// Include the header part
include 'TemplateParts/Header/header.php'; 
?>

<div class="container register-page center-content">
    <div class="row">
        <div class="col-lg-6 image-section">
            <img src="Assets/Img/scooter.png" alt="Scooter Image">
        </div>

        <div class="col-lg-6 form-section">
            <h1>Need a Quick Ride?</h1>

            <form action="submit_driver.php" method="post">
                <input type="text" class="form-control" placeholder="Enter Pickup Point">
                <input type="text" class="form-control" placeholder="Enter Destination">
                <button type="submit" class="btn btn-warning">Request Now</button>        
            </form>
        </div>
    </div>
</div>

<!-- Include the navigation menu -->
<?php 
include 'TemplateParts/Shared/NavMenu.php'; 
?>

<!-- Include the footer -->
<?php 
include 'TemplateParts/Footer/footer.php'; 
?>
