<?php
include_once '../../Functions/Common/Database.php';
include_once '../../Functions/Common/Taxi.php';

$taxi = new Taxi();
$taxiList = $taxi->fetchAllTaxis();

?>

<!-- Main Content -->
<div class="container mt-3">
    <div class="row">
        <h2>Manage Vehicles</h2>
    </div>
</div>

<!-- Content here -->
<div class="container mt-3">
    <div class="row">
        <a href="addVehicle.php" class="btn btn-primary mb-3">Add New Vehicle</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Plate Number</th>
                    <th>Registration Date</th>
                    <th>Revenue Licence</th>
                    <th>Insurance Info</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($taxiList as $taxi): ?>
                <tr>
                    <td><?= htmlspecialchars($taxi['Taxi_ID']) ?></td>
                    <td><?= htmlspecialchars($taxi['Taxi_type']) ?></td>
                    <td><?= htmlspecialchars($taxi['Plate_number']) ?></td>
                    <td><?= htmlspecialchars($taxi['Registration_Date']) ?></td>
                    <td><?= htmlspecialchars($taxi['RevenueLicence']) ?></td>
                    <td><?= htmlspecialchars($taxi['Insurance_info']) ?></td>
                    <td>
                        <a href="editVehicle.php?edit=<?= $taxi['Taxi_ID'] ?>" class="btn btn-info">Edit</a>
                        <a href="?delete=<?= $taxi['Taxi_ID'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this vehicle?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
