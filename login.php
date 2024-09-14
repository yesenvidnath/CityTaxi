<?php 
// Include the header part
include 'TemplateParts/Header/header.php'; 
?>

<div class="container register-page center-content">

    <div class="row">

        <div class="col-lg-6 form-section">
            <h1>Drive. Earn. Enjoy. Repeat.</h1>

            <form action="Functions/Common/LoginRegistration.php" method="post">
                <input type="text" class="form-control" name="email" placeholder="Enter Email">
                <input type="password" class="form-control" name="password" placeholder="Enter Password">
                <button type="submit" class="btn btn-warning">Login</button>
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
