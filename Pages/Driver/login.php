<?php 
// Start session
session_start(); 
// Include the header
include '../../TemplateParts/Header/header.php'; 
?>

<div class="container mt-5">
    <h2 class="text-center">Driver Login</h2>
    
    <?php if (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['message']); ?></div>
    <?php endif; ?>
    
    <form action="../../Functions/Common/LoginRegistration.php" method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Login</button>
        
        <div class="text-center mt-3">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </form>
</div>

<?php 

include '../../TemplateParts/Shared/NavMenu.php'; 


// Include the footer
include '../../TemplateParts/Footer/footer.php'; 
?>
