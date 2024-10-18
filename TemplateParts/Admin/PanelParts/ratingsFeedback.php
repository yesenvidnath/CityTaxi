<?php
include_once '../../Functions/Common/Database.php';
include_once '../../Functions/Common/Ratings.php';

$ratings = new Ratings();
$ratingsList = $ratings->fetchAllRatings();

?>

<!-- Main Content -->
<div class="container mt-3">
    <div class="row">
        <h2>Ratings and Feedback</h2>
    </div>
</div>

<!-- Content here -->
<div class="container mt-3">
    <div class="row">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Passenger</th>
                    <th>Driver</th>
                    <th>Rate</th>
                    <th>Comment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ratingsList as $rating): ?>
                <tr>
                    <td><?= htmlspecialchars($rating['Rating_ID']) ?></td>
                    <td><?= htmlspecialchars($rating['PassengerFirstName'] . ' ' . $rating['PassengerLastName']) ?></td>
                    <td><?= htmlspecialchars($rating['DriverFirstName'] . ' ' . $rating['DriverLastName']) ?></td>
                    <td><?= htmlspecialchars($rating['Rate']) ?></td>
                    <td><?= htmlspecialchars($rating['Comment']) ?></td>
                    <td>
                        <!-- Placeholder for action buttons -->
                        <button class="btn btn-info" onclick="addressFeedback(<?= $rating['Rating_ID'] ?>);">Address</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function addressFeedback(ratingId) {
        // JavaScript function to handle feedback addressing
        // Placeholder for modal popup or redirect to a feedback response page
        console.log("Address Feedback for Rating ID:", ratingId);
    }
</script>