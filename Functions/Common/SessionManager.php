<?php
class SessionManager {
    // Start the session
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Set session data
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    // Get session data
    public static function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    // Destroy session (logout)
    public static function logout() {
        session_destroy();
    }
}
