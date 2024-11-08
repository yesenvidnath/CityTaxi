// Initialize the map after the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    
    let currentLocationMarker = null; 
    let identifiedLatitude = null; 
    let identifiedLongitude = null; 

    // Default coordinates (center on Sri Lanka) in case driverLocation is not available
    let defaultCoordinates = [7.8731, 80.7718];

    // Check if driverLocation is available and parse it
    if (driverLocation) {
        const locationParts = driverLocation.split(','); // Split the string into latitude and longitude
        if (locationParts.length === 2) {
            const latitude = parseFloat(locationParts[0].trim());
            const longitude = parseFloat(locationParts[1].trim());

            // Ensure valid coordinates
            if (!isNaN(latitude) && !isNaN(longitude)) {
                defaultCoordinates = [latitude, longitude];
            }
        }
    }

    // Initialize the map with the default or driver's coordinates
    window.map = L.map('map').setView(defaultCoordinates, 12); // Center on default location


    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(window.map);

    // Add a marker for the driver's location by default
    L.marker(defaultCoordinates)
        .addTo(window.map)
        .bindPopup("Driver's Current Location")
        .openPopup();


    // Function to update the location on the map
    function updateLocationOnMap(coordinates, locationName) {
        // Ensure valid coordinates
        if (!coordinates || coordinates.length !== 2) {
            console.error('Invalid coordinates:', coordinates);
            return;
        }

        // Store the identified latitude and longitude
        identifiedLatitude = coordinates[0];
        identifiedLongitude = coordinates[1];

        // Remove previous marker if exists
        if (currentLocationMarker) {
            window.map.removeLayer(currentLocationMarker);
        }

        // Add a new marker at the provided coordinates
        currentLocationMarker = L.marker(coordinates)
            .addTo(window.map)
            .bindPopup(locationName || 'Selected Location')
            .openPopup();

        // Center the map on the new location
        window.map.setView(coordinates, 12);
    }


    // Function to get the user's current location using TomTom API
    function useTomTomLocation() {
        if (navigator.geolocation) {
            // Request high accuracy for geolocation
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    var lat = position.coords.latitude;
                    var lon = position.coords.longitude;
                    var userLocation = [lat, lon];

                    // Reverse geocoding to get the location name from coordinates using TomTom API
                    fetch(`https://api.tomtom.com/search/2/reverseGeocode/${lat},${lon}.json?key=${tomTomApiKey}&language=en-GB`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.addresses && data.addresses.length > 0) {
                                var locationName = data.addresses[0].address.freeformAddress;
                                document.getElementById('startLocation').value = locationName;

                                // Update the map with the user's current location
                                updateLocationOnMap(userLocation, locationName);
                            } else {
                                alert('Could not fetch location name');
                            }
                        })
                        .catch(error => {
                            alert('Error fetching location name: ' + error);
                        });
                },
                function(error) {
                    // Handle different geolocation errors
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            alert("You have denied access to your location. Please enable it to use this feature.");
                            break;
                        case error.POSITION_UNAVAILABLE:
                            alert("Location information is unavailable.");
                            break;
                        case error.TIMEOUT:
                            alert("The request to get your location timed out.");
                            break;
                        case error.UNKNOWN_ERROR:
                            alert("An unknown error occurred.");
                            break;
                    }
                },
                {
                    enableHighAccuracy: true, // Use high accuracy for better results
                    timeout: 10000,           // Timeout after 10 seconds
                    maximumAge: 0             // No cached position
                }
            );
        } else {
            alert('Geolocation is not supported by this browser.');
        }
    }


    // Function to get coordinates using OpenCage API
    function getCoordinates(locationName, callback) {
        fetch(`https://api.opencagedata.com/geocode/v1/json?q=${encodeURIComponent(locationName)}&key=${openCageApiKey}&countrycode=LK`)
            .then(response => response.json())
            .then(data => {
                if (data.results && data.results.length > 0) {
                    var coords = data.results[0].geometry;
                    callback([coords.lat, coords.lng]);
                } else {
                    alert('Location not found: ' + locationName);
                }
            })
            .catch(error => {
                alert('Error fetching coordinates: ' + error);
            });
    }


    // Autocomplete location suggestions
    function autocompleteLocation(inputId, listId) {
        document.getElementById(inputId).addEventListener('input', function() {
            var query = this.value;
            if (query.length > 2) {
                // Query TomTom API for suggestions
                fetch(`https://api.tomtom.com/search/2/search/${encodeURIComponent(query)}.json?key=${tomTomApiKey}&limit=5&countrySet=LKA&language=en-GB&typeahead=true&idxSet=Geo,POI`)
                    .then(response => response.json())
                    .then(data => {
                        var list = document.getElementById(listId);
                        list.innerHTML = ''; // Clear previous suggestions
                        if (data.results && data.results.length > 0) {
                            data.results.forEach(result => {
                                var li = document.createElement('li');
                                li.textContent = result.address.freeformAddress || result.poi.name;
                                li.onclick = function() {
                                    document.getElementById(inputId).value = this.textContent;
                                    list.innerHTML = ''; // Clear suggestions after selection

                                    // Get the coordinates for the selected location and update the map
                                    getCoordinates(this.textContent, function(coords) {
                                        updateLocationOnMap(coords, this.textContent);
                                    });
                                };
                                list.appendChild(li);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching location suggestions:', error);
                    });
            }
        });
    }

    // Event listener for "Get Current Location" button
    document.getElementById('getCurrentLocationBtn').addEventListener('click', function() {
        useTomTomLocation();
    });

    // Event listener for "Update Location" button in the HTML
    document.getElementById('updateLocationBtn').addEventListener('click', function() {
        confirmLocationUpdate();
    });

    // Function to confirm location update with SweetAlert (v1.1.3 compatible)
    function confirmLocationUpdate() {
        swal({
            title: "Are you sure?",
            text: "Do you want to update your location?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: false
        }, function(isConfirm) {
            if (isConfirm) {
                // Call the function to update location via AJAX
                updateDriverLocation();
            }
        });
    }

    // Initialize autocomplete for start location
    autocompleteLocation('startLocation', 'startLocationList');

    // Function to update the driver's location via AJAX
    function updateDriverLocation() {
        // Ensure driverID is present
        if (!driverID) {
            alert('Driver ID not found. Unable to update location.');
            return;
        }

        // Send the request via AJAX
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "/CityTaxi/Functions/Driver/Driver.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    swal({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        button: "OK"
                    });

                    // After success, update the map and location element
                    updateMapAndLocationElement(identifiedLatitude, identifiedLongitude);
                } else {
                    swal({
                        title: "Error!",
                        text: response.message,
                        icon: "error",
                        button: "OK"
                    });
                }
            }
        };

        // Send the driver ID and coordinates (without brackets) to the server
        xhr.send(`action=updateLocation&driverId=${driverID}&latitude=${identifiedLatitude}&longitude=${identifiedLongitude}`);
    }
    

    // Function to update the map and the driverLocation element after updating the location
    function updateMapAndLocationElement(latitude, longitude) {
        // Update the location in the span
        document.getElementById('driverLocation').textContent = `${latitude}, ${longitude}`;

        // Refresh the map marker with the new location
        const newCoordinates = [latitude, longitude];

        // Remove the existing marker and add a new one
        if (currentLocationMarker) {
            window.map.removeLayer(currentLocationMarker);
        }

        currentLocationMarker = L.marker(newCoordinates)
            .addTo(window.map)
            .bindPopup("Updated Driver's Location")
            .openPopup();

        // Re-center the map on the new location
        window.map.setView(newCoordinates, 12);
    }


    const finishRideButtons = document.querySelectorAll('.finish-ride');
    finishRideButtons.forEach(button => {
        button.addEventListener('click', function() {
            const rideID = this.getAttribute('data-ride-id');
            const passengerID = this.getAttribute('data-passenger-id');
            const totalAmount = this.getAttribute('data-amount');  // Get the amount from the button
            const taxiID = this.getAttribute('data-taxi-id');
            const driverID = this.getAttribute('data-driver-id');

            console.log('Finish ride clicked. RideID:', rideID, 'PassengerID:', passengerID, 'Amount:', totalAmount, 'TaxiID:', taxiID, 'Driver ID:',driverID );


            if (!driverID || !passengerID || !rideID || !totalAmount || !taxiID) {
                console.error('Missing driverID, passengerID, rideID, totalAmount, or taxiID.');
                return;
            }

            swal({
                title: "Are you sure?",
                text: "Do you want to finish this ride?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {
                    socket.send(JSON.stringify({
                        action: 'finishRide',
                        rideID: rideID,
                        driverID: driverID,
                        passengerID: passengerID,
                        totalAmount: totalAmount
                    }));

                    swal("Waiting", "Waiting for the passenger to proceed.", "info");

                   // Listen for the passenger's payment method response and other actions
                    socket.onmessage = function(event) {
                        const response = JSON.parse(event.data);
                        console.log('Driver received message:', response);

                        if (response.action === 'passengerPaymentMethod' && response.rideID === rideID) {
                            if (response.paymentMethod === 'cash') {
                                swal({
                                    title: "Cash Payment",
                                    text: "Please confirm the correct amount of cash has been received.",
                                    type: "warning",
                                    showCancelButton: true,
                                    confirmButtonText: "Yes, correct",
                                    cancelButtonText: "No, incorrect",
                                    closeOnConfirm: false,
                                    closeOnCancel: false
                                }, function(confirmed) {
                                    if (confirmed) {
                                        // Call the new method to finish the ride and update the ride table
                                        finishRideAndUpdateTable(rideID, driverID, totalAmount, taxiID, driverName, passengerID);

                                        swal("Success", "Cash payment has been confirmed.", "success");
                                    } else {
                                        // Send a message to the passenger that the cash amount is incorrect
                                        socket.send(JSON.stringify({
                                            action: 'driverCashConfirmation',
                                            confirmed: false, // Set confirmed to false
                                            rideID: rideID,
                                            passengerID: passengerID,
                                            totalAmount: totalAmount
                                        }));

                                        swal("Waiting", "Notified passenger to recheck the cash amount.", "info");
                                    }
                                });
                            } else if (response.paymentMethod === 'online') {
                                swal("Online Payment", "The passenger is proceeding with online payment.", "info");
                            }
                        } else if (response.action === 'passengerRecheckedCash' && response.rideID === rideID) {
                            // Handle passenger rechecking the cash
                            console.log("Received 'passengerRecheckedCash' message.");
                            swal({
                                title: "Cash Re-checked",
                                text: "The passenger has rechecked the cash amount. Do you confirm?",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonText: "Yes, correct",
                                cancelButtonText: "No, incorrect",
                                closeOnConfirm: false,
                                closeOnCancel: false
                            }, function(confirmed) {
                            if (confirmed) {
                                // Call the method to finish the ride and update the ride table
                                finishRideAndUpdateTable(rideID, driverID, totalAmount, taxiID, driverName, passengerID);

                                // Notify the WebSocket server of cash payment success
                                socket.send(JSON.stringify({
                                    action: 'cashPaymentSuccess',  // New action for cash payment success
                                    rideID: rideID,
                                    passengerID: passengerID,
                                    driverID: driverID,
                                    totalAmount: totalAmount
                                }));

                                swal("Success", "Cash payment has been confirmed.", "success");
                            } else {
                                // Send a message to the passenger that the cash amount is incorrect
                                socket.send(JSON.stringify({
                                    action: 'driverCashConfirmation',
                                    confirmed: false, // Set confirmed to false
                                    rideID: rideID,
                                    passengerID: passengerID,
                                    totalAmount: totalAmount
                                }));

                                swal("Please ask the passenger to pay online.", "info");
                            }
                        });
                        } else if (response.action === 'passengerOnlinePaymentSuccess' && response.rideID === rideID) {

                            finishRideAndUpdateTable(rideID, driverID, totalAmount, taxiID, driverName, passengerID);
                            swal({
                                title: "Payment Completed",
                                text: "The passenger has successfully completed the online payment.",
                                type: "success",
                                confirmButtonText: "OK"
                            });//, function() {
                            //     // Call the function to finish the ride and update the ride table
                            //     finishRideAndUpdateTable(rideID, driverID, totalAmount, taxiID, driverName, passengerID);
                            // }
                        }
                    };

                }
            });
        });
    });


    // Method to finish the ride, update the ride table, insert into payments and invoices table
    function finishRideAndUpdateTable(rideID, driverID, amount, taxiID, driverName, passengerID) {
        // Get the current date and time
        const now = new Date();
        const endDate = now.toISOString().split('T')[0]; // Format as YYYY-MM-DD
        const endTime = now.toTimeString().split(' ')[0]; // Format as HH:MM:SS

        // Check if all required data is present before proceeding
        if (!rideID || !driverID || !amount || !taxiID || !driverName || !passengerID) {
            console.error('Missing required data to finish the ride.');
            return;
        }

        // Send AJAX request to finish the ride, update the ride and insert into payments and invoices
        fetch('/CityTaxi/Functions/Common/Rides.php', {  // Updated path
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'finishRide',
                rideID: rideID,
                driverID: driverID,
                endDate: endDate,
                endTime: endTime,
                amount: amount,        // Send the amount for the payment
                taxiID: taxiID,        // Send the taxiID for the payment
                driverName: driverName, // Send the driver name
                passengerID: passengerID // Send the passenger ID
            })
        })
        .then(response => response.text())  // Get the full response as text for debugging
        .then(text => {
            try {
                const data = JSON.parse(text);  // Try to parse the text into JSON
                if (data.success) {
                    console.log('Ride, payment, and invoice processed successfully:', data);
                    swal("Success", "Ride has been completed and payment recorded.", "success");
                } else {
                    console.error('Error in processing ride, payment, and invoice:', data.message);
                    swal("Error", "There was an issue processing the ride. Please try again.", "error");
                }
            } catch (err) {
                console.error('Response is not valid JSON:', text);  // Log the entire response for debugging
                swal("Error", "An unexpected error occurred. Please try again.", "error");
            }
        })
        .catch(error => {
            console.error('Error finishing ride, payment, and invoice:', error);
            swal("Error", "An error occurred while processing the ride. Please try again.", "error");
        });
    }




    socket.onmessage = function(event) {
        const response = JSON.parse(event.data);
        console.log('Received response:', response);

        if (response.status === 'rideOffer') {
            sweetAlert({
                title: "New Ride Available",
                text: response.message,
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Accept",
                cancelButtonText: "Reject",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm){
                if (isConfirm) {
                    socket.send(JSON.stringify({
                        action: 'acceptRide',
                        driverID: driverID,
                        driverName: driverName,
                        driverLocation: driverLocation,
                        driverMobile: driverMobile,
                        rideDetails: response.rideDetails
                    }));
                    sweetAlert("Accepted!", "You have accepted the ride.", "success");

                    // Update availability status
                    document.getElementById('driverAvailability').innerText = "Unavailable"; // Update availability status
                } else {
                    socket.send(JSON.stringify({
                        action: 'rejectRide',
                        driverID: driverID,
                        rideDetails: response.rideDetails
                    }));
                    sweetAlert("Rejected", "You have rejected the ride.", "info");
                }
            });
        } else if (response.status === 'confirmed') {
            sweetAlert("Booking Confirmed!", response.message, "success");
        } else if (response.status === 'rejected') {
            sweetAlert("Ride Rejected", "The passenger has been notified.", "info");
        } else if (response.status === 'availabilityUpdate') {
            // Update the availability status based on the received message
            document.getElementById('driverAvailability').innerText = response.availability === 1 ? "Available" : "Unavailable";
        }
    };

    socket.onerror = function(error) {
        console.error('WebSocket Error:', error);
    };

    socket.onclose = function(event) {
        if (event.wasClean) {
            console.log(`WebSocket closed cleanly, code=${event.code}, reason=${event.reason}`);
        } else {
            console.error('WebSocket connection died');
        }
    };

    document.getElementById('changeAvailabilityBtn').addEventListener('click', function() {
        const driverId = this.getAttribute('data-driver-id');
        
        // Use AJAX to change availability
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "/CityTaxi/Functions/Driver/Driver.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                const response = JSON.parse(xhr.responseText);
                
                if (response.status === 'success') {
                    // Update availability status in the DOM
                    const newAvailabilityText = response.newAvailability == 1 ? 'Available' : 'Unavailable';
                    document.getElementById('driverAvailabilityStatus').textContent = newAvailabilityText;

                    // Show SweetAlert for success
                    swal({
                        title: "Success!",
                        text: "Driver availability updated to " + newAvailabilityText,
                        icon: "success",
                        button: "OK"
                    });
                } else {
                    // Show SweetAlert for error with custom message
                    swal({
                        title: "Error!",
                        text: response.message, // Display the custom error message
                        icon: "error",
                        button: "OK"
                    });
                }
            }
        };
        xhr.send(`action=changeAvailability&driverId=${driverId}`);
    });
});
