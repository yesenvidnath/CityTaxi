<?php


// DIsplay the CSS if the page equal to profile
function showLinkOnProfilePage($link) {
    // Get the current page's filename
    $currentPage = basename($_SERVER['PHP_SELF']);

    // Check if the current page is 'profile.php'
    if ($currentPage === 'profile.php') {
        // Echo the link tag
        echo $link;
    }
}

// DIsplay the CSS if the page equal to MyRides
function showLinkOnMyRides($link) {
    // Get the current page's filename
    $currentPage = basename($_SERVER['PHP_SELF']);

    // Check if the current page is 'MyRides.php'
    if ($currentPage === 'MyRides.php') {
        // Echo the link tag
        echo $link;
    }
}


// DIsplay the CSS if the page equal to ride
function showLinkOnRide($link) {
    // Get the current page's filename
    $currentPage = basename($_SERVER['PHP_SELF']);

    // Check if the current page is 'ride.php'
    if ($currentPage === 'ride.php') {
        // Echo the link tag
        echo $link;
    }
}

// DIsplay the CSS if the page equal to dashboard
function showLinkOnDashboard($link) {
    // Get the current page's filename
    $currentPage = basename($_SERVER['PHP_SELF']);

    // Check if the current page is 'AdminDashboard.php'
    if ($currentPage === 'Pages/Admin/AdminDashboard.php') {
        // Echo the link tag
        echo $link;
    }
}

// DIsplay the CSS if the page equal to register
function showLinkOnRegister($link) {
    // Get the current page's filename
    $currentPage = basename($_SERVER['PHP_SELF']);

    // Check if the current page is 'register.php'
    if ($currentPage === 'register.php') {
        // Echo the link tag
        echo $link;
    }
}

// DIsplay the CSS if the page equal to profile
function showLinkOnDriverProfilePage($link) {
    // Get the current page's filename
    $currentPage = basename($_SERVER['PHP_SELF']);

    // Check if the current page is 'profile.php'
    if ($currentPage === 'profile.php') {
        // Echo the link tag
        echo $link;
    }
}

function showLinkOnIndexPage($link) {
    // Get the current page's filename
    $currentPage = basename($_SERVER['PHP_SELF']);

    // Check if the current page is 'profile.php'
    if ($currentPage === 'index.php') {
        // Echo the link tag
        echo $link;
    }
}

function showLinkOnLoginPage($link) {
    // Get the current page's filename
    $currentPage = basename($_SERVER['PHP_SELF']);

    // Check if the current page is 'profile.php'
    if ($currentPage === 'login.php') {
        // Echo the link tag
        echo $link;
    }
}
?>
