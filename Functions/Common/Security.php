<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/Functions/Common/Database.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

class Security {
    private $db;
    private $conn;


    // Method to add JavaScript for preventing users from inspecting the website
    public function preventInspect() {
        echo '
            <script>
                // Disable right-click
                document.addEventListener("contextmenu", function(e) {
                    e.preventDefault();
                });

                // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
                document.addEventListener("keydown", function(e) {
                    if (e.keyCode == 123 || // F12
                        (e.ctrlKey && e.shiftKey && e.keyCode == 73) || // Ctrl+Shift+I
                        (e.ctrlKey && e.shiftKey && e.keyCode == 74) || // Ctrl+Shift+J
                        (e.ctrlKey && e.keyCode == 85)) { // Ctrl+U
                        e.preventDefault();
                        return false;
                    }
                });
            </script>
        ';
    }
}
?>
