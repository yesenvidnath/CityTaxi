// Initialize the map
var map = L.map('map').setView([0, 0], 2); // Initial zoomed-out view

// Set up the OpenStreetMap layer
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

var routeLayer; // To store the route line

// Function to get coordinates using OpenCage API
function getCoordinates(locationName, callback) {
    fetch(`https://api.opencagedata.com/geocode/v1/json?q=${encodeURIComponent(locationName)}&key=${openCageApiKey}`)
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
                    routeLayer = L.polyline(routeCoords, {color: 'blue'}).addTo(map);

                    // Add markers
                    L.marker(startCoords).addTo(map).bindPopup('Start Location');
                    L.marker(endCoords).addTo(map).bindPopup('End Location');

                    // Fit the map to show the entire route
                    map.fitBounds(routeLayer.getBounds());

                    // Display route details
                    var distance = routeData.features[0].properties.segments[0].distance / 1000; // Convert to km
                    var duration = routeData.features[0].properties.segments[0].duration / 60; // Convert to minutes

                    document.getElementById('routeDetails').innerHTML = `
                        Total Distance: ${distance.toFixed(2)} km<br>
                        Estimated Travel Time: ${duration.toFixed(2)} minutes
                    `;
                })
                .catch(error => {
                    alert('Error fetching route: ' + error);
                });
        });
    });
}
