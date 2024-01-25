<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DAMS</title>
    <link rel="stylesheet" href="assets/style.css">
    <!--font icon-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

    <nav class="navigation-bar">
        <div class="nav-link">
             <ul>
                 <li>
                     <a class="home" href="#">DAMS</a>
                 </li>
             </ul>
        </div>

        <div class="nav-link">
            <ul>
                 <li>
                     <?php
                     if(!isset($_SESSION['patient_id']))
                     {
                     ?>
                     <a href="login.php">Login</a>
                         <?php
                     }
                     ?>
                 </li>
             </ul>
        </div>

    </nav>

    <div class="hero-section">
        <h1 class="header4">Doctor Appointment Management System</h1>
    </div>


