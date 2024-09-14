// Menu toglle Script

// Select the menu toggle button and the nav menu
const menuToggle = document.getElementById('menuToggle');
const navMenu = document.querySelector('.nav-menu');
const closeMenu = document.getElementById('closeMenu');

// Event listener to open the menu (only for mobile)
menuToggle.addEventListener('click', () => {
    navMenu.classList.add('active');
});

// Event listener to close the menu (only for mobile)
closeMenu.addEventListener('click', () => {
    navMenu.classList.remove('active');
});


// Display Alert Accordingly
function showAlert(status, message) {
    // Ensure 'status' is either 'success' or 'error'
    const alertType = (status === 'success' || status === 'error') ? status : 'info';
    
    // Call swal function from SweetAlert 1.1.3
    swal({
        title: alertType === 'success' ? 'Success!' : 'Error!',
        text: message,
        type: alertType, // 'success' or 'error'
        confirmButtonText: 'OK'
    }, function() {
        // This function is called when the user clicks "OK"
        clearURLParams();
    });
}

// Function to clear URL parameters
function clearURLParams() {
    // Remove the query parameters from the URL without reloading the page
    if (history.pushState) {
        var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.pushState({ path: newUrl }, '', newUrl);
    }
}