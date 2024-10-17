<?php 
// Include the header part
include 'TemplateParts/Header/header.php'; 
?>

<div class="container-fluid">

    <div class="container">
        <div>
            <h1 class="mt-5 mb-4">Drive. Earn. Enjoy. Repeat.</h1>
        </div>
    </div>
    <br>

    <!-- General Information Section -->
    
    <div class="form-outer">
        <form>

            <!-- Seperate Registration -->

            <div class="page slide-page">
                <div class="container register-page center-content">
                    
                    <div class="row">

                        <div class="col-6">
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="firstNext btn btn-warning">Register as a Passenger</button>
                            </div>
                            <br>
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="firstNext btn btn-warning">Register as a Driver</button>
                            </div>
                            <br>
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="firstNext btn btn-warning">Register as a Vehicle Owner</button>
                            </div>
                        </div>

                        <div class="col-6 image-section">
                            <img src="Assets/Img/register_page_image.png" alt="Drive Image" class="img-fluid">
                        </div>

                    </div>
                </div>
            </div>

            <!-- General User Registration -->

            <div class="page">
                <div class="container">
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-3">
                                <label for="profile-pic" class="font-weight-bold p-2">Profile Picture</label>
                            </div>
                            <div class="col-9">
                                <div class="row align-items-center">
                                    <div class="col-6 px-5">
                                        <div class="card card-image p-2">
                                            <img src="" alt="" id="img-pic" class="card-image-top">
                                        </div>
                                    </div>
                                    <div class="col-6 px-5">
                                        <div class="card card-profile-pic p-2">
                                            <input type="file" class="form-control-file p-2" id="profile-pic" accept=".png,.jpg,.jpeg">
                                        </div>
                                        <button id="remove-profile-pic" class="btn btn-danger mt-3" type="button">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-3">
                                <label for="full-name" class="font-weight-bold p-2">Full Name</label>
                            </div>
                            <div class="col-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" placeholder="First Name">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" placeholder="Last Name">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-3">
                                <label for="nic-no" class="font-weight-bold p-2">NIC No</label>
                            </div>
                            <div class="col-9">
                                <input type="text" class="form-control" placeholder="NIC No">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-3">
                                <label for="contact-no" class="font-weight-bold p-2">Contact No</label>
                            </div>
                            <div class="col-9">
                                <input type="text" class="form-control" placeholder="Contact No">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-3">
                                <label for="address" class="font-weight-bold p-2">Address</label>
                            </div>
                            <div class="col-9">
                                <input type="text" class="form-control" placeholder="Address">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3 mb-3">
                        <div class="col-auto mr-auto">
                            <button type="button" class="prev-1 btn btn-warning font-weight-bold px-5">Back</button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="next-1 btn btn-warning font-weight-bold px-5">Next</button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Driver and Vehicle Owner Registration -->

            <div class="page">
                <div class="container">

                    <div class="form-group">
                        <div class="row">
                            <div class="col-3">
                                <label for="full-name" class="font-weight-bold p-2">NIC Images</label>
                            </div>
                            
                            <div class="col-9">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="row ml-5">
                                            <div class="card card-image">
                                                <div class="center">
                                                    <div class="dropzone">
                                                        <img src="http://100dayscss.com/codepen/upload.svg" id="img-nic-front" class="upload-icon" />
                                                        <p class="nic-dec">NIC Front</p>
                                                        <input type="file" class="upload-input" id="nic-front" accept=".png,.jpg,.jpeg"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row ml-5">
                                            <button id="remove-nic-front" class="btn btn-danger" type="button">Remove</button>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="row">
                                            <div class="card card-image">
                                                <div class="center">
                                                    <div class="dropzone">
                                                        <img src="http://100dayscss.com/codepen/upload.svg" id="img-nic-back" class="upload-icon" />
                                                        <p class="nic-dec">NIC Back</p>
                                                        <input type="file" class="upload-input" id="nic-back" accept=".png,.jpg,.jpeg"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <button id="remove-nic-back" class="btn btn-danger" type="button">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-3">
                                <label for="full-name" class="font-weight-bold p-2">Driver's Licence No</label>
                            </div>
                            
                            <div class="col-9">
                                <div class="row">
                                    <div class="col-10 align-self-center ml-4">
                                        <input type="text" class="form-control" placeholder="Driver's Licence No">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-3">
                                <label for="full-name" class="font-weight-bold p-2">Driver's Licence No</label>
                            </div>
                            
                            <div class="col-9">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="row ml-5">
                                            <div class="card card-image">
                                                <div class="center">
                                                    <div class="dropzone">
                                                        <img src="http://100dayscss.com/codepen/upload.svg" id="img-license-front" class="upload-icon" />
                                                        <p class="nic-dec">Licence Front</p>
                                                        <input type="file" class="upload-input" id="license-front" accept=".png,.jpg,.jpeg"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row ml-5">
                                            <button id="remove-license-front" class="btn btn-danger" type="button">Remove</button>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="row">
                                            <div class="card card-image">
                                                <div class="center">
                                                    <div class="dropzone">
                                                        <img src="http://100dayscss.com/codepen/upload.svg" id="img-license-back" class="upload-icon" />
                                                        <p class="nic-dec">Licence Back</p>
                                                        <input type="file" class="upload-input" id="license-back" accept=".png,.jpg,.jpeg"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <button id="remove-license-back" class="btn btn-danger" type="button">Remove</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Toggle Button Section -->
                                <div class="row mt-5 ml-5" id="toggleDriverSwitch" style="display: none;">
                                    <div class="custom-control custom-switch">
                                        <label class="custom-control-label" for="toggleDriverSwitch">Also want to become a Driver?</label>
                                        <input type="checkbox" class="custom-control-input" id="toggleDriverSwitch">
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <br>

                    <div class="row mt-3 mb-3">
                        <div class="col-auto mr-auto">
                            <button type="button" class="prev-2 btn btn-warning font-weight-bold px-5">Back</button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="submit btn btn-warning font-weight-bold px-5">Submit</button>
                        </div>
                    </div>

                </div>
            </div>

        </form>
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
