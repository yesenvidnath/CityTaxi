<?php

class HomeController
{
    public function index()
    {
        // You can pass any data to the view if necessary.
        $pageTitle = "Welcome to City Taxi";
        
        // Render the view (views/home/index.php)
        require_once('../app/views/home/index.php');
    }
}
