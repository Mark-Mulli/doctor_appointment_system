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

    <a href="#mainBody" class="scroll">
        <i class="fa fa-angle-up"></i>
    </a>

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
            dropdownMenu.classList.toggle('appear');
        });

        // Close the dropdown if the user clicks outside of it
        window.onclick = function (event) {
            if (!event.target.matches('.dropdown-link')) {
                dropdownMenu.classList.remove('show');
            }
        }
        var scrollButton = document.querySelector('.scroll');

        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                scrollButton.style.display = 'block';
            } else {
                scrollButton.style.display = 'none';
            }
        });

        scrollButton.addEventListener('click', function(e) {
            e.preventDefault();

            var targetElement = document.querySelector(this.getAttribute('href'));
            if (targetElement) {
                var offsetTop = targetElement.offsetTop;
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
</script>



    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

<!--    core javascript-->
<script src="../jquery/jquery.min.js"></script>
<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
<!--validations-->
<script src="../jquery/jquery.validate.min.js"></script>

<!-- Page level plugins -->
<script src="../datatables/jquery.dataTables.min.js"></script>
<script src="../datatables/dataTables.bootstrap4.min.js"></script>

</body>
</html>
