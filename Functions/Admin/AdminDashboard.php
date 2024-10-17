<?php 
// Include the header part
include 'TemplateParts/Header/header.php'; 
?>

<!-- Side Navigation Bar -->
<div class="container-fluid">
    <div class="row flex-nowrap">
        <!-- Sidebar -->
        <div class="bg-dark col-auto col-md-2 min-vh-100">
            <div class="bg-dark">
                <ul class="nav nav-pills flex-column mt-4">
                    <li class="nav-item">
                        <a class="nav-link text-white mb-4" href="?page=dashboard">
                            <i class="fa-solid fa-gauge mr-3"></i><span class="ms-3 d-none d-sm-inline">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white mb-4" href="?page=manageUser">
                            <i class="fas fa-user mr-3"></i><span class="ms-3 d-none d-sm-inline">Manage User</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white mb-4" href="?page=manageVehicle">
                            <i class="fas fa-car mr-3"></i><span class="ms-3 d-none d-sm-inline">Manage Vehicle</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white mb-4" href="?page=manageRide">
                            <i class="fas fa-road mr-3"></i><span class="ms-3 d-none d-sm-inline">Manage Ride</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white mb-4" href="?page=financials">
                            <i class="fas fa-money-bill-alt mr-3"></i><span class="ms-3 d-none d-sm-inline">Financials</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white mb-4" href="?page=ratingsFeedback">
                            <i class="fas fa-star mr-3"></i><span class="ms-3 d-none d-sm-inline">Ratings & Feedback</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white mb-4" href="?page=analytics">
                            <i class="fas fa-chart-bar mr-3"></i><span class="ms-3 d-none d-sm-inline">Analytics</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-md-10">
    <!-- Dynamic Content Loading Here -->
    <?php
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
        switch ($page) {
            case 'dashboard':
                include('TemplateParts/Admin/PanelParts/dashboard.php');
                break;
            case 'manageUser':
                include('TemplateParts/Admin/PanelParts/manageUser.php');
                break;
            case 'manageVehicle':
                include('TemplateParts/Admin/PanelParts/manageVehicle.php');
                break;
            case 'manageRide':
                include('TemplateParts/Admin/PanelParts/manageRide.php');
                break;
            case 'financials':
                include('TemplateParts/Admin/PanelParts/financials.php');
                break;
            case 'ratingsFeedback':
                include('TemplateParts/Admin/PanelParts/ratingsFeedback.php');
                break;
            case 'analytics':
                include('TemplateParts/Admin/PanelParts/analytics.php');
                break;
            default:
                include('TemplateParts/Admin/PanelParts/dashboard.php');  // default page
        }
    } else {
        include('dashboard.php');  // default page if no parameter is set
    }
    ?>
</div>
