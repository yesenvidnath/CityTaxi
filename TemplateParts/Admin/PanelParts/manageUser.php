<?php
include_once 'Functions/Common/Database.php';
include_once 'Functions/Common/Users.php';

$users = new Users();

// Handling deletion of a user
if (isset($_GET['delete'])) {
    $userID = $_GET['delete'];
    $deleteResult = $users->deleteUser($userID);
    if ($deleteResult) {
        echo "<script>alert('User deleted successfully!');</script>";
    } else {
        echo "<script>alert('Failed to delete user.');</script>";
    }
}

// Fetching all users
$userList = $users->fetchAllInfoFromUsers();

?>

<!-- Main Content -->
<div class="container mt-3">
    <div class="row">
        <h2>Manage Users</h2>
    </div>
</div>

<!-- Content here -->
<div class="container mt-3">
    <div class="row">
        <!-- Button to open modal in "add" mode -->
        <a href="javascript:void(0);" class="btn btn-primary mb-3" onclick="openModalForNewUser()">Add New User</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>NIC No</th>
                    <th>Mobile</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($userList as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['user_ID']) ?></td>
                    <td><?= htmlspecialchars($user['user_type']) ?></td>
                    <td><?= htmlspecialchars($user['First_name']) ?></td>
                    <td><?= htmlspecialchars($user['Last_name']) ?></td>
                    <td><?= htmlspecialchars($user['Email']) ?></td>
                    <td><?= htmlspecialchars($user['NIC_No']) ?></td>
                    <td><?= htmlspecialchars($user['mobile_number']) ?></td>
                    <td><?= htmlspecialchars($user['Address']) ?></td>
                    <td>
                        <a href="javascript:void(0);" class="btn btn-info" onclick="loadUserDetails('<?= $user['user_ID'] ?>')">Edit</a>
                        <a href="?delete=<?= $user['user_ID'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Update User Information Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" name="user_ID" id="modal_user_ID">
                    <div class="form-group">
                        <label for="modal_first_name">First Name</label>
                        <input type="text" class="form-control" id="modal_first_name" name="First_name" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_last_name">Last Name</label>
                        <input type="text" class="form-control" id="modal_last_name" name="Last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_email">Email</label>
                        <input type="email" class="form-control" id="modal_email" name="Email" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_nic_no">NIC No</label>
                        <input type="text" class="form-control" id="modal_nic_no" name="NIC_No" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_mobile_number">Mobile</label>
                        <input type="text" class="form-control" id="modal_mobile_number" name="mobile_number" required>
                    </div>
                    <div class="form-group">
                        <label for="modal_address">Address</label>
                        <input type="text" class="form-control" id="modal_address" name="Address" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveUserDetails()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Include Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Your custom JS file -->
<script src="Assets/Js/user.js"></script>
