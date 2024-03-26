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

<!--view patient-->
<div id="viewModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">View Patient Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="patient_details">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="patientModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" id="patient_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title">Edit Patient</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <span id="form_message"></span>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Patient Email Address <span class="text-danger">*</span></label>
                                <input type="text" name="patient_email_address" id="patient_email_address" class="form-control" required data-parsley-type="email" data-parsley-trigger="keyup" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Patient First Name <span class="text-danger">*</span></label>
                                <input type="text" name="patient_first_name" id="patient_first_name" class="form-control" required data-parsley-trigger="keyup" />
                            </div>
                            <div class="col-md-6">
                                <label>Patient Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="patient_last_name" id="patient_last_name" class="form-control" required data-parsley-trigger="keyup" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Patient Postal Address </label>
                                <input type="text" name="patient_address" id="patient_address" class="form-control" />
                            </div>
                            <div class="col-md-6">
                                <label>Patient Date of Birth </label>
                                <input type="text" name="patient_date_of_birth" id="patient_date_of_birth" readonly class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Patient Postal Code </label>
                                <input type="text" name="patient_postal_code" id="patient_postal_code" class="form-control" />
                            </div>
                            <div class="col-md-6">
                                <label>Patient phone number <span class="text-danger">*</span></label>
                                <input type="text" name="patient_phone_no" id="patient_phone_no" class="form-control" required data-parsley-trigger="keyup" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="hidden_id" id="hidden_id" />
                        <input type="hidden" name="action" id="action" value="Add" />
                        <input type="submit" name="submit" id="submit_button" class="btn btn-success" value="Add" />
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
        </form>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#patient_table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "get_patients.php", // PHP script to fetch data
                "type": "POST",
                "data": {action: 'fetch'}
            },
            "columnDefs": [
                {
                    "targets": -1, // Target the last column (Action column)
                    "orderable": false, // Disable sorting
                    "searchable": false // Disable searching
                }
            ]
        });

        $(document).on('click', '.view_button', function(){
            var patient_id = $(this).data('id');

            $.ajax({
                url: "get_patients.php",
                method: "POST",
                data: {patient_id: patient_id, action: 'fetch_single'},
                dataType: 'JSON',
                success: function(data) {
                    var html = '<div class="table-responsive">';
                    html += '<table class="table">';
                    html += '<tr><th width="40%" class="text-right">Email <br> Address</th><td width="60%">'+data.patient_email_address+'</td></tr>';
                    html += '<tr><th width="40%" class="text-right">Patient <br> Name</th><td width="60%">'+data.patient_first_name+' '+data.patient_last_name+'</td></tr>';
                    html += '<tr><th width="40%" class="text-right">Contact <br> No.</th><td width="60%">'+data.patient_phone_no+'</td></tr>';
                    html += '<tr><th width="40%" class="text-right">Postal <br> Address</th><td width="60%">'+data.patient_address+'</td></tr>';
                    html += '<tr><th width="40%" class="text-right">Postal <br> Code</th><td width="60%">'+data.patient_postal_code+'</td></tr>';
                    html += '<tr><th width="40%" class="text-right">Date of <br> Birth</th><td width="60%">'+data.patient_date_of_birth+'</td></tr>';
                    html += '<tr><th width="40%" class="text-right">Gender</th><td width="60%">'+data.patient_gender+'</td></tr>';
                    html += '<tr><th width="40%" class="text-right">Email <br> Verification <br> Status</th><td width="60%">'+data.email_verify+'</td></tr>';
                    html += '</table></div>';
                    $('#viewModal').modal('show');
                    $('#patient_details').html(html);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText); // Log any errors to console
                    // Optionally, you can display an error message to the user here
                }
            });
        });
    });
</script>





