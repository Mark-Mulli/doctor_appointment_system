<?php
    session_start();
    include "header.php";
?>

<div class="container_fluid">
    <?php
        include "navbar.php";
    ?>
</div>

<script>
    function toggleMenu() {
        var navlinks = document.getElementById("nav-links");
        var menu = document.getElementById("menu-icon");
        var close = document.getElementById("close-icon");
        //var message = document.querySelector(".error-message,.success-message");
        var content = document.querySelector(".content-wrapper");

        if (navlinks.style.left === "0px" || navlinks.style.left === "0%") {
            hideMenu();
        } else {
            showMenu();
        }

        function showMenu() {
            navlinks.style.left = "0";
            menu.style.display = "none";
            close.style.display = "block";
            //message.style.marginTop = "200px";
            content.style.marginTop = "200px";
        }

        function hideMenu() {
            navlinks.style.left = "-300%";
            menu.style.display = "block";
            close.style.display = "none";
            //message.style.marginTop = "1rem";
            content.style.marginTop = "0";
        }
        function hideMenuOnResize() {
            if (window.innerWidth > 909) {
                hideMenu();
            }
        }
        // Add an event listener to the window resize event
        window.addEventListener('resize', hideMenuOnResize);
    }
</script>

<?php
include "footer.php";
?>


