<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="admin_assets/admin_style.css">
    <!--fonts-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <title>Administrator</title>
</head>
<body id="mainBody">

    <!--page wrapper-->
    <div class="wrap">

        <!--sidebar-->
        <ul class="sidebar" id="sidenav">
            <a href="#" class="sidebar-brand">
<!--                <div class="icon-rotate">-->
<!--                </div>-->
                <i  class="fa-solid fa-face-laugh-wink"></i>
                <div class="sidebar-text">Admin</div>
            </a>

            <!--divider-->
            <hr class="divider">

            <!--Nav item dashboard-->
            <li class="sidebar-item">
                <a href="#" class="sidebar-link">
                    <i class="fa fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#" class="sidebar-link">
                    <i class="fa fa-user-md"></i>
                    <span>Doctor</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#" class="sidebar-link">
                    <i class="fa fa-procedures"></i>
                    <span>Patient</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#" class="sidebar-link">
                    <i class="fa fa-user-clock"></i>
                    <span>Doctor Schedule</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#" class="sidebar-link">
                    <i class="fa fa-notes-medical"></i>
                    <span>Appointment</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#" class="sidebar-link">
                    <i class="fa fa-id-card"></i>
                    <span>Profile</span>
                </a>
            </li>


            <!--sidebar toggler-->
            <div class="toggler">
                <button id="sidebarToggle" onclick="toggleMobileView()"></button>
            </div>

        </ul>
        <!--end of sidebar-->

        <!--content wrapper-->
        <div id="content-wrapper" class="content-wrapper">

            <!--main content-->
            <div class="content">

                <!--topbar-->
                <nav class="navbar">
                    <!--  button    -->
                    <button class="btn-menu" onclick="toggleSidebar()">
                        <i class="fa fa-bars"></i>
                    </button>


                    <!--menu bar-->
                        <ul class="navbar-nav">

                            <div class="topbar-divider"></div>

                            <li class="dropdown">
                                <a href="#" class="dropdown-link" id="userDropdown">
                                    <span class="name" id="user_profile_name">
                                        John Smith
                                    </span>
                                    <img src="" alt="" class="img-profile" id="user_profile_image">
                                </a>

                                <!--dropdown user information-->
                                <div class="dropdown-menu" id="dropdownMenu">

                                    <a href="#" class="dropdown-item">
                                        <i class="fa fa-user"></i>
                                        Profile
                                    </a>

                                    <div class="dropdown-divider"></div>

                                    <a href="#" class="dropdown-item" data-toggle="modal" target="#logoutModal">
                                        <i class="fa fa-sign-out-alt"></i>
                                        Logout
                                    </a>
                                </div>
                            </li>
                        </ul>
                </nav>
                <!--end of topbar-->


                <!--start of the page content-->
                <div class="container-fluid">









