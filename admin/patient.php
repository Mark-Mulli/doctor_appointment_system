<?php
include "header.php";
?>

<!--page heading-->
<h1 class="heading"> Patient Management </h1>

<!--datatable-->
<div class="card-shadow">
    <div class="card-header">
        <div class="row">
            <div class="col">
                <h6 class="card-header-text">Patient List</h6>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="patient_table" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email Address</th>
                    <th>Contact No.</th>
                    <th>Email Verification Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include "footer.php";
?>


<script>
    $(document).ready(function() {
        $('#patient_table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "get_patients.php", // PHP script to fetch data
                "type": "POST"
            },
            "columnDefs": [
                {
                    "targets": -1, // Target the last column (Action column)
                    "orderable": false, // Disable sorting
                    "searchable": false // Disable searching
                }
            ]
        });
    });
</script>





