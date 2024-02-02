    </div>
    <!--end of container fluid-->
    </div>
    <!--end of Main Content-->

    <footer class="footer">
        <div class="footer-container">
                <div class="copyright">
                    <span>Copyright &#169;  DAMS <?php echo date('Y'); ?></span>
                </div>
        </div>
    </footer>


    </div>
    <!--end of content wrapper-->

</div>
    <!--end of wrap-->

    <a href="#page-top" class="scroll"></a>

<script>
    function toggleSidebar() {
        var sidebar = document.getElementById('sidenav');
        sidebar.classList.toggle('sidebar-toggled');
    }

    function toggleMobileView() {
        var bodyElement = document.getElementById('mainBody');
        bodyElement.classList.toggle('mobile-view');
    }

    document.addEventListener('DOMContentLoaded', function () {
        var dropdownLink = document.getElementById('userDropdown');
        var dropdownMenu = document.getElementById('dropdownMenu');

        dropdownLink.addEventListener('click', function (event) {
            event.stopPropagation(); // Prevents the event from reaching the window.onclick handler
            dropdownMenu.classList.toggle('show');
        });

        // Close the dropdown if the user clicks outside of it
        window.onclick = function (event) {
            if (!event.target.matches('.dropdown-link')) {
                dropdownMenu.classList.remove('show');
            }
        }
    });
</script>

</body>
</html>
