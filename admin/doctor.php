<?php
include "header.php";

$currentDate = new DateTime();
$maxDate = $currentDate->modify('-25 years')->format('Y-m-d');
?>
<!--page heading-->
<h1 class="heading"> Doctor Management </h1>

<!--datatable-->
<span id="message"></span>
<div class="card-shadow">
    <div class="card-header">
        <div class="row">
            <div class="col">
                <h6 class="card-header-text">Doctor List</h6>
            </div>
            <div class="col" align="right">
                <button type="button" name="add_doctor" id="add_doctor" class="btn btn-success btn-circle btn-sm"><i class="fa fa-plus"></i></button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="doctor_table" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>Doctor Name</th>
                    <th>Image</th>
                    <th>Email Address</th>
                    <th>Doctor Phone No.</th>
                    <th>Doctor Speciality</th>
                    <th>Status</th>
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

<div id="doctorModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" id="doctor_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title">Add Doctor</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <span id="form_message"></span>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Doctor Email Address <span class="text-danger">*</span></label>
                                <input type="text" name="doctor_email_address" id="doctor_email_address" class="form-control"/>
                            </div>
                            <div class="col-md-6">
                                <label>Doctor Password <span class="text-danger">*</span></label>
                                <input type="password" name="doctor_password" id="doctor_password" class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Doctor Name <span class="text-danger">*</span></label>
                                <input type="text" name="doctor_name" id="doctor_name" class="form-control"/>
                            </div>
                            <div class="col-md-6">
                                <label>Doctor Phone No. <span class="text-danger">*</span></label>
                                <input type="text" name="doctor_phone_no" id="doctor_phone_no" class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Doctor Address </label>
                                <input type="text" name="doctor_address" id="doctor_address" class="form-control" />
                            </div>
                            <div class="col-md-6">
                                <label>Doctor Date of Birth </label>
                                <input type="date" name="doctor_date_of_birth" id="doctor_date_of_birth" class="form-control" min="1940-01-01" max="<?php echo $maxDate; ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Doctor Degree <span class="text-danger">*</span></label>
                                <select name="doctor_degree" id="doctor_degree" class="form-control">
                                    <option value="">Select Degree</option>
                                    <option value="Bachelor of Medicine and Bachelor of Surgery (MBBS)">Bachelor of Medicine and Bachelor of Surgery (MBBS)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Doctor Speciality <span class="text-danger">*</span></label>
                                <select name="doctor_expert_in" id="doctor_expert_in" class="form-control">
                                    <option value="">Select Speciality</option>
                                    <option value="Anatomical Pathology">Anatomical Pathology</option>
                                    <option value="Anesthesiology">Anesthesiology</option>
                                    <option value="Cardiology">Cardiology</option>
                                    <option value="Cardiovascular and Thoracic Surgery">Cardiovascular and Thoracic Surgery</option>
                                    <option value="Clinical Immunology or Allergy">Clinical Immunology or Allergy</option>
                                    <option value="Critical Care Medicine">Critical Care Medicine</option>
                                    <option value="Dermatology">Dermatology</option>
                                    <option value="Diagnostic Radiology">Diagnostic Radiology</option>
                                    <option value="Emergency Medicine">Emergency Medicine</option>
                                    <option value="Endocrinology and Metabolism">Endocrinology and Metabolism</option>
                                    <option value="Family Medicine">Family Medicine</option>
                                    <option value="Gastroenterology">Gastroenterology</option>
                                    <option value="General Internal Medicine">General Internal Medicine</option>
                                    <option value="General Surgery">General Surgery</option>
                                    <option value="General or Clinical Pathology">General or Clinical Pathology</option>
                                    <option value="Geriatric Medicine">Geriatric Medicine</option>
                                    <option value="Hematology">Hematology</option>
                                    <option value="Medical Biochemistry">Medical Biochemistry</option>
                                    <option value="Medical Genetics">Medical Genetics</option>
                                    <option value="Medical Microbiology and Infectious Diseases">Medical Microbiology and Infectious Diseases</option>
                                    <option value="Medical Oncology">Medical Oncology</option>
                                    <option value="Nephrology">Nephrology</option>
                                    <option value="Neurology">Neurology</option>
                                    <option value="Neurosurgery">Neurosurgery</option>
                                    <option value="Nuclear Medicine">Nuclear Medicine</option>
                                    <option value="Obstetrics or Gynecology">Obstetrics or Gynecology</option>
                                    <option value="Occupational Medicine">Occupational Medicine</option>
                                    <option value="Ophthalmology">Ophthalmology</option>
                                    <option value="Orthopedic Surgery">Orthopedic Surgery</option>
                                    <option value="Otolaryngology">Otolaryngology</option>
                                    <option value="Pediatrics">Pediatrics</option>
                                    <option value="Physical Medicine and Rehabilitation (PM and R)">Physical Medicine and Rehabilitation (PM and R)</option>
                                    <option value="Plastic Surgery">Plastic Surgery</option>
                                    <option value="Psychiatry">Psychiatry</option>
                                    <option value="Public Health and Preventive Medicine (PhPm)">Public Health and Preventive Medicine (PhPm)</option>
                                    <option value="Radiation Oncology">Radiation Oncology</option>
                                    <option value="Respirology">Respirology</option>
                                    <option value="Rheumatology">Rheumatology</option>
                                    <option value="Urology">Urology</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Doctor Image <span class="text-danger">*</span></label>
                        <br />
                        <input type="file" name="doctor_profile_image" id="doctor_profile_image" />
                        <div id="uploaded_image"></div>
                        <input type="hidden" name="hidden_doctor_profile_image" id="hidden_doctor_profile_image" />
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

