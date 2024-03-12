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
                                  0  <?php ?>
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
                                0  <?php ?>
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
                                0  <?php ?>
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
                                0  <?php ?>
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