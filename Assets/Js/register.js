// Get all the form pages
const formPages = document.querySelectorAll('.page');

// Initialize the current step and total steps
let currentStep = 1;
const totalSteps = formPages.length;
let userType = ''; // This will track the selected user type

// Initialize variables to store user data
let userData = {
    firstName: '',
    lastName: '',
    nicNo: '',
    contactNo: '',
    address: '',
    email: '',
    password: '',
    userType: ''
};

// Initialize the form to show only the first step on load and scroll to the top
function initializeForm() {
    formPages.forEach((page, index) => {
        page.style.display = index === 0 ? 'block' : 'none'; // Show only the first step
    });
    window.scrollTo(0, 0); // Scroll to the top of the page
}

// Function to show the current step and scroll to the top
function showStep(step) {
    formPages.forEach((page, index) => {
        page.style.display = index === step - 1 ? 'block' : 'none'; // Display the correct step
    });
    formPages[step - 1].scrollIntoView({ behavior: 'smooth', block: 'start' }); // Scroll to the top
}

// Function to load data into variables from the form
function loadFormData() {
    userData.firstName = document.querySelector('input[placeholder="First Name"]').value;
    userData.lastName = document.querySelector('input[placeholder="Last Name"]').value;
    userData.nicNo = document.querySelector('input[placeholder="NIC No"]').value;
    userData.contactNo = document.querySelector('input[placeholder="Contact No"]').value;
    userData.address = document.querySelector('input[placeholder="Address"]').value;
    userData.email = document.querySelector('input[placeholder="Email"]').value;
    userData.password = document.querySelector('input[placeholder="Password"]').value;
    console.log('General Data Loaded:', userData);
}

// Combined Event Listener for all registration buttons
document.querySelectorAll('.firstNext').forEach((button, index) => {
    button.addEventListener('click', () => {
        // Set userType based on which button is clicked
        if (index === 0) {
            userType = 'Passenger';
            userData.userType = 'Passenger';
            console.log("Passenger Button Clicked");
        } else if (index === 1) {
            userType = 'Driver';
            userData.userType = 'Driver';
            console.log("Driver Button Clicked");
        } else if (index === 2) {
            userType = 'Vehicle Owner';
            userData.userType = 'Vehicle Owner';
            console.log("Vehicle Owner Button Clicked");
        }

        // Move to the next step if applicable
        if (currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep); // Show the next page
            updateButtonText(); // Update button text based on user type
        }
    });
});

// Event listener for "Back" button
document.querySelector('.prev-1').addEventListener('click', () => {
    if (currentStep > 1) {
        location.reload();
    }
});

// Function to update button text based on user type
function updateButtonText() {
    const nextButton = document.querySelector('.next-1');
    if (userType === 'Passenger' && currentStep === 2) {
        nextButton.textContent = 'Submit'; // Change "Next" to "Submit" for Passenger
    } else {
        nextButton.textContent = 'Next'; // Keep it "Next" for Driver/Vehicle Owner
    }
}

document.querySelector('.prev-2').addEventListener('click', () => {
    if (currentStep > 1) {
      currentStep--;
      showStep(currentStep);
    }
  });