<div id="viewModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">View Doctor Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="doctor_details">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
         var dataTable = $('#doctor_table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "get_doctors.php", // PHP script to fetch data
                "type": "POST",
                "data": {action: 'fetch'}
            },
            "columnDefs": [
                {
                    "targets":[1,2,3,4,5,6],
                    "orderable": false // Disable sorting
                }
            ]
        });

        $('#add_doctor').click(function(){

            $('#doctor_form')[0].reset();

            $('#modal_title').text('Add Doctor');

            $('#action').val('Add');

            $('#submit_button').val('Add');

            $('#doctorModal').modal('show');

            $('#form_message').html('');

            $('#doctor_password').closest('.col-md-6').show();


            $('#doctor_email_address').closest('.col-sm-12').removeClass('col-sm-12');

            $('#doctor_email_address').closest('div').addClass('col-md-6');

        });

        $('#doctor_form').on('submit', function(event) {
            event.preventDefault();
            if ($(this).valid()) { // Check if the form is valid
                // Proceed with form submission
                $.ajax({
                    url:"get_doctors.php",
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
                            $('#doctorModal').modal('hide');
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
            var doctor_id = $(this).data('id');

            $.ajax({

                url:"get_doctors.php",

                method:"POST",

                data:{doctor_id:doctor_id, action:'fetch_single'},

                dataType:'JSON',

                success:function(data)
                {
                    var html = '<div class="table-responsive">';
                    html += '<table class="table">';

                    html += '<tr><td colspan="2" class="text-center" style="text-align: center"><img src="'+data.doctor_profile_image+'" class="img-fluid img-thumbnail" width="150" /></td></tr>';

                    html += '<tr><th width="40%" class="text-right">Doctor Email Address</th><td width="60%">'+data.doctor_email_address+'</td></tr>';

                    html += '<tr><th width="40%" class="text-right">Doctor Name</th><td width="60%">'+data.doctor_name+'</td></tr>';

                    html += '<tr><th width="40%" class="text-right">Doctor Phone No.</th><td width="60%">'+data.doctor_phone_no+'</td></tr>';

                    html += '<tr><th width="40%" class="text-right">Doctor Address</th><td width="60%">'+data.doctor_address+'</td></tr>';

                    html += '<tr><th width="40%" class="text-right">Doctor Date of Birth</th><td width="60%">'+data.doctor_date_of_birth+'</td></tr>';
                    html += '<tr><th width="40%" class="text-right">Doctor Qualification</th><td width="60%">'+data.doctor_degree+'</td></tr>';

                    html += '<tr><th width="40%" class="text-right">Doctor Speciality</th><td width="60%">'+data.doctor_expert_in+'</td></tr>';

                    html += '</table></div>';

                    $('#viewModal').modal('show');

                    $('#doctor_details').html(html);

                }

            })
        });

        $.validator.addMethod("customRegex", function(value, element, regex) {
            return regex.test(value);
        }, "Invalid format.");

        $('#doctor_form').validate({ // Initialize form validation

            errorClass: 'err', // Specify the CSS class for error messages
            errorElement: 'span', // Use <span> for error messages

            rules: {
                doctor_email_address: {
                    required: true,
                    customRegex: /^[a-zA-Z0-9. _-]+@[a-zA-Z0-9. -]+\.[a-zA-Z]{2,4}$/
                },
                doctor_password: {
                    required: true,
                    customRegex: /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&.])[A-Za-z\d@$!%*#?&.]{8,16}$/
                },
                doctor_phone_no: {
                    required: true,
                    customRegex: /^(?:\+254|0)(?:7\d{8}|1\d{8})$/
                },
                doctor_name: {
                    required: true,
                    customRegex: /^[a-zA-Z.\s]+$/
                },
                doctor_address: {
                    required: true,
                    customRegex: /^\d{5}$/
                },
                doctor_date_of_birth: {
                    required: true,
                    date: true
                },
                doctor_degree: {
                    required: true,
                    customRegex: /^[a-zA-Z\s()]+$/
                },
                doctor_expert_in: {
                    required: true,
                    customRegex: /^[a-zA-Z\s()]+$/
                }

            },
            messages: {
                doctor_email_address: {
                    required: "Please enter email address",
                    customRegex: "Please enter a valid email address"
                },
                doctor_password: {
                    required: "Please enter email address",
                    customRegex: "Invalid Passcode. Must contain at least 1 uppercase letter, 1 lowercase letter, 1 digit, 1 special character, and be 8-16 characters long."
                },
                doctor_phone_no: {
                    required: "Please enter phone number",
                    customRegex: "Must be in the form of '+2547','07' or '01' followed by 8 digits."
                },
                doctor_name: {
                    required: "Please enter your name",
                    customRegex: "Please enter a valid name"
                },
                doctor_address: {
                    required: "Please enter postal address",
                    customRegex: "Postal Address must be of 5 digits"
                },
                doctor_date_of_birth: {
                    required: "Please enter date of birth",
                    date: "Please enter a valid date"
                },
                doctor_degree: {
                    required: "Please enter a doctor's degree",
                    customRegex: "Enter a valid doctor's degree"
                },
                doctor_expert_in: {
                    required: "Enter area of specialization",
                    customRegex: "Enter a valid area of specialization"
                }
            },
            errorPlacement: function(error, element) {
                error.insertAfter(element); // Display error message below the input field
            }
        });

        $(document).on('hidden.bs.modal', '#doctorModal', function () {
            // Reset the form validation when the modal is closed
            $('#doctor_form').validate().resetForm();
            // Clear error styling from input fields
            $('#doctor_form').find('.err').removeClass('err');
            $('#doctor_form').find('.err').css('color', ''); // Clear color
            $('#doctor_form').find('.error-message').removeClass('error-message');
        });

        $(document).on('click', '.edit_button', function(){

            var doctor_id = $(this).data('id');

            $('#form_message').html('');

            $.ajax({

                url:"get_doctors.php",

                method:"POST",

                data:{doctor_id:doctor_id, action:'fetch_single'},

                dataType:'JSON',

                success:function(data)
                {

                    $('#doctor_email_address').val(data.doctor_email_address);

                    $('#doctor_email_address').val(data.doctor_email_address);

                    $('#doctor_name').val(data.doctor_name);
                    $('#uploaded_image').html('<img src="'+data.doctor_profile_image+'" class="img-fluid img-thumbnail" width="150" />')
                    $('#hidden_doctor_profile_image').val(data.doctor_profile_image);
                    $('#doctor_phone_no').val(data.doctor_phone_no);
                    $('#doctor_address').val(data.doctor_address);
                    $('#doctor_date_of_birth').val(data.doctor_date_of_birth);
                    $('#doctor_degree').val(data.doctor_degree);
                    $('#doctor_expert_in').val(data.doctor_expert_in);

                    $('#modal_title').text('Edit Doctor');

                    $('#action').val('Edit');

                    $('#submit_button').val('Edit');

                    $('#doctorModal').modal('show');

                    $('#hidden_id').val(doctor_id);

                    $('#doctor_password').closest('.col-md-6').hide();

                    $('#doctor_email_address').closest('.col-md-6').removeClass('col-md-6');

                    $('#doctor_email_address').closest('div').addClass('col-sm-12');

                }

            })

        });

        $(document).on('click', '.status_button', function(){
            var id = $(this).data('id');
            var status = $(this).data('status');
            var next_status = 'Active';
            if(status == 'Active')
            {
                next_status = 'On Leave';
            }
            if(confirm("Are you sure you want to change the status to "+next_status+"?"))
            {

                $.ajax({

                    url:"get_doctors.php",

                    method:"POST",

                    data:{id:id, action:'change_status', status:status, next_status:next_status},

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

        //delete
        $(document).on('click', '.delete_button', function(){

            var id = $(this).data('id');

            if(confirm("Are you sure you want to remove it?"))
            {

                $.ajax({

                    url:"get_doctors.php",

                    method:"POST",

                    data:{id:id, action:'delete'},

                    success:function(data)
                    {

                        $('#message').html(data);

                        dataTable.ajax.reload();

                        setTimeout(function(){

                            $('#message').html('');

                        }, 5000);

                    },


                })

            }

        });








    });

</script>
