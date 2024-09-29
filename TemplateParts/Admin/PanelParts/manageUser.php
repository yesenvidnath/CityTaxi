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
        <a href="addUser.php" class="btn btn-primary mb-3">Add New User</a>
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
                        <a href="editUser.php?edit=<?= $user['user_ID'] ?>" class="btn btn-info">Edit</a>
                        <a href="?delete=<?= $user['user_ID'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>