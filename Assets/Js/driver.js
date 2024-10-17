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

});
