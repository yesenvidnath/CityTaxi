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

// Get all the form pages and the progress bar
const formPages = document.querySelectorAll('.page');

// Initialize the current step and total steps
let currentStep = 1;
const totalSteps = formPages.length;

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

// Event listeners for the "Next" and "Back" buttons
document.querySelector('.firstNext').addEventListener('click', () => {
    if (currentStep < totalSteps) {
      currentStep++;
      showStep(currentStep);
    }
  });
  
  document.querySelector('.prev-1').addEventListener('click', () => {
    if (currentStep > 1) {
      currentStep--;
      showStep(currentStep);
    }
  });

  document.querySelector('.submit').addEventListener('click', () => {
    // Optional: Delay the reload
    setTimeout(() => {
      location.reload();
    }, 1000); // Delay of 1 second
  });

  // Initialize the form to show only the first step on load
initializeForm();

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