<?php
session_start();
include ('header.php');
include ('connection.php');

if(isset($_SESSION['patient_id']))
{
    header('location:dashboard.php');
}
$status = 'Active';
$query = "SELECT * FROM doctor_schedule_table INNER JOIN doctor_table ON doctor_table.doctor_id = doctor_schedule_table.doctor_id WHERE doctor_schedule_table.doctor_schedule_date >= '".date('Y-m-d')."' AND doctor_schedule_table.doctor_schedule_status = ? AND doctor_table.doctor_status = ? ORDER BY doctor_schedule_table.doctor_schedule_date ASC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'ss', $status, $status);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);



?>

<div class="container_fluid">
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
                        <?php
                        foreach($result as $row)
                        {
                            ?>
                            <tr>
                                <td><?php echo $row["doctor_name"] ?></td>

                                <td><?php echo $row["doctor_degree"] ?></td>

                                <td><?php echo $row["doctor_expert_in"] ?></td>

                                <td><?php echo  $row["doctor_schedule_date"] ?></td>

                                <td><?php echo  $row["doctor_schedule_day"] ?></td>

                                <td><?php echo $row["doctor_schedule_start_time"].' - '.$row["doctor_schedule_end_time"] ?></td>

                                <td><button type="button" name="get_appointment" class="btn btn-primary btn-sm get_appointment" data-id="<?php echo '.$row["doctor_schedule_id"].'?>">Get Appointment</button></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

</div>



<?php
include ('footer.php');
?>

<script>

    $(document).ready(function(){

        $('#appointment_list_table').DataTable({
            "processing": true,
            "columnDefs": [
                {
                    "targets":[6],
                    "orderable": false // Disable sorting
                }
            ]
        });


        $(document).on('click', '.get_appointment', function(){
            var action = 'check_login';
            var doctor_schedule_id = $(this).data('id');
            $.ajax({
                url:"action.php",
                method:"POST",
                data:{action:action, doctor_schedule_id:doctor_schedule_id},
                success:function(data)
                {
                    window.location.href=data;
                }
            })
        });
    });

</script>
