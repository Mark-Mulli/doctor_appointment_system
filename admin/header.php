<?php
session_start();
include 'connectdb.php';
?>
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
    <!-- Custom styles for this page -->
    <link rel="stylesheet" href="../datatables/dataTables.bootstrap4.min.css">

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

                <?php
                if ($_SESSION['type'] == 'Admin') {
                ?>
                <i  class="fa-solid fa-face-laugh-wink"></i>
                <div class="sidebar-text">Admin</div>
                <?php
                } else {
                ?>
                <i  class="fa-solid fa-user-doctor"></i>
                <div class="sidebar-text">Doctor</div>
                <?php
                }
                ?>
            </a>

            <!--divider-->
            <hr class="divider">

            <!--Nav item dashboard-->
            <?php
                if ($_SESSION['type'] == 'Admin') {
            ?>
            <li class="sidebar-item">
                <a href="dashboard.php" class="sidebar-link">
                    <i class="fa fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="doctor.php" class="sidebar-link">
                    <i class="fa fa-user-md"></i>
                    <span>Doctor</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="patient.php" class="sidebar-link">
                    <i class="fa fa-procedures"></i>
                    <span>Patient</span>
                </a>
            </li>
            <?php
                }
            ?>
            <li class="sidebar-item">
                <a href="doctor_schedule.php" class="sidebar-link">
                    <i class="fa fa-user-clock"></i>
                    <span>Doctor Schedule</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="appointment.php" class="sidebar-link">
                    <i class="fa fa-notes-medical"></i>
                    <span>Appointment</span>
                </a>
            </li>

            <?php
                if ($_SESSION['type'] == 'Admin') {
            ?>

            <li class="sidebar-item">
                <a href="admin_profile.php" class="sidebar-link">
                    <i class="fa fa-id-card"></i>
                    <span>Profile</span>
                </a>
            </li>
            <?php
                }
                else {
            ?>
            <!--doctor's profile-->
            <li class="sidebar-item">
                <a href="doctor_profile.php" class="sidebar-link">
                    <i class="fa fa-id-card"></i>
                    <span>Profile</span>
                </a>
            </li>
            <?php
                }
            ?>

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

                            <?php
                            $user_name = '';
                            $user_profile_image = '';

                            if ($_SESSION['type'] == 'Admin') {
                                $query = "SELECT * FROM admin_table WHERE admin_id = ?";
                                $stmt = mysqli_prepare($conn,$query);
                                mysqli_stmt_bind_param($stmt, 'i', $_SESSION['admin_id']);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);

                                $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

                                foreach ($rows as $row) {
                                    $user_name = $row['admin_name'];
                                    $user_profile_image = $row['hospital_logo'];
                                }

                            }
                            if ($_SESSION['type'] == 'Doctor') {
                                $query = " SELECT * FROM doctor_table WHERE doctor_id = ?";
                                $stmt = mysqli_prepare($conn,$query);
                                mysqli_stmt_bind_param($stmt, 'i', $_SESSION['admin_id']);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);

                                $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

                                foreach ($rows as $row) {
                                    $user_name = $row['doctor_name'];
                                    $user_profile_image = $row['doctor_profile_image'];
                                }
                            }

                            ?>

                            <li class="dropdown">
                                <a href="#" class="dropdown-link" id="userDropdown">
                                    <span class="name" id="user_profile_name">
                                        <?php echo $user_name; ?>
                                    </span>
                                    <img src="<?php echo $user_profile_image; ?>" alt="" class="img-profile" id="user_profile_image">
                                </a>

                                <!--dropdown user information-->
                                <?php
                                if($_SESSION['type'] == 'Admin')
                                {
                                ?>
                                <div class="dropdown-menu" id="dropdownMenu">

                                    <a href="admin_profile.php" class="dropdown-item">
                                        <i class="fa fa-user"></i>
                                        Profile
                                    </a>

                                    <div class="dropdown-divider"></div>
                                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#logoutModal">
                                        <i class="fa fa-sign-out-alt"></i>
                                        Logout
                                    </a>
                                </div>
                                    <?php
                                }
                                if($_SESSION['type'] == 'Doctor')
                                {
                                ?>
                                <div class="dropdown-menu" id="dropdownMenu">

                                    <a href="doctor_profile.php" class="dropdown-item">
                                        <i class="fa fa-user"></i>
                                        Profile
                                    </a>

                                    <div class="dropdown-divider"></div>
                                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#logoutModal">
                                        <i class="fa fa-sign-out-alt"></i>
                                        Logout
                                    </a>
                                </div>
                                    <?php
                                }
                                ?>
                            </li>
                        </ul>
                </nav>
                <!--end of topbar-->


                <!--start of the page content-->
                <div class="container-fluid">









