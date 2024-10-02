// Function to open modal for adding a new user
function openModalForNewUser() {
    clearModalFields(); // Clear all input fields in the modal
    document.getElementById('modal_user_ID').value = ''; // No ID for new user
    document.getElementById('editUserModalLabel').innerText = 'Add New User';
    $('#editUserModal').modal('show'); // Using jQuery to show modal
}

// Function to load user details into the modal for editing
/*function loadUserDetails(userId) {
    document.getElementById('modal_user_ID').value = userId; // Set user ID for update
    document.getElementById('editUserModalLabel').innerText = 'Edit User';
    // Fetch user details from the server and fill out the form
    fetch('getUserDetails.php?user_ID=' + userId)
    .then(response => response.json())
    .then(data => {
        fillModalFields(data); // Fill the modal fields with fetched data
    })
    .catch(error => console.error('Error loading user details:', error));
    $('#editUserModal').modal('show');
}*/

/*function loadUserDetails(userId) {
    console.log("Requested user ID:", userId); // Check if userId is captured correctly
    fetch(`getUserDetails.php?user_ID=${userId}`)
    .then(response => response.json())
    .then(data => {
        console.log("Data received:", data); // What does the data look like?
        fillModalFields(data);
        $('#editUserModal').modal('show');
    })
    .catch(error => {
        console.error('Error loading user details:', error);
        alert('Failed to load data: ' + error.message);
    });
}*/

function loadUserDetails(userId) {
    fetch(`getUserDetails.php?user_ID=${userId}`)
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            throw new Error(data.error);
        }
        fillModalFields(data.data);
        $('#editUserModal').modal('show');
    })
    .catch(error => {
        console.error('Error loading user details:', error);
        alert('Failed to load data: ' + error.message);
    });
}



// Function to save user details
function saveUserDetails() {
    var userId = document.getElementById('modal_user_ID').value;
    var formData = new FormData(document.getElementById('editUserForm'));
    var endpoint = userId ? 'updateUser.php' : 'insertUser.php'; // Determine endpoint based on existence of user ID
    fetch(endpoint, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message); // Alert with response message
        if(data.success) {
            $('#editUserModal').modal('hide'); // Hide modal on success
            location.reload(); // Reload the page to refresh user list
        }
    })
    .catch(error => console.error('Error saving user:', error));
}

// Function to clear all input fields in the modal
function clearModalFields() {
    document.getElementById('editUserForm').reset();
}

// Function to fill the modal fields with user data
/*function fillModalFields(data) {
    document.getElementById('modal_first_name').value = data.First_name;
    document.getElementById('modal_last_name').value = data.Last_name;
    document.getElementById('modal_email').value = data.Email;
    document.getElementById('modal_nic_no').value = data.NIC_No;
    document.getElementById('modal_mobile_number').value = data.mobile_number;
    document.getElementById('modal_address').value = data.Address;
}*/

function fillModalFields(data) {
    if (data) {
        document.getElementById('modal_first_name').value = data.First_name || '';
        document.getElementById('modal_last_name').value = data.Last_name || '';
        document.getElementById('modal_email').value = data.Email || '';
        document.getElementById('modal_nic_no').value = data.NIC_No || '';
        document.getElementById('modal_mobile_number').value = data.mobile_number || '';
        document.getElementById('modal_address').value = data.Address || '';
    } else {
        console.log("No data returned for this user.");
    }
}

