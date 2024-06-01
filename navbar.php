<nav class="navbar">
    <!-- Brand -->
    <a href="dashboard.php" class="nav_brand"><?php echo $_SESSION['patient_name']; ?></a>

    <!-- Links -->
    <ul class="nav_nav" id="nav-links">
        <li>
            <a href="profile.php">Profile</a>
        </li>
        <li>
            <a href="dashboard.php">Book Appointment</a>
        </li>
        <li>
            <a href="appointment.php">My Appointment</a>
        </li>
        <li>
            <a href="logout.php">Logout</a>
        </li>
    </ul>

    <label id="icon" onclick="toggleMenu()">
        <i class="fa fa-bars" id="menu-icon"></i>
        <i class="fa fa-times" id="close-icon" style="display: none;"></i>
    </label>
</nav>
