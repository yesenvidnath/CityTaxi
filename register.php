<?php 
// Include the header part
include 'TemplateParts/Header/header.php'; 
?>

<div class="container register-page center-content">
    <div class="row">
        <div class="col-lg-6 form-section">
            <h1>Drive. Earn. Enjoy. Repeat.</h1>

            <form id="registrationForm" action="Functions/Common/LoginRegistration.php" method="post" enctype="multipart/form-data">
                <!-- Add a hidden input to specify the registration process -->
                <input type="hidden" name="registration" value="true">
                <!-- Existing form fields... -->

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="first_name" placeholder="Enter First Name" required data-msg="Please enter your first name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="last_name" placeholder="Enter Last Name" required data-msg="Please enter your last name">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="email" class="form-control" name="email_address" placeholder="Enter Email Address" required data-msg="Please enter a valid email address">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Create Password" required data-msg="Please provide a password">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required data-msg="Please confirm your password" data-rule-equalTo="#password">
                            <button type="button" onclick="togglePasswordVisibility()">Show/Hide</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="nic_no" placeholder="Enter NIC No">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <input type="text" class="form-control" name="address" placeholder="Enter Address">
                        </div>
                    </div>
                </div>

                <!-- Add image upload field -->
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="user_img">Upload Profile Image:</label>
                            <input type="file" class="form-control" name="user_img" id="user_img" required data-msg="Please upload an image">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn btn-warning">Apply to Drive</button>
                </div>
            </form>
        </div>
        <div class="col-lg-6 image-section">
            <img src="Assets/Img/register_page_image.png" alt="Drive Image" class="img-fluid">
        </div>
    </div>
</div>

<!-- Display SweetAlert alerts if status is passed -->
<?php 
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
        $message = htmlspecialchars($_GET['message']);
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showAlert('$status', '$message');
                // Redirect to index.php after 2 seconds if status is success
                if ('$status' === 'success') {
                    setTimeout(function() {
                        window.location.href = '/CityTaxi/index.php';
                    }, 2000);
                }
            });
        </script>
        ";
    }
?>

<!-- Include the navigation menu -->
<?php 
include 'TemplateParts/Shared/NavMenu.php'; 
?>

<!-- Include the footer -->
<?php 
include 'TemplateParts/Footer/footer.php'; 
?>