// Combined Event Listener for "Submit" and "Next" button
document.querySelector('.next-1').addEventListener('click', (event) => {
    if (currentStep < totalSteps) {
        // Check if the user type is Passenger and it's the final step
        if (userType === 'Passenger' && currentStep === 2) {
            event.preventDefault(); // Prevent default form submission
            loadFormData(); // Load Passenger form data into userData
            console.log('Passenger Data Loaded:', userData); // Log Passenger data
            submitPassengerData(); // Submit the Passenger data via AJAX
        }
        // Check for Driver or Vehicle Owner and handle the final step
        else if ((userType === 'Driver' || userType === 'Vehicle Owner') && currentStep === totalSteps) {
            event.preventDefault(); // Prevent default form submission
            loadFormData(); // Load Driver/Vehicle Owner form data into userData
            console.log(`${userType} Data Loaded (Final Step):`, userData); // Log Driver/Vehicle Owner data
            submitDriverVehicleOwnerData(); // Submit Driver/Vehicle Owner data via AJAX
        }
        // Handle intermediate steps for Driver and Vehicle Owner
        else if (userType === 'Driver' || userType === 'Vehicle Owner') {
            loadFormData(); // Load form data on each step
            console.log(`${userType} Data Loaded (Next Step):`, userData); // Log data for each step
            currentStep++; // Move to the next step
            showStep(currentStep); // Show the next page
        }
        // For any other case, move to the next step
        else {
            currentStep++;
            showStep(currentStep); // Show the next page for Passenger
        }
    }
});

document.querySelector('.submit').addEventListener('click', (event) => {
    event.preventDefault(); // Prevent the default form submission behavior

    // Check the user type and call the appropriate function
    if (userType === 'Passenger' && currentStep === 2) {
        loadFormData(); // Load Passenger form data into userData
        console.log('Passenger Data Loaded:', userData); // Log Passenger data
        submitPassengerData(); // Submit the Passenger data via AJAX
    } else if ((userType === 'Driver' || userType === 'Vehicle Owner') && currentStep === totalSteps) {
        loadFormData(); // Load Driver/Vehicle Owner form data into userData
        console.log(`${userType} Data Loaded (Final Step):`, userData); // Log Driver/Vehicle Owner data
        //submitDriverVehicleOwnerData(); // Submit Driver/Vehicle Owner data via AJAX
    }
});

document.addEventListener('click', function(event) {
    if (event.target.classList.contains('submit')) {
        event.preventDefault();
        if (userType === 'Driver' || userType === 'Vehicle Owner') {
            loadFormData(); // Load form data
            console.log(`${userType} Data Loaded (Final Step):`, userData);
            //submitDriverVehicleOwnerData();
        }
    }
});


function submitPassengerData() {
    const formData = new FormData();
    formData.append('first_name', document.querySelector('input[placeholder="First Name"]').value);
    formData.append('last_name', document.querySelector('input[placeholder="Last Name"]').value);
    formData.append('nic_no', document.querySelector('input[placeholder="NIC No"]').value);
    formData.append('contact_no', document.querySelector('input[placeholder="Contact No"]').value);
    formData.append('address', document.querySelector('input[placeholder="Address"]').value);
    formData.append('email', document.querySelector('input[placeholder="Email"]').value);
    formData.append('password', document.querySelector('input[placeholder="Password"]').value);
    formData.append('user_type', 'Passenger');
    const profilePicInput = document.getElementById('profile-pic');
    if (profilePicInput.files.length > 0) {
        formData.append('profile_pic', profilePicInput.files[0]);
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/CityTaxi/Functions/Common/Registration.php', true); // Adjusted the path to be relative to your web server root
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) { // Ensure the request is completed
            if (xhr.status === 200) { // Check if the HTTP status is OK
                console.log('Response:', xhr.responseText);
                if (xhr.responseText.includes("Registration successful")) {
                    alert('Registration successful!');
                    window.location.href = 'login.php';
                } else {
                    alert('Error in registration: ' + xhr.responseText);
                }
            } else {
                alert('Error in registration. Please try again.');
            }
        }
    };
    xhr.send(formData);
}


