<?php
include_once 'Functions/Common/Users.php';

$users = new Users();
$userList = $users->fetchAllUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link rel="stylesheet" href="Assets/Css/style.css">
</head>
<body>
    <div class="container">
        <h1>User List</h1>
        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($userList as $user): ?>
                    <tr>
                        <td><?php echo $user['user_ID']; ?></td>
                        <td><?php echo $user['First_name']; ?></td>
                        <td><?php echo $user['Last_name']; ?></td>
                        <td><?php echo $user['Email']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
