<nav class="navbar">
    <!-- Brand -->
    <a href="#" class="nav_brand"><?php echo $_SESSION['patient_name']; ?></a>

    <!-- Links -->
    <ul class="nav_nav" id="nav-links">
        <li>
            <a href="profile.php">Profile</a>
        </li>
        <li>
            <a href="#">Logout</a>
        </li>
    </ul>

    <label id="icon" onclick="toggleMenu()">
        <i class="fa fa-bars" id="menu-icon"></i>
        <i class="fa fa-times" id="close-icon" style="display: none;"></i>
    </label>
</nav>
