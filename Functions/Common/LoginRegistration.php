<?php
session_start();
include 'Database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate email and password fields
    if (empty($email) || empty($password)) {
        header("Location: /CityTaxi/login.php?status=error&message=Please enter both email and password.");
        exit();
    }

    try {
        $db = new Database();
        $conn = $db->getConnection();

        $sql = "SELECT * FROM Users WHERE Email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($password === $user['password']) {
                $_SESSION['user_ID'] = $user['user_ID'];
                $_SESSION['user_type'] = $user['user_type'];

                // Redirect to the login page with success status
                header("Location: /CityTaxi/login.php?status=success&message=Login successful!");
                exit();
            } else {
                header("Location: /CityTaxi/login.php?status=error&message=Invalid password!");
                exit();
            }
        } else {
            header("Location: /CityTaxi/login.php?status=error&message=Email not found!");
            exit();
        }

        $db->close();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
