// Initialize the map using TomTom's base layer
var map = L.map('map').setView([7.8731, 80.7718], 7); // Centered on Sri Lanka

// Global variable to store the total distance of the route
var totalDistance = 0; 

// Set up the TomTom layer with the language set to English (en-GB)
L.tileLayer(`https://api.tomtom.com/map/1/tile/basic/main/{z}/{x}/{y}.png?key=${tomTomApiKey}&language=en-GB`, {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.tomtom.com/copyright">TomTom</a>'
}).addTo(map);

// Set the bounds for Sri Lanka to restrict map panning and zooming
var sriLankaBounds = L.latLngBounds(
    L.latLng(5.91667, 79.6527), // Southwest corner
    L.latLng(9.83333, 81.8812)  // Northeast corner
);
map.setMaxBounds(sriLankaBounds);
map.on('drag', function() {
    map.panInsideBounds(sriLankaBounds, { animate: false });
});

var routeLayer; // To store the route line
var currentLocationMarker; // To store the current location marker

// Autocomplete Suggestions for Location Input Fields (limited to Sri Lanka and includes places like landmarks)
function autocompleteLocation(inputId, listId) {
    document.getElementById(inputId).addEventListener('input', function() {
        var query = this.value;
        if (query.length > 2) {
            // Query TomTom API for both address and POI (Point of Interest) searches, limited to Sri Lanka
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

// Initialize autocomplete for both start and end locations
autocompleteLocation('startLocation', 'startLocationList');
autocompleteLocation('endLocation', 'endLocationList');

// Function to get coordinates using OpenCage API (can also limit to Sri Lanka)
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

// Function to show the route on the map
function showRoute() {
    var startLocation = document.getElementById('startLocation').value;
    var endLocation = document.getElementById('endLocation').value;

    // Get coordinates for both locations
    getCoordinates(startLocation, function(startCoords) {
        getCoordinates(endLocation, function(endCoords) {
            // Request the route from OpenRouteService API
            fetch(`https://api.openrouteservice.org/v2/directions/driving-car?api_key=${openRouteServiceApiKey}&start=${startCoords[1]},${startCoords[0]}&end=${endCoords[1]},${endCoords[0]}`)
                .then(response => response.json())
                .then(routeData => {
                    if (routeLayer) {
                        map.removeLayer(routeLayer);
                    }

                    // Get the coordinates for the route
                    var routeCoords = routeData.features[0].geometry.coordinates.map(function(coord) {
                        return [coord[1], coord[0]];
                    });

                    // Draw the route on the map
                    routeLayer = L.polyline(routeCoords, { color: 'blue' }).addTo(map);

                    // Add markers
                    L.marker(startCoords).addTo(map).bindPopup('Start Location');
                    L.marker(endCoords).addTo(map).bindPopup('End Location');

                    // Fit the map to show the entire route
                    map.fitBounds(routeLayer.getBounds());

                    // Store the total distance (in kilometers)
                    totalDistance = routeData.features[0].properties.segments[0].distance / 1000;

                    // Display route details
                    document.getElementById('routeDetails').innerHTML = `
                        Total Distance: ${totalDistance.toFixed(2)} km<br>
                        Estimated Travel Time: ${routeData.features[0].properties.segments[0].duration / 60} minutes
                    `;

                    // Show confirm button for route
                    document.getElementById('confirmRouteBtn').style.display = 'inline-block';
                })
                .catch(error => {
                    alert('Error fetching route: ' + error);
                });
        });
    });
}


// Function to get the user's current location using TomTom API
function useTomTomLocation() {
    if (navigator.geolocation) {
        // Request high accuracy
        navigator.geolocation.getCurrentPosition(function(position) {
            var lat = position.coords.latitude;
            var lon = position.coords.longitude;
            var userLocation = [lat, lon];

            // Reverse geocoding to get the location name from coordinates using TomTom API with language set to English (en-GB)
            fetch(`https://api.tomtom.com/search/2/reverseGeocode/${lat},${lon}.json?key=${tomTomApiKey}&language=en-GB`)
                .then(response => response.json())
                .then(data => {
                    if (data.addresses && data.addresses.length > 0) {
                        var locationName = data.addresses[0].address.freeformAddress;
                        document.getElementById('startLocation').value = locationName;

                        // Add marker for current location
                        if (currentLocationMarker) {
                            map.removeLayer(currentLocationMarker);
                        }
                        currentLocationMarker = L.marker(userLocation)
                            .addTo(map)
                            .bindPopup('Your Current Location')
                            .openPopup();

                        map.setView(userLocation, 12); // Center the map on the current location
                    } else {
                        alert('Could not fetch location name');
                    }
                })
                .catch(error => {
                    alert('Error fetching location name: ' + error);
                });
        }, function(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    alert("User denied the request for Geolocation.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert("Location information is unavailable.");
                    break;
                case error.TIMEOUT:
                    alert("The request to get user location timed out.");
                    break;
                case error.UNKNOWN_ERROR:
                    alert("An unknown error occurred.");
                    break;
            }
        }, {
            enableHighAccuracy: true, // Enable high accuracy
            timeout: 10000,           // 10 seconds timeout
            maximumAge: 0             // No cached position
        });
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}


function selectTaxiType(taxiType) {
    swal("Taxi Selected", `You have selected ${taxiType}.`, "success");
}

// Keep the route layer global so it's not removed when switching sections
var routeLayer = null; 
var currentLocationMarker = null;

// Call this function after the route is confirmed to update prices
function confirmRoute() {
    swal({
        title: "Confirm Route",
        text: "Are you sure you want to confirm this route?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, confirm it!",
        closeOnConfirm: false
    }, function() {
        swal("Confirmed!", "Route has been confirmed.", "success");
        document.getElementById('step1').style.display = 'none';
        document.getElementById('step2').style.display = 'block';
        updateTaxiPrices(); // Update the prices after route confirmation
    });
}

//Function to confirm changing the route
function confirmChangeRoute() {
    swal({
        title: "Change Route",
        text: "Are you sure you want to change the route?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, change it!",
        closeOnConfirm: false
    }, function() {
        swal("Route Changed!", "You can now select a new route.", "success");
        document.getElementById('step2').style.display = 'none';
        document.getElementById('step1').style.display = 'block';
    });
}