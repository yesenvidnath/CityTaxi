<!-- Main Content -->
<div class="container-fluid mt-3">
    <div class="row">
        <h1 class="ml-3">Admin Dashboard</h1>
    </div>
</div>

<!-- Cards for Display Basic Details -->
<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-primary text-white mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text">500</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark mb-3">
                <div class="card-body">
                    <h5 class="card-title">Transactions</h5>
                    <p class="card-text">$15,000</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
           <div class="card bg-success text-white mb-3">
                <div class="card-body">
                    <h5 class="card-title">Reports</h5>
                    <p class="card-text">130 Reports Generated</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cards for Graphs -->
<div class="container-fluid mt-3">
    <div class="row">
        <!-- Usage Statistics Card -->
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Usage Statistics</h5>
                    <canvas id="usageChart"></canvas>
                </div>
            </div>
        </div>
        <!-- Driver Performance Card -->
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Driver Performance</h5>
                    <canvas id="driverPerformanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var ctxUsage = document.getElementById('usageChart').getContext('2d');
    var usageChart;

    // Fetch ride statistics and update chart
    fetch('/CityTaxi/Functions/Chart/getRideStatistics.php')
        .then(response => response.json())
        .then(data => {
            var months = data.map(item => item.Month);
            var totalRides = data.map(item => item.TotalRides);

            usageChart = new Chart(ctxUsage, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Total Rides',
                        data: totalRides,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
        .catch(error => console.error('Error fetching ride statistics:', error));

    var ctxPerformance = document.getElementById('driverPerformanceChart').getContext('2d');
    var driverPerformanceChart;

    // Fetch driver performance and update chart
    fetch('/CityTaxi/Functions/Chart/getDriverPerformance.php')
        .then(response => response.json())
        .then(data => {
            var drivers = data.map(item => item.First_name);
            var avgRatings = data.map(item => item.AvgRating);

            driverPerformanceChart = new Chart(ctxPerformance, {
                type: 'bar',
                data: {
                    labels: drivers,
                    datasets: [{
                        label: 'Average Rating',
                        data: avgRatings,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
        .catch(error => console.error('Error fetching driver performance:', error));
});
</script>
