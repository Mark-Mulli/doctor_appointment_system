<?php
include 'header.php';
include 'connectdb.php';
?>

<!--page heading-->
<h1 class="heading"> Doctor Schedule Management </h1>

<!--datatable-->
<span id="message"></span>
<div class="card-shadow">
    <div class="card-header">
        <div class="row">
            <div class="col">
                <h6 class="card-header-text">Doctor Schedule List</h6>
            </div>
            <div class="col" align="right">
                <button type="button" name="add_exam" id="add_doctor_schedule" class="btn btn-success btn-circle btn-sm"><i class="fa fa-plus"></i></button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="doctor_schedule_table" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <?php
                    if($_SESSION['type'] == 'Admin')
                    {
                        ?>
                        <th>Doctor Name</th>
                        <?php
                    }
                    ?>
                    <th>Schedule Date</th>
                    <th>Schedule Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Consulting Time</th>
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
include 'footer.php';
?>

<div id="doctor_scheduleModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" id="doctor_schedule_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title">Add Doctor Schedule</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <span id="form_message"></span>
                    <?php
                    if($_SESSION['type'] == 'Admin')
                    {
                        ?>
                        <div class="form-group">
                            <label>Select Doctor</label>
                            <select name="doctor_id" id="doctor_id" class="form-control">
                                <option value="">Select Doctor</option>
                                <?php
                                $doctor_status = 'Active';
                                $stmt = mysqli_prepare($conn,"SELECT * FROM doctor_table WHERE doctor_status = ? ORDER BY doctor_name ASC");
                                mysqli_stmt_bind_param($stmt, 's',$doctor_status);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);

                                $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

                                foreach ($rows as $row) {
                                    echo '
                                <option value="'.$row["doctor_id"].'">'.$row["doctor_name"].'</option>
                                ';
                                }

                                ?>
                            </select>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="form-group">
                        <label>Schedule Date</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                            </div>
                            <input type="date" name="doctor_schedule_date" id="doctor_schedule_date" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Start Time</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-clock"></i></span>
                            </div>
                            <input type="time" name="doctor_schedule_start_time" id="doctor_schedule_start_time" class="form-control" onkeydown="return false" onpaste="return false;" ondrop="return false;" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>End Time</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-clock"></i></span>
                            </div>
                            <input type="time" name="doctor_schedule_end_time" id="doctor_schedule_end_time" class="form-control" onkeydown="return false" onpaste="return false;" ondrop="return false;" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Average Consulting Time</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-clock"></i></span>
                            </div>
                            <select name="average_consulting_time" id="average_consulting_time" class="form-control">
                                <option value="">Select Consulting Duration</option>
                                <?php
                                $count = 0;
                                for($i = 1; $i <= 15; $i++)
                                {
                                    $count += 5;
                                    echo '<option value="'.$count.'">'.$count.' Minutes </option>';
                                }
                                ?>
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
    $(document).ready(function () {

        // Function to get current date in YYYY-MM-DD format
        function getCurrentDate() {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
            var yyyy = today.getFullYear();
            return yyyy + '-' + mm + '-' + dd;
        }

        // Function to get current time in HH:MM format
        function getCurrentTime() {
            var today = new Date();
            var hh = String(today.getHours()).padStart(2, '0');
            var mm = String(today.getMinutes()).padStart(2, '0');
            return hh + ':' + mm;
        }

        // Set minimum date for doctor_schedule_date input field
        $('#doctor_schedule_date').attr('min', getCurrentDate());

        // Set time range for doctor_schedule_start_time input field
        $('#doctor_schedule_start_time').attr('min', '08:00');
        $('#doctor_schedule_start_time').attr('max', '17:00'); // 5 PM

        // Set time range for doctor_schedule_end_time input field
        $('#doctor_schedule_end_time').attr('min', '08:00');
        $('#doctor_schedule_end_time').attr('max', '17:00'); // 5 PM

        // Add event listener to doctor_schedule_end_time to check against doctor_schedule_start_time
        $('#doctor_schedule_end_time').on('change', function () {
            var startTime = $('#doctor_schedule_start_time').val();
            var endTime = $(this).val();

            if (startTime && endTime) {
                // Convert start and end times to Date objects for comparison
                var startDate = new Date("2000-01-01 " + startTime); // Assuming dates don't matter, just time
                var endDate = new Date("2000-01-01 " + endTime);

                // Check if end time is before or equal to start time
                if (endDate <= startDate) {
                    alert("End time must be after start time.");
                    $(this).val(""); // Reset end time to empty
                } else {
                    // Calculate the minimum allowed end time (10 minutes after start time)
                    var minEndTime = new Date(startDate.getTime() + 10 * 60000); // 10 minutes in milliseconds
                    if (endDate.getTime() < minEndTime.getTime()) {
                        alert("End time must be at least 10 minutes after start time.");
                        // Reset end time to 10 minutes after start time
                        var formattedMinEndTime = pad(minEndTime.getHours()) + ":" + pad(minEndTime.getMinutes());
                        $(this).val(formattedMinEndTime);
                    }
                }
            }

            updateConsultingTimeOptions(startTime, endTime);
        });

        // Function to pad single digit numbers with leading zero
        function pad(number) {
            if (number < 10) {
                return '0' + number;
            }
            return number;
        }

        // Function to update average consulting time options based on start and end times
        function updateConsultingTimeOptions(startTime, endTime) {
            // Convert start and end times to Date objects
            var startDate = new Date("2000-01-01 " + startTime);
            var endDate = new Date("2000-01-01 " + endTime);

            // Calculate the time difference in minutes
            var timeDiff = (endDate - startDate) / 1000 / 60;

            // Update the options in the dropdown
            $('#average_consulting_time option').each(function () {
                var optionValue = parseInt($(this).val());
                if (optionValue > timeDiff) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });
        }

        // Add event listener to doctor_schedule_date to prevent restriction when selecting tomorrow's date
        $('#doctor_schedule_date').on('change', function () {
            var selectedDate = $(this).val();
            var currentDate = getCurrentDate();
            var currentTime = getCurrentTime();
            if (selectedDate === currentDate) {
                $('#doctor_schedule_start_time').attr('min', currentTime);
                $('#doctor_schedule_end_time').attr('min', currentTime);
            } else {
                $('#doctor_schedule_start_time').attr('min', '08:00');
                $('#doctor_schedule_end_time').attr('min', '08:00');
            }
        });

        var dataTable = $('#doctor_schedule_table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "doctor_schedule_action.php", // PHP script to fetch data
                "type": "POST",
                "data": {action: 'fetch'}
            },
            "columnDefs": [
                {
                    <?php
                    if($_SESSION['type'] == 'Admin')
                    {
                    ?>
                    "targets":[6, 7],
                    <?php
                    }
                    else
                    {
                    ?>
                    "targets":[5, 6],
                    <?php
                    }
                    ?>

                    "orderable":false,
                }
            ]
        });

        $('#add_doctor_schedule').click(function(){

            $('#doctor_schedule_form')[0].reset();

            $('#modal_title').text('Add Doctor Schedule Data');

            $('#action').val('Add');

            $('#submit_button').val('Add');

            $('#doctor_scheduleModal').modal('show');

            $('#form_message').html('');

        });

        $('#doctor_schedule_form').on('submit', function(event) {
            event.preventDefault();
            if ($(this).valid()) { // Check if the form is valid
                // Proceed with form submission
                $.ajax({
                    url:"doctor_schedule_action.php",
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
                            $('#doctor_scheduleModal').modal('hide');
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

        $('#doctor_schedule_form').validate({ // Initialize form validation

            errorClass: 'err', // Specify the CSS class for error messages
            errorElement: 'span', // Use <span> for error messages

            rules: {
                doctor_id: {
                    required: true
                },
                doctor_schedule_date: {
                    required: true
                },
                doctor_schedule_start_time: {
                    required: true
                },
                doctor_schedule_end_time: {
                    required: true
                },
                average_consulting_time: {
                    required: true
                }
            },
            messages: {
                doctor_id: {
                    required: "Please select a doctor"
                },
                doctor_schedule_date: {
                    required: "Please select a schedule date"
                },
                doctor_schedule_start_time: {
                    required: "Please select a start time"
                },
                doctor_schedule_end_time: {
                    required: "Please select an end time"
                },
                average_consulting_time: {
                    required: "Please select average consulting time"
                }
            },
            errorPlacement: function(error, element) {
                if (element.hasClass('form-control')) {
                    error.insertAfter(element.parent()); // Append error message after the parent element
                } else {
                    error.insertAfter(element);
                }
            }
        });

        $(document).on('hidden.bs.modal', '#doctor_scheduleModal', function () {
            // Reset the form validation when the modal is closed
            $('#doctor_schedule_form').validate().resetForm();
            // Clear error styling from input fields
            $('#doctor_schedule_form').find('.err').removeClass('err');
            $('#doctor_schedule_form').find('.err').css('color', ''); // Clear color
            $('#doctor_schedule_form').find('.error-message').removeClass('error-message');
        });

        $(document).on('click', '.edit_button', function(){

            var doctor_schedule_id = $(this).data('id');

            $('#form_message').html('');

            $.ajax({

                url:"doctor_schedule_action.php",

                method:"POST",

                data:{doctor_schedule_id:doctor_schedule_id, action:'fetch_single'},

                dataType:'JSON',

                success:function(data)
                {
                    <?php
                    if($_SESSION['type'] == 'Admin')
                    {
                    ?>
                    $('#doctor_id').val(data.doctor_id);
                    <?php
                    }
                    ?>
                    $('#doctor_schedule_date').val(data.doctor_schedule_date);

                    $('#doctor_schedule_start_time').val(data.doctor_schedule_start_time);

                    $('#doctor_schedule_end_time').val(data.doctor_schedule_end_time);

                    $('#average_consulting_time').val(data.average_consulting_time);

                    $('#modal_title').text('Edit Doctor Schedule Data');

                    $('#action').val('Edit');

                    $('#submit_button').val('Edit');

                    $('#doctor_scheduleModal').modal('show');

                    $('#hidden_id').val(doctor_schedule_id);
                }

            })
        });

        $(document).on('click', '.status_button', function(){
            var id = $(this).data('id');
            var status = $(this).data('status');
            var next_status = 'Active';
            if(status == 'Active')
            {
                next_status = 'Inactive';
            }
            if(confirm("Are you sure you want to "+next_status+" it?"))
            {

                $.ajax({

                    url:"doctor_schedule_action.php",

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

        $(document).on('click', '.delete_button', function(){

            var id = $(this).data('id');

            if(confirm("Are you sure you want to remove it?"))
            {

                $.ajax({

                    url:"doctor_schedule_action.php",

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
