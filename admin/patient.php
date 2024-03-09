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
            <div id="patient_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                <div class="row">
                    <div class="col-12 col-6">
                        <div class="dataTables_length" id="patient_table_length">
                            <label>
                                Show
                                <select name="patient_table_length" aria-controls="patient_table" class="custom-select custom-select-sm form-control form-control-sm"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select>
                                entries
                            </label>
                        </div>
                    </div>
                    <div class="col-12 col-6">
                        <div id="patient_table_filter" class="dataTables_filter">
                            <label>Search:
                                <input type="search" class="form-control form-control-sm" placeholder="" aria-controls="patient_table">
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-s-12">
                        <table id="patient_table" class="table table-bordered dataTable no-footer" width="100%" cellspacing="0" role="grid" aria-describedby="patient_table_info" style="width: 100%">
                            <thead>
                            <tr role="row">
                                <th class="sorting" tabindex="0" aria-controls="patient_table" rowspan="1" colspan="1" style="width: 44px;" aria-label="First Name: activate to sort column ascending">First Name</th>
                                <th class="sorting" tabindex="0" aria-controls="patient_table" rowspan="1" colspan="1" style="width: 53px;" aria-label="Last Name: activate to sort column ascending">Last Name</th>
                                <th class="sorting" tabindex="0" aria-controls="patient_table" rowspan="1" colspan="1" style="width: 165px;" aria-label="Email Address: activate to sort column ascending">Email Address</th>
                                <th class="sorting_asc" tabindex="0" aria-controls="patient_table" rowspan="1" colspan="1" style="width: 88px;" aria-label="Contact No.: activate to sort column descending" aria-sort="ascending">Contact No.</th>
                                <th class="sorting" tabindex="0" aria-controls="patient_table" rowspan="1" colspan="1" style="width: 85px;" aria-label="Email Verification Status: activate to sort column ascending">Email Verification Status</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 49px;" aria-label="Action">Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<?php
include "footer.php";
?>

<script>
    new DataTable('#example');
</script>





