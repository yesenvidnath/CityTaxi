<?php 
// Include the header part
include 'TemplateParts/Header/header.php'; 
?>

<div class="container register-page center-content">
    <div class="row">
        <div class="col-lg-6 form-section">
            <h1>Drive. Earn. Enjoy. Repeat.</h1>
            <form action="submit_driver.php" method="post">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="first_name" placeholder="Enter First Name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="last_name" placeholder="Enter Last Name">
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="mobile_number" placeholder="Enter Mobile Number">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="vehicle_detail" placeholder="Vehicle Detail">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="email_address" placeholder="Enter Email Address">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="license_number" placeholder="License Number">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button type="submit" class="btn btn-warning">Apply to Drive</button>
                    </div>
                    <div>
                        <a href="become_rider.php" class="text-decoration-none">Become a Rider?</a>
                    </div>
                </div>
                                
            </form>
        </div>
        <div class="col-lg-6 image-section">
            <img src="Assets/Img/register_page_image.png" alt="Drive Image" class="img-fluid">
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
