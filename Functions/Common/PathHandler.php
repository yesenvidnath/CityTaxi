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
// DIsplay the CSS if the page equal to profile
function showLinkOnMyRides($link) {
    // Get the current page's filename
    $currentPage = basename($_SERVER['PHP_SELF']);

    // Check if the current page is 'profile.php'
    if ($currentPage === 'MyRides.php') {
        // Echo the link tag
        echo $link;
    }
}

?>
