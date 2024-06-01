<?php
include "header.php";

$currentDate = new DateTime();
$maxDate = $currentDate->modify('-10 years')->format('Y-m-d');
?>

<!--page heading-->
<h1 class="heading"> Patient Management </h1>

<!--datatable-->
<span id="message"></span>
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
                                <input type="text" name="patient_email_address" id="patient_email_address" class="form-control"/>
                            </div>
                            <div class="col-md-6">
                                <label>Patient phone number <span class="text-danger">*</span></label>
                                <input type="text" name="patient_phone_no" id="patient_phone_no" class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Patient First Name <span class="text-danger">*</span></label>
                                <input type="text" name="patient_first_name" id="patient_first_name" class="form-control"/>
                            </div>
                            <div class="col-md-6">
                                <label>Patient Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="patient_last_name" id="patient_last_name" class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Patient Postal Address <span class="text-danger">*</span></label>
                                <input type="text" name="patient_address" id="patient_address" class="form-control" />
                            </div>
                            <div class="col-md-6">
                                <label>Patient Date of Birth <span class="text-danger">*</span></label>
                                <input class="form-control" type="date" id="patient_date_of_birth" name="patient_date_of_birth" min="1940-01-01" max="<?php echo $maxDate; ?>" >
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Patient Postal Code <span class="text-danger">*</span></label>
                                <input type="text" name="patient_postal_code" id="patient_postal_code" class="form-control" />
                            </div>
                            <div class="col-md-6">
                                <label>Patient gender <span class="text-danger">*</span></label>
                                <select id="patient_gender" name="patient_gender" class="form-control">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
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
        var dataTable = $('#patient_table').DataTable({
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

        $('#patient_form').on('submit', function(event) {
            event.preventDefault();
            if ($(this).valid()) { // Check if the form is valid
                // Proceed with form submission
                $.ajax({
                    url:"get_patients.php",
                    method:"POST",
                    data: new FormData(this),
                    dataType:'json',
                    contentType: false,
                    cache: false,
                    processData:false,
                    beforeSend:function()
                    {
                        $('#submit_button').attr('disabled', 'disabled');
                        $('#submit_button').val('wait...');
                    },
                    success:function(data)
                    {
                        $('#submit_button').attr('disabled', false);
                        if(data.error != '')
                        {
                            $('#form_message').html(data.error);
                            $('#submit_button').val('Add');
                            setTimeout(function(){

                                $('#form_message').html('');

                            }, 2500);
                        }
                        else
                        {
                            $('#patientModal').modal('hide');
                            $('#message').html(data.success);
                            dataTable.ajax.reload();

                            setTimeout(function(){

                                $('#message').html('');

                            }, 2500);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText); // Log any errors to console
                        // Optionally, you can display an error message to the user here
                    }
                });
            }
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

        $.validator.addMethod("customRegex", function(value, element, regex) {
            return regex.test(value);
        }, "Invalid format.");

        $('#patient_form').validate({ // Initialize form validation

            errorClass: 'err', // Specify the CSS class for error messages
            errorElement: 'span', // Use <span> for error messages

            rules: {
                patient_email_address: {
                    required: true,
                    customRegex: /^[a-zA-Z0-9. _-]+@[a-zA-Z0-9. -]+\.[a-zA-Z]{2,4}$/
                },
                patient_phone_no: {
                    required: true,
                    digits: true,
                    customRegex: /^(?:\+254|0)(?:7\d{8}|1\d{8})$/
                },
                patient_first_name: {
                    required: true,
                    customRegex: /^[a-zA-Z]+$/
                },
                patient_last_name: {
                    required: true,
                    customRegex: /^[a-zA-Z]+$/
                },
                patient_address: {
                    required: true,
                    customRegex: /^\d{5}$/
                },
                patient_postal_code: {
                    required: true,
                    digits: true,
                    customRegex: /^\d{5}$/
                },
                patient_date_of_birth: {
                    required: true,
                    date: true
                }
            },
            messages: {
                patient_email_address: {
                    required: "Please enter email address",
                    customRegex: "Please enter a valid email address"
                },
                patient_phone_no: {
                    required: "Please enter phone number",
                    digits: "Please enter digits only",
                    customRegex: "Must be in the form of '+2547','07' or '01' followed by 8 digits."
                },
                patient_first_name: {
                    required: "Please enter first name",
                    customRegex: "Please enter a valid first name"
                },
                patient_last_name: {
                    required: "Please enter last name",
                    customRegex: "Please enter a valid last name"
                },
                patient_address: {
                    required: "Please enter postal address",
                    customRegex: "Postal Address must be of 5 digits"
                },
                patient_postal_code: {
                    required: "Please enter postal code",
                    digits: "Please enter digits only",
                    customRegex: "Postal Code must be of 5 digits"
                },
                patient_date_of_birth: {
                    required: "Please enter date of birth",
                    date: "Please enter a valid date"
                }
            },
            errorPlacement: function(error, element) {
                error.insertAfter(element); // Display error message below the input field
            }
        });

        $(document).on('hidden.bs.modal', '#patientModal', function () {
            // Reset the form validation when the modal is closed
            $('#patient_form').validate().resetForm();
            // Clear error styling from input fields
            $('#patient_form').find('.err').removeClass('err');
            $('#patient_form').find('.err').css('color', ''); // Clear color
            $('#patient_form').find('.error-message').removeClass('error-message');
        });






        $(document).on('click', '.edit_button', function(){

            var patient_id = $(this).data('id');


            $.ajax({

                url:"get_patients.php",

                method:"POST",

                data:{patient_id:patient_id, action:'fetch_single'},

                dataType:'JSON',

                success:function(data)
                {

                    $('#patient_email_address').val(data.patient_email_address);

                    $('#patient_email_address').val(data.patient_email_address);
                    $('#patient_first_name').val(data.patient_first_name);
                    $('#patient_last_name').val(data.patient_last_name);
                    $('#patient_phone_no').val(data.patient_phone_no);
                    $('#patient_address').val(data.patient_address);
                    $('#patient_postal_code').val(data.patient_postal_code);
                    $('#patient_gender').val(data.patient_gender);
                    $('#patient_date_of_birth').val(data.patient_date_of_birth);

                    $('#modal_title').text('Edit Patient');

                    $('#action').val('Edit');

                    $('#submit_button').val('Edit');

                    $('#patientModal').modal('show');

                    $('#hidden_id').val(patient_id);
                }
            })
        });

        $(document).on('click', '.delete_button', function(){

            var id = $(this).data('id');

            if(confirm("Are you sure you want to remove it?"))
            {

                $.ajax({

                    url:"get_patients.php",

                    method:"POST",

                    data:{id:id, action:'delete'},

                    success:function(data)
                    {

                        $('#message').html(data);

                        dataTable.ajax.reload();

                        setTimeout(function(){

                            $('#message').html('');

                        }, 5000);

                    }

                })

            }

        });
    });
</script>





