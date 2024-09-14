<?php
session_start();
include 'Database.php';
include 'SessionManager.php';
include 'Users.php'; // Include the Users class

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate email and password fields
    if (empty($email) || empty($password)) {
        header("Location: /CityTaxi/login.php?status=error&message=Please enter both email and password.");
        exit();
    }

    try {
        // Create an instance of the Users class and call fetchUserByEmail
        $users = new Users();
        $user = $users->fetchUserByEmail($email);

        if ($user) {
            if ($password === $user['password']) {
                // Store user details in the session
                SessionManager::startSession();
                SessionManager::set('user_ID', $user['user_ID']);
                SessionManager::set('user_type', $user['user_type']);
                SessionManager::set('first_name', $user['First_name']);
                SessionManager::set('last_name', $user['Last_name']);
                SessionManager::set('logged_in', true); // Mark the user as logged in

                // Redirect to the correct URL
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

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