// Function to submit Driver or Vehicle Owner data
function submitDriverVehicleOwnerData() {
    if (!validateDriverVehicleOwnerData()) {
        alert('Please complete all required fields and ensure files are selected.');
        return; // Stop the function if validation fails
    }

    const formData = new FormData();
    formData.append('first_name', userData.firstName);
    formData.append('last_name', userData.lastName);
    formData.append('nic_no', userData.nicNo);
    formData.append('contact_no', userData.contactNo);
    formData.append('address', userData.address);
    formData.append('email', userData.email);
    formData.append('password', userData.password);
    formData.append('user_type', userData.userType);

    // Append file inputs
    formData.append('profile_pic', document.getElementById('profile-pic').files[0]);
    formData.append('nic_front', document.getElementById('nic-front').files[0]);
    formData.append('nic_back', document.getElementById('nic-back').files[0]);
    formData.append('license_front', document.getElementById('license-front').files[0]);
    formData.append('license_back', document.getElementById('license-back').files[0]);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/CityTaxi/Functions/Common/registerDriverVehicleOwner.php', true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log('Response:', xhr.responseText);
            if (xhr.responseText.includes("Registration successful")) {
                alert('Registration successful!');
                window.location.href = 'login.php';
            } else {
                alert('Error in registration: ' + xhr.responseText);
            }
        }
    };
    xhr.send(formData);
}

// Validate data before submission
function validateDriverVehicleOwnerData() {
    // Example validation, extend it according to your actual form requirements
    return document.getElementById('profile-pic').files.length &&
           document.getElementById('nic-front').files.length &&
           document.getElementById('nic-back').files.length &&
           document.getElementById('license-front').files.length &&
           document.getElementById('license-back').files.length;
}

// Bind event listener to the submit button
document.addEventListener('DOMContentLoaded', function() {
    const submitBtn = document.querySelector('.submit');
    if (submitBtn) {
        submitBtn.addEventListener('click', function(event) {
            event.preventDefault();
            submitDriverVehicleOwnerData();
        });
    }
});

// Initialize the form to show only the first step on load
initializeForm();


// General Information Profile pic preview
document.addEventListener('DOMContentLoaded', function() {
    var fileInput = document.getElementById('profile-pic');
    var imagePreview = document.getElementById('img-pic');

    fileInput.addEventListener('change', function() {
        if (fileInput.files && fileInput.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                imagePreview.src = e.target.result;
            };

            reader.readAsDataURL(fileInput.files[0]);
        } else {
            imagePreview.src = 'img/default-image.jpg'; // Provide a default image source
        }
    });
});

// Profile picture remove button function

document.addEventListener("DOMContentLoaded", function () {
    // Get elements
    var profilePicInput = document.getElementById("profile-pic");
    var imgPreview = document.getElementById("img-pic");
    var removeButton = document.querySelector(".btn.btn-danger"); // The remove button

    // Function to remove the image and reset the input
    function removeProfilePic() {
        imgPreview.src = ""; // Clear the previewed image
        profilePicInput.value = ""; // Reset the file input
    }

    // Attach event listener to the remove button
    removeButton.addEventListener("click", function (e) {
        e.preventDefault(); // Prevent any default behavior
        removeProfilePic(); // Call the remove function
    });

    // File input change event for preview
    profilePicInput.addEventListener("change", function () {
        if (profilePicInput.files && profilePicInput.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                imgPreview.src = e.target.result; // Set the previewed image
            };
            reader.readAsDataURL(profilePicInput.files[0]); // Read the file to get its data URL
        }
    });
});

// NIC Image preview section

