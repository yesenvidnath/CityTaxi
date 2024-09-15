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



// Function to confirm logout
function confirmLogout() {
    swal({
        title: "Are you sure?",
        text: "You will be logged out of the application.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, log me out!",
        cancelButtonText: "Cancel",
        closeOnConfirm: false,
        closeOnCancel: true
    },
    function(isConfirm) {
        if (isConfirm) {
            // Redirect to the logout PHP script
            window.location.href = "/CityTaxi/Functions/Common/logout.php";
        }
    });
}



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


// Registration Js Funtions

function togglePasswordVisibility() {
    var password = document.getElementById("password");
    var confirm_password = document.getElementById("confirm_password");
    if (password.type === "password") {
        password.type = "text";
        confirm_password.type = "text";
    } else {
        password.type = "password";
        confirm_password.type = "password";
    }
}

$(document).ready(function() {
    $("#registrationForm").validate({
        errorElement: 'div',
        errorClass: 'invalid-feedback',
        highlight: function(element, errorClass, validClass) {
            $(element).addClass('is-invalid').removeClass('is-valid');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).addClass('is-valid').removeClass('is-invalid');
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        rules: {
            confirm_password: { // Special handling for confirm password to match
                equalTo: "#password"
            }
        }
    });

    // Define custom messages and rules based on data attributes
    $("#registrationForm input").each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: $(this).data('msg')
            }
        });
    });
});


/**
 * Hides elements on the page based on the provided CSS selector.
 * @param {string} selector - The CSS selector of the elements to hide.
 */
function hideElementsBySelector(selector) {
    const elements = document.querySelectorAll(selector);
    elements.forEach(element => {
        element.style.display = 'none';
    });
}