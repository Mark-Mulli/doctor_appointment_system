<?php
    session_start();
    include "header.php";
?>

    <div class="container_fluid">
        <?php
            include "navbar.php";
        ?>
        <br />
        <div class="content-wrapper">
            <div class="card lg">
                <div class="card-header">
                    <h4>Doctor Schedule List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="appointment_list_table" width="100%">
                            <thead>
                            <tr>
                                <th>Doctor Name</th>
                                <th>Education</th>
                                <th>Speciality</th>
                                <th>Appointment Date</th>
                                <th>Appointment Day</th>
                                <th>Available Time</th>
                                <th>Action</th>
                            </tr>
                            <tbody>

                            </tbody>
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
include "footer.php";
?>

<div id="appointmentModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" id="appointment_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title">Make Appointment</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <span id="form_message"></span>
                    <div id="appointment_detail"></div>

                    <div style="display: flex; justify-content: flex-end;">
                        <!-- Move the input element to the right side -->
                        <input type="time" style="width: 25%;" class="form-control form-control-sm" id="selected_time_slot" name="selected_time_slot" value="" readonly />
                    </div>
                    <div class="err"><span></span></div>
                    <div class="form-group">
                        <label><b>Reason for Appointment</b></label>
                        <textarea name="reason_for_appointment" id="reason_for_appointment" class="form-control" rows="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="hidden_doctor_id" id="hidden_doctor_id" />
                    <input type="hidden" name="hidden_doctor_schedule_id" id="hidden_doctor_schedule_id" />
                    <input type="hidden" name="action" id="action" value="book_appointment" />

                    <input type="submit" name="submit" id="submit_button" class="btn btn-success" value="Book" />
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
                "data": {action: 'fetch_schedule'}
            },
            "columnDefs": [
                {
                    "targets":[6],
                    "orderable": false // Disable sorting
                }
            ]
        });

        $(document).on('click', '.get_appointment', function(){

            var doctor_schedule_id = $(this).data('doctor_schedule_id');
            var doctor_id = $(this).data('doctor_id');

            $.ajax({
                url:"action.php",
                method:"POST",
                data:{action:'make_appointment', doctor_schedule_id:doctor_schedule_id},
                success:function(data)
                {
                    $('#appointmentModal').modal('show');
                    $('#hidden_doctor_id').val(doctor_id);
                    $('#hidden_doctor_schedule_id').val(doctor_schedule_id);
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
                            $('#selected_time_slot').val(selectedTimeSlot);
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
                reason_for_appointment: {
                    required: true,
                    customRegex: /^[a-zA-Z.\s]+$/
                },
                selected_time_slot: { // New validation rule for the time slot
                    required: true
                }
            },
            messages: {
                reason_for_appointment: {
                    required: "Please enter your reason for appointment",
                    customRegex: "Invalid input. Please enter a string containing only letters, dots, and spaces."
                },
                selected_time_slot: {
                    required: "Please click on the time slot"
                }
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") === "selected_time_slot") {
                    // Display error message below the time slot selection area
                    error.appendTo($(".err span"));
                } else {
                    // Display error message below the input field
                    error.insertAfter(element);
                }
            }
        });

        $(document).on('hidden.bs.modal', '#appointmentModal', function () {
            // Reset the form validation when the modal is closed
            $('#appointment_form').validate().resetForm();
            // Clear error styling from input fields
            $('#appointment_form').find('.err').removeClass('err');
            $('#appointment_form').find('.err').css('color', ''); // Clear color
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

                $.ajax({
                    url:"action.php",
                    method:"POST",
                    data:$(this).serialize(),
                    dataType:"json",
                    beforeSend:function(){
                        $('#submit_button').attr('disabled', 'disabled');
                        $('#submit_button').val('wait...');
                    },
                    success:function(data)
                    {
                        $('#submit_button').attr('disabled', false);
                        $('#submit_button').val('Book');
                        if(data.error != '')
                        {
                            $('#form_message').html(data.error);
                            setTimeout(function(){
                                $('#form_message').html('');
                            }, 4500);
                        }
                        else
                        {
                            window.location.href="appointment.php";
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText); // Log any error response for debugging
                        $('#submit_button').attr('disabled', false);
                        $('#submit_button').val('Book');
                        $('#form_message').html('An error occurred while processing your request. Please try again later.');
                    }
                })

            }

        })
    })
</script>


