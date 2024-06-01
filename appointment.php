<?php
session_start();
include 'header.php';
?>

<div class="container-fluid">
    <?php
    include('navbar.php');
    ?>
    <br />
    <span id="appointment_message">
        <?php echo isset($_SESSION['appointment_message']) ?  $_SESSION['appointment_message']: ''; ?>
    </span>
    <div class="content-wrapper">
        <div class="card lg">
            <span id="message"></span>
            <div class="card-header"><h4>My Appointment List</h4></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="appointment_list_table" width="100%">
                        <thead>
                        <tr>
                            <th>Appointment No.</th>
                            <th>Doctor Name</th>
                            <th>Appointment Date</th>
                            <th>Appointment Time</th>
                            <th>Appointment Day</th>
                            <th>Appointment Status</th>
                            <th>Download</th>
                            <th>Reschedule</th>
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
            content.style.marginTop = "350px";
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
include('footer.php');
?>

<div id="appointmentModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" id="appointment_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title">Reschedule your Appointment</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <span id="form_message"></span>
                    <div id="appointment_detail"></div>

                    <div style="display: flex; justify-content: space-between; flex-direction: row-reverse;">
                        <!-- Move the input element to the right side -->
                        <input type="time" style="width: 25%;" class="form-control form-control-sm" id="new_selected_time_slot" name="new_selected_time_slot" value="" readonly />
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="hidden_doctor_id" id="hidden_doctor_id" />
                    <input type="hidden" name="hidden_doctor_schedule_id" id="hidden_doctor_schedule_id" />
                    <input type="hidden" name="hidden_appointment_id" id="hidden_appointment_id" />
                    <input type="hidden" name="hidden_appointment_number" id="hidden_appointment_number" />
                    <input type="hidden" name="action" id="action" value="reschedule_appointment" />

                    <input type="submit" name="submit" id="submit_button" class="btn btn-success" value="Reschedule" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        var dataTable = $('#appointment_list_table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "action.php", // PHP script to fetch data
                "type": "POST",
                "data": {action: 'fetch_appointment'}
            },
            "columnDefs": [
                {
                    "targets":[6, 7],
                    "orderable": false // Disable sorting
                }
            ]
        });



        $(document).on('click', '.reschedule_appointment', function(){

            var doctor_schedule_id = $(this).data('doctor_schedule_id');
            var doctor_id = $(this).data('doctor_id');
            var appointment_id = $(this).data('id');
            var appointment_number = $(this).data('appointment_number');


            $.ajax({
                url:"action.php",
                method:"POST",
                data:{action:'rescheduling_appointment', doctor_schedule_id:doctor_schedule_id},
                success:function(data)
                {
                    $('#appointmentModal').modal('show');
                    $('#hidden_doctor_id').val(doctor_id);
                    $('#hidden_doctor_schedule_id').val(doctor_schedule_id);
                    $('#hidden_appointment_id').val(appointment_id);
                    $('#hidden_appointment_number').val(appointment_number);
                    $('#appointment_detail').html(data);

                    // Add click event handler to time slots
                    $('.time-slot').click(function() {
                        // Check if the slot is available
                        if (!$(this).hasClass('unavailable-time-slot')) {
                            // Remove background color from all time slots
                            $('.time-slot').removeClass('selected-time-slot');
                            // Add background color to the clicked time slot
                            $(this).addClass('selected-time-slot');
                            // Update hidden input field with selected time slot
                            var selectedTimeSlot = $(this).text().trim();
                            $('#new_selected_time_slot').val(selectedTimeSlot);
                        }
                    });

                    // Fetch booked slots for the selected doctor's schedule and mark them as unavailable
                    $.ajax({
                        url: "action.php",
                        method: "POST",
                        data: {action: 'fetch_booked_slots', doctor_schedule_id: doctor_schedule_id},
                        success: function(response) {
                            var bookedSlots = JSON.parse(response);
                            $('.time-slot').each(function() {
                                var timeSlot = $(this).text().trim() + ':00';
                                if (bookedSlots.includes(timeSlot)) {
                                    $(this).addClass('unavailable-time-slot');
                                }
                            });
                        }
                    });

                }
            });
        });

        $.validator.addMethod("customRegex", function(value, element, regex) {
            return regex.test(value);
        }, "Invalid format.");

        $('#appointment_form').validate({
            errorClass: 'err', // Specify the CSS class for error messages
            errorElement: 'span', // Use <span> for error messages

            rules: {
                new_selected_time_slot: { // New validation rule for the time slot
                    required: true
                }
            },
            messages: {
                new_selected_time_slot: {
                    required: "Please click on the time slot"
                }
            },
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            }
        });

        $(document).on('hidden.bs.modal', '#appointmentModal', function () {
            // Reset the form validation when the modal is closed
            $('#appointment_form').validate().resetForm();
            $('#appointment_form').find('.error-message').removeClass('error-message');
        });


        $('#appointment_form').on('submit', function(event){

            event.preventDefault();

            if ($(this).valid()) {
                // Scroll to the error message if it exists
                var errorMessage = $('#form_message');
                if (errorMessage.length > 0) {
                    // Calculate the position of the error message relative to the modal
                    var modal = $('#appointmentModal');
                    var modalTop = modal.offset().top;
                    var errorMessageTop = errorMessage.offset().top;
                    var scrollTo = errorMessageTop - modalTop;

                    // Scroll the modal to the error message
                    modal.animate({
                        scrollTop: scrollTo
                    }, 500);
                }

                if(confirm("This action will force you to reschedule the appointment. Are you sure you want to complete this action?")) {
                    // Serialize form data
                    var formData = $(this).serialize();


                    $.ajax({
                        url:"action.php",
                        method:"POST",
                        data:formData,
                        dataType:"json",
                        beforeSend:function(){
                            $('#submit_button').attr('disabled', 'disabled');
                            $('#submit_button').val('wait...');
                        },
                        success:function(data)
                        {
                            $('#submit_button').attr('disabled', false);
                            $('#submit_button').val('Reschedule');
                            if(data.error != '')
                            {
                                $('#form_message').html(data.error);
                                setTimeout(function(){
                                    $('#form_message').html('');
                                }, 4500);
                            }
                            else
                            {
                                $('#appointmentModal').modal('hide');
                                $('#appointment_message').html(data.success);
                                dataTable.ajax.reload();

                                setTimeout(function(){

                                    $('#appointment_message').html('');

                                }, 4500);
                            }
                        },

                        error: function(xhr, status, error) {
                            console.error(xhr.responseText); // Log any error response for debugging
                            $('#submit_button').attr('disabled', false);
                            $('#submit_button').val('Reschedule');
                            $('#form_message').html('An error occurred while processing your request. Please try again later.');
                        }
                    })
                }
            }
        })



        // Function to remove the appointment message after 2 seconds
        setTimeout(function() {
            var appointmentMessage = $('#appointment_message');
            if (appointmentMessage) {
                appointmentMessage.html(''); // Clear the message
            }
        }, 5000);
    });
</script>




