<?php 
// Include the header part
include '../../Header/header.php'; 
?>

<!-- Side Navigation Bar -->
<div class="container-fluid">
    <div class="row flex-nowrap">
        <!-- Sidebar -->
        <div class="bg-dark col-auto col-md-2 min-vh-100">
            <div class="bg-dark">
                <ul class="nav nav-pills flex-column mt-4">
                    <li class="nav-item">
                        <a class="nav-link text-white mb-4" href="#">
                            <i class="fa-solid fa-gauge mr-3"></i><span class="ms-3 d-none d-sm-inline">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white mb-4" href="#">
                            <i class="fas fa-user mr-3"></i><span class="ms-3 d-none d-sm-inline">Manage User</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white mb-4" href="#">
                            <i class="fas fa-car mr-3"></i><span class="ms-3 d-none d-sm-inline">Manage Vehicle</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white mb-4" href="#">
                            <i class="fas fa-road mr-3"></i><span class="ms-3 d-none d-sm-inline">Manage Ride</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white mb-4" href="#">
                            <i class="fas fa-money-bill-alt mr-3"></i><span class="ms-3 d-none d-sm-inline">Financials</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white mb-4" href="#">
                            <i class="fas fa-star mr-3"></i><span class="ms-3 d-none d-sm-inline">Ratings & Feedback</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white mb-4" href="#">
                            <i class="fas fa-chart-bar mr-3"></i><span class="ms-3 d-none d-sm-inline">Analytics</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Main Content -->
        <div class="col-md-10">
            <h1 class="mt-3">Admin Dashboard</h1>
            <!-- Content here -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-primary text-white mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Total Users</h5>
                            <p class="card-text">500</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-dark mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Transactions</h5>
                            <p class="card-text">$15,000</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Reports</h5>
                            <p class="card-text">130 Reports Generated</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>