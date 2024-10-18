<?php
include_once '../../Functions/Common/Database.php';
include_once '../../Functions/Common/Rides.php';

$ride = new Ride();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    // Assume searchRides returns an array of rides based on search criteria
    $rideList = $ride->searchRides($_POST);
} else {
    $rideList = $ride->fetchAllRides();
}

if (isset($_GET['delete'])) {
    $ride->deleteRide($_GET['delete']);
    header("Location: manageRide.php"); // Redirect to avoid re-submission
}

// Optional: handle status updates
if (isset($_POST['update_status'])) {
    $ride->updateRideStatus($_POST['ride_id'], $_POST['new_status']);
}
?>

<!-- Main Content -->
<div class="container mt-3">
    <div class="row">
        <h2>Manage Rides</h2>
    </div>
</div>

<!-- Content here -->
<div class="container mt-3">
    <div class="row">
        <div class="col-md-2">
            <a href="addRide.php" class="btn btn-primary mb-3">Add New Ride</a>
        </div>
        <div class="col-md-10">
            <form method="post" action="manageRide.php" class="form-inline justify-content-end mt-3">
                <div class="input-group" style="width: 100%;">
                    <input type="text" class="form-control" name="search_criteria" placeholder="Search..." aria-label="Search Rides" style="width: 50%;">
                    <select class="custom-select" name="status">
                        <option value="All">All</option>
                        <option value="Accepted">Accepted</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-outline-secondary" name="search">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Start Location</th>
                    <th>End Location</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Distance</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rideList as $ride): ?>
                <tr>
                    <td><?= htmlspecialchars($ride['Ride_ID']) ?></td>
                    <td><?= htmlspecialchars($ride['Type']) ?></td>
                    <td><?= htmlspecialchars($ride['Start_Location']) ?></td>
                    <td><?= htmlspecialchars($ride['End_Location']) ?></td>
                    <td><?= htmlspecialchars($ride['Start_time']) ?></td>
                    <td><?= htmlspecialchars($ride['End_time']) ?></td>
                    <td><?= htmlspecialchars($ride['Total_distance']) ?></td>
                    <td><?= htmlspecialchars($ride['Amount']) ?></td>
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="ride_id" value="<?= $ride['Ride_ID'] ?>">
                            <select class="custom-select" style="width: 50%;" name="new_status">
                                <option value="Accepted">Accepted</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                            <button type="submit" class="btn btn-outline-secondary" name="update_status">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