document.addEventListener("DOMContentLoaded", function () {
    // Elements for NIC Front
    var nicFrontInput = document.getElementById("nic-front"); // File input for NIC Front
    var nicFrontImg = document.getElementById("img-nic-front"); // Image element for NIC Front preview
    var nicFrontRemove = document.getElementById("remove-nic-front"); // Remove button for NIC Front

    // Elements for NIC Back
    var nicBackInput = document.getElementById("nic-back"); // File input for NIC Back
    var nicBackImg = document.getElementById("img-nic-back"); // Image element for NIC Back preview
    var nicBackRemove = document.getElementById("remove-nic-back"); // Remove button for NIC Back

    // Handle image preview for a specific input and image
    function handleImagePreview(fileInput, imagePreview) {
        fileInput.addEventListener("change", function () {
            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    imagePreview.src = e.target.result; // Set the previewed image
                };

                reader.readAsDataURL(fileInput.files[0]);
            }
        });
    }

    // Handle removing the image and resetting the input field
    function handleRemove(fileInput, imagePreview, defaultIcon) {
        fileInput.value = ""; // Reset the file input
        imagePreview.src = defaultIcon; // Reset to default icon
    }

    // Default icon for reset
    var defaultIcon = "http://100dayscss.com/codepen/upload.svg";

    // Attach preview functionality
    handleImagePreview(nicFrontInput, nicFrontImg);
    handleImagePreview(nicBackInput, nicBackImg);

    // Attach remove functionality to both buttons
    nicFrontRemove.addEventListener("click", function (e) {
        e.preventDefault(); // Prevent default behavior
        handleRemove(nicFrontInput, nicFrontImg, defaultIcon); // Reset NIC Front
    });

    nicBackRemove.addEventListener("click", function (e) {
        e.preventDefault(); // Prevent default behavior
        handleRemove(nicBackInput, nicBackImg, defaultIcon); // Reset NIC Back
    });
});

// Drivers license preview image and remove

document.addEventListener("DOMContentLoaded", function () {
    // Elements for Drivers license image 1
    var licenseFrontInput = document.getElementById("license-front"); // File input for Drivers license image 1
    var licenseFrontImg = document.getElementById("img-license-front"); // Image element for Drivers license image 1 preview
    var licenseFrontRemove = document.getElementById("remove-license-front"); // Remove button for Drivers license image 1
  
    // Elements for Drivers license Imane 2
    var licenseBackInput = document.getElementById("license-back"); // File input for Drivers license Imane 2
    var licenseBackImg = document.getElementById("img-license-back"); // Image element for Drivers license Imane 2 preview
    var licenseBackRemove = document.getElementById("remove-license-back"); // Remove button for Drivers license Imane 2
  
    // Handle image preview for a specific input and image
    function handleImagePreview(fileInput, imagePreview) {
        fileInput.addEventListener("change", function () {
            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();
  
                reader.onload = function (e) {
                    imagePreview.src = e.target.result; // Set the previewed image
                };
  
                reader.readAsDataURL(fileInput.files[0]);
            }
        });
    }
  
    // Handle removing the image and resetting the input field
    function handleRemove(fileInput, imagePreview, defaultIcon) {
        fileInput.value = ""; // Reset the file input
        imagePreview.src = defaultIcon; // Reset to default icon
    }
  
    // Default icon for reset
    var defaultIcon = "http://100dayscss.com/codepen/upload.svg";
  
    // Attach preview functionality
    handleImagePreview(licenseFrontInput, licenseFrontImg);
    handleImagePreview(licenseBackInput, licenseBackImg);
  
    // Attach remove functionality to both buttons
    licenseFrontRemove.addEventListener("click", function (e) {
        e.preventDefault(); // Prevent default behavior
        handleRemove(licenseFrontInput, licenseFrontImg, defaultIcon); // Reset NIC Front
    });
  
    licenseBackRemove.addEventListener("click", function (e) {
        e.preventDefault(); // Prevent default behavior
        handleRemove(licenseBackInput, licenseBackImg, defaultIcon); // Reset NIC Back
    });
  });

  function sendRegistrationData(userData) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'Registration.php', true);  // Send request to Registration.php
    xhr.setRequestHeader('Content-Type', 'application/json');  // Send data as JSON
    xhr.onload = function() {
        console.log("Server response:", xhr.responseText); // Log the server response
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                alert('Registration successful!');
                window.location.href = 'login.php';  // Redirect to login after success
            } else {
                alert('Error: ' + response.message);
            }
        } else {
            alert('Error: Failed to register. Please try again.');
        }
    };

    // Log the data being sent
    console.log("Sending registration data:", userData);

    // Send the userData object as JSON to the backend
    xhr.send(JSON.stringify(userData));
}