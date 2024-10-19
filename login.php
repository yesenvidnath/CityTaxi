<?php 
// Include the header part
include 'TemplateParts/Header/header.php'; 
?>

<div class="background">
    <div class="gradient-overlay"></div>
</div>
<div class="container">
    <div class="login-card">
        <h1>Welcome Back</h1>
        <div class="feature-icons">
            <div class="feature-icon">
                <i class="fas fa-car"></i>
                <span>Drive</span>
            </div>
            <div class="feature-icon">
                <i class="fas fa-dollar-sign"></i>
                <span>Earn</span>
            </div>
            <div class="feature-icon">
                <i class="fas fa-smile"></i>
                <span>Enjoy</span>
            </div>
        </div>
        <form action="Functions/Common/LoginRegistration.php" method="post">
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email" >
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password" >
            </div>
            <button type="submit" class="btn-primary">
                Log In
            </button>
        </form>
        <p class="signup-link">New to our platform? <a href="register.php">Sign up</a></p>
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
