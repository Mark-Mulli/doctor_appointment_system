<?php

include 'connectdb.php';

function get_total_patient($conn) {
    $query = "SELECT * FROM patient_table";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $num_result = mysqli_num_rows($result);
    return $num_result;
}

function get_total_appointment($conn) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM appointment_table");
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $num_result = mysqli_num_rows($result);
    return $num_result;
}

function get_total_seven_day_appointment($conn) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM appointment_table INNER JOIN doctor_schedule_table ON doctor_schedule_table.doctor_schedule_id = appointment_table.doctor_schedule_id WHERE doctor_schedule_date >= DATE(NOW()) - INTERVAL 7 DAY");
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $num_result = mysqli_num_rows($result);
    return $num_result;
}

function get_total_yesterday_appointment($conn) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM appointment_table INNER JOIN doctor_schedule_table ON doctor_schedule_table.doctor_schedule_id = appointment_table.doctor_schedule_id WHERE doctor_schedule_date = CURDATE() - 1");
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $num_result = mysqli_num_rows($result);
    return $num_result;
}

function get_total_today_appointment($conn) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM appointment_table INNER JOIN doctor_schedule_table ON doctor_schedule_table.doctor_schedule_id = appointment_table.doctor_schedule_id WHERE doctor_schedule_date = CURDATE() ");
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $num_result = mysqli_num_rows($result);
    return $num_result;
}



include 'header.php';


?>


    <!--page heading-->
    <h1 class="heading"> Dashboard </h1>

    <div class="row">
        <div class="column">
            <div class="card border-left h-100">
                <div class="card-body">
                    <div class="no-gutters">
                        <div class="top-row">
                            <div class="card-text">
                                Today's Total Appointments
                            </div>
                            <div class="number">
                                  <?php echo get_total_today_appointment($conn)?>
                            </div>
                        </div>
                        <div class="bottom-row">
                            <i class="fa fa-clipboard-list">

                            </i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="column">
            <div class="card border-left h-100">
                <div class="card-body">
                    <div class="no-gutters">
                        <div class="top-row">
                            <div class="card-text">
                                Yesterday's Total Appointments
                            </div>
                            <div class="number">
                                <?php echo get_total_yesterday_appointment($conn)?>
                            </div>
                        </div>
                        <div class="bottom-row">
                            <i class="fa fa-clipboard-list">

                            </i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="column">
            <div class="card border-left h-100">
                <div class="card-body">
                    <div class="no-gutters">
                        <div class="top-row">
                            <div class="card-text">
                                Last 7 Days Total Appointments
                            </div>
                            <div class="number">
                                <?php echo  get_total_seven_day_appointment($conn)?>
                            </div>
                        </div>
                        <div class="bottom-row">
                            <i class="fa fa-clipboard-list">

                            </i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="column">
            <div class="card border-left h-100">
                <div class="card-body">
                    <div class="no-gutters">
                        <div class="top-row">
                            <div class="card-text">
                                Total Appointments till date
                            </div>
                            <div class="number">
                                 <?php echo get_total_appointment($conn)?>
                            </div>
                        </div>
                        <div class="bottom-row">
                            <i class="fa fa-clipboard-list">

                            </i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="column">
            <div class="card border-left h-100">
                <div class="card-body">
                    <div class="no-gutters">
                        <div class="top-row">
                            <div class="card-text">
                                Total Registered Patients
                            </div>
                            <div class="number">
                                 <?php echo get_total_patient($conn) ?>
                            </div>
                        </div>
                        <div class="bottom-row">
                            <i class="fa fa-clipboard-list">

                            </i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>




<?php
include "footer.php";
?>