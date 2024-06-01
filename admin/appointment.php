<?php
include "header.php";
?>

<!--page heading-->
<h1 class="heading"> Appointment Management </h1>

<!--datatable-->
<span id="message"></span>
<div class="card-shadow">
    <div class="card-header">
        <div class="row">
            <div class="col">
                <h6 class="card-header-text">Appointment List</h6>
            </div>
            <div class="col">
                <div class="row">
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col">
                                <input type="date" name="start_date" id="start_date" class="form-control form-control-sm"/>
                            </div>
                            <div class="col">
                                <input type="date" name="end_date" id="end_date" class="form-control form-control-sm"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <button type="button" name="search" id="search" value="Search" class="btn btn-info btn-sm"><i class="fa fa-search"></i></button>
                            &nbsp;<button type="button" name="refresh" id="refresh" class="btn btn-secondary btn-sm"><i class="fa fa-sync-alt"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="appointment_table" width="100%">
                <thead>
                <tr>
                    <th>Appointment No.</th>
                    <th>Patient Name</th>
                    <?php
                    if($_SESSION['type'] == 'Admin')
                    {
                        ?>
                        <th>Doctor Name</th>
                        <?php
                    }
                    ?>
                    <th>Appointment Date</th>
                    <th>Appointment Time</th>
                    <th>Appointment Day</th>
                    <th>Appointment Status</th>
                    <th>View</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<?php
include "footer.php";
?>

<div id="viewModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" id="edit_appointment_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title">View Appointment Details</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="appointment_details"></div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="hidden_appointment_id" id="hidden_appointment_id" />
                    <input type="hidden" name="action" value="change_appointment_status" />
                    <input type="submit" name="save_appointment" id="save_appointment" class="btn btn-primary" value="Save" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        fetch_data('no');

        function fetch_data(is_date_search, start_date='', end_date='')
        {
            var dataTable = $('#appointment_table').DataTable({
                "processing" : true,
                "serverSide" : true,
                "paging": false,
                "ajax" : {
                    "url": "appointment_action.php", // PHP script to fetch data
                    "type": "POST",
                    "data": {is_date_search:is_date_search, start_date:start_date, end_date:end_date, action: 'fetch'}
                },
                "columnDefs":[
                    {
                        <?php
                        if($_SESSION['type'] == 'Admin')
                        {
                        ?>
                        "targets":[7],
                        <?php
                        }
                        else
                        {
                        ?>
                        "targets":[6],
                        <?php
                        }
                        ?>
                        "orderable":false,
                    }
                ]
            });
        }
        $('#search').click(function(){
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            if(start_date !== '' && end_date !== '')
            {
                $('#appointment_table').DataTable().destroy();
                fetch_data('yes', start_date, end_date);
            }
            else
            {
                alert("Both Date is Required");
            }
        });

        $('#refresh').click(function(){
            $('#appointment_table').DataTable().destroy();
            fetch_data('no');
        });
        // Function to set min attribute of end date input field dynamically based on start date selection
        $('#start_date').change(function() {
            var start_date = $(this).val();
            $('#end_date').attr('min', start_date);
        });


        $(document).on('click', '.view_button', function(){

            var appointment_id = $(this).data('id');

            $.ajax({

                url:"appointment_action.php",

                method:"POST",

                data:{appointment_id:appointment_id, action:'fetch_single'},

                success:function(data)
                {
                    $('#viewModal').modal('show');

                    $('#appointment_details').html(data);

                    $('#hidden_appointment_id').val(appointment_id);

                    $('#appointment_status_row').hide();
// Check if appointment status is Completed, and hide the save button if it is
                    if ($('#appointment_status').text().trim() === 'Completed') {
                        $('#save_appointment').hide();
                    } else {
                        $('#save_appointment').show();
                    }
                }

            })
        });
        $('#edit_appointment_form').on('submit', function(event){
            event.preventDefault();

            // Check if all required fields are filled out
            var isValid = true;
            $('#edit_appointment_form [required]').each(function() {
                if ($.trim($(this).val()) == '') {
                    isValid = false;
                    return false; // Exit the loop early if any field is empty
                }
            });

            if (isValid) {
                $.ajax({
                    url: "appointment_action.php",
                    method: "POST",
                    data: $(this).serialize(),
                    beforeSend: function() {
                        $('#save_appointment').attr('disabled', 'disabled');
                        $('#save_appointment').val('wait...');
                    },
                    success: function(data) {
                        $('#save_appointment').attr('disabled', false);
                        $('#save_appointment').val('Save');
                        $('#viewModal').modal('hide');
                        $('#message').html(data);
                        $('#appointment_table').DataTable().destroy();
                        fetch_data('no');
                        setTimeout(function(){
                            $('#message').html('');
                        }, 5000);
                    }
                });
            } else {
                // If any required field is empty, display an error message or handle it as needed
                // Example:
                alert("Please fill out all required fields.");
            }
        });
    })


</script>