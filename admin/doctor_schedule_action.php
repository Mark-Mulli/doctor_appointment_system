<?php
session_start();

include 'connectdb.php';
include "../log_function.php";

$user_id = $_SESSION['admin_id'];

if (isset($_POST["action"])) {
    if($_POST["action"] == 'fetch') {

        $output = array();

        if($_SESSION['type'] == 'Admin') {
            // Define columns
            $columns = array(
                0 => 'doctor_table.doctor_name',
                1 => 'doctor_schedule_table.doctor_schedule_date',
                2 => 'doctor_schedule_table.doctor_schedule_day',
                3 => 'doctor_schedule_table.doctor_schedule_start_time',
                4 => 'doctor_schedule_table.doctor_schedule_end_time',
                5 => 'doctor_schedule_table.average_consulting_time'
            );

            // Fetch records
            $query = "SELECT * FROM doctor_schedule_table INNER JOIN doctor_table ON doctor_table.doctor_id = doctor_schedule_table.doctor_id ";

        } else {
            $columns = array(
                0 => 'doctor_schedule_date',
                1 => 'doctor_schedule_day',
                2 => 'doctor_schedule_start_time',
                3 => 'doctor_schedule_end_time',
                4 => 'average_consulting_time'
            );

            $query = "SELECT * FROM doctor_schedule_table WHERE doctor_id = '".$_SESSION["admin_id"]."' ";
        }

        // Search condition
        if (!empty($_POST['search']['value'])) {
            $searchValue = $_POST['search']['value'];
            $searchConditions = array();
            foreach ($columns as $column) {
                $searchConditions[] = "$column LIKE '%$searchValue%'";
            }
            $query .= "AND (" . implode(" OR ", $searchConditions) . ")";
        }

        // Order by
        $orderColumnIndex = $_POST['order'][0]['column'];
        $orderColumnName = $columns[$orderColumnIndex];
        $orderDirection = $_POST['order'][0]['dir'];
        $query .= " ORDER BY $orderColumnName $orderDirection";

        // Get total records without filtering
        $totalData = mysqli_num_rows(mysqli_query($conn, $query));

        // Apply limit and offset
        $start = $_POST['start'];
        $length = $_POST['length'];
        $query .= " LIMIT $start, $length";

        // Fetch records
        $result = mysqli_query($conn, $query);
        $data = array();

        while ($row = mysqli_fetch_array($result)) {
            $sub_array = array();
            if ($_SESSION['type'] == 'Admin') {
                $sub_array[] = html_entity_decode($row["doctor_name"]);
            }
            $sub_array[] = $row["doctor_schedule_date"];
            $sub_array[] = $row["doctor_schedule_day"];
            $sub_array[] = $row["doctor_schedule_start_time"];
            $sub_array[] = $row["doctor_schedule_end_time"];
            $sub_array[] = $row["average_consulting_time"] . ' Minutes';
            $status = '';
            if ($row["doctor_schedule_status"] == 'Active') {
                $status = '<button type="button" name="status_button" class="btn btn-primary btn-sm status_button" data-id="'.$row["doctor_schedule_id"].'" data-status="'.$row["doctor_schedule_status"].'">Active</button>';
            } else {
                $status = '<button type="button" name="status_button" class="btn btn-danger btn-sm status_button" data-id="'.$row["doctor_schedule_id"].'" data-status="'.$row["doctor_schedule_status"].'">Inactive</button>';
            }
            $sub_array[] = $status;
            $sub_array[] = '
        <div align="center">
            <button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["doctor_schedule_id"].'"><i class="fas fa-edit"></i></button>
            &nbsp;
            <button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["doctor_schedule_id"].'"><i class="fas fa-times"></i></button>
        </div>';
            $data[] = $sub_array;
        }
        // Prepare output
        $output = array(
            "draw"           => intval($_POST["draw"]),
            "recordsTotal"   => $totalData,
            "recordsFiltered" => $totalData, // Assuming no filtering in this case
            "data"           => $data
        );
        // Output JSON
        echo json_encode($output);
    }

    if ($_POST["action"] == 'Add') {
        $error = '';

        $success = '';

        $doctor_id = '';

        if($_SESSION['type'] == 'Admin')
        {
            $doctor_id = $_POST["doctor_id"];
        }

        if($_SESSION['type'] == 'Doctor')
        {
            $doctor_id = $_SESSION['admin_id'];
        }

        $data = array(
            ':doctor_id'					=>	$doctor_id,
            ':doctor_schedule_date'			=>	$_POST["doctor_schedule_date"],
            ':doctor_schedule_day'			=>	date('l', strtotime($_POST["doctor_schedule_date"])),
            ':doctor_schedule_start_time'	=>	$_POST["doctor_schedule_start_time"],
            ':doctor_schedule_end_time'		=>	$_POST["doctor_schedule_end_time"],
            ':average_consulting_time'		=>	$_POST["average_consulting_time"]
        );

        $stmt = mysqli_prepare($conn, "INSERT INTO doctor_schedule_table (doctor_id, doctor_schedule_date, doctor_schedule_day, doctor_schedule_start_time, doctor_schedule_end_time, average_consulting_time) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'issssi', $data[':doctor_id'], $data[':doctor_schedule_date'], $data[':doctor_schedule_day'], $data[':doctor_schedule_start_time'],$data[':doctor_schedule_end_time'], $data[':average_consulting_time']);
        if (mysqli_stmt_execute($stmt)) {
            $log_message = "Staff with id $user_id added doctor schedule successfully.";
            logger($log_message);
            $success = '<div class="success-message w-100">Doctor Schedule Added Successfully</div>';
        } else {
            echo "Query Failed:".mysqli_error();
        }



        $output = array(
            'error'		=>	$error,
            'success'	=>	$success
        );

        echo json_encode($output);
    }
    if ($_POST["action"] == 'fetch_single') {
        $query = "SELECT * FROM doctor_schedule_table WHERE doctor_schedule_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $_POST['doctor_schedule_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $data['doctor_id'] = $row['doctor_id'];
            $data['doctor_schedule_date'] = $row['doctor_schedule_date'];
            $data['doctor_schedule_start_time'] = $row['doctor_schedule_start_time'];
            $data['doctor_schedule_end_time'] = $row['doctor_schedule_end_time'];
            $data['average_consulting_time'] = $row['average_consulting_time'];
        }
        echo json_encode($data);
    }

    if ($_POST["action"] == 'Edit') {
        $error = '';

        $success = '';

        $doctor_id = '';

        if($_SESSION['type'] == 'Admin')
        {
            $doctor_id = $_POST["doctor_id"];
        }

        if($_SESSION['type'] == 'Doctor')
        {
            $doctor_id = $_SESSION['admin_id'];
        }

        $data = array(
            ':doctor_id'					=>	$doctor_id,
            ':doctor_schedule_date'			=>	$_POST["doctor_schedule_date"],
            ':doctor_schedule_day'			=>	date('l', strtotime($_POST["doctor_schedule_date"])),
            ':doctor_schedule_start_time'	=>	$_POST["doctor_schedule_start_time"],
            ':doctor_schedule_end_time'		=>	$_POST["doctor_schedule_end_time"],
            ':average_consulting_time'		=>	$_POST["average_consulting_time"]
        );

        $id = $_POST['hidden_id'];

        $stmt = mysqli_prepare($conn, "UPDATE doctor_schedule_table SET doctor_id = ?, doctor_schedule_date = ?,doctor_schedule_day = ?, doctor_schedule_start_time = ?, doctor_schedule_end_time = ?, average_consulting_time = ? WHERE doctor_schedule_id = ?");
        mysqli_stmt_bind_param($stmt, 'issssii', $data[':doctor_id'], $data[':doctor_schedule_date'],$data[':doctor_schedule_day'], $data[':doctor_schedule_start_time'], $data[':doctor_schedule_end_time'], $data[':average_consulting_time'], $_POST['hidden_id']);
        if(mysqli_stmt_execute($stmt)) {
            $log_message = "Staff with id $user_id with doctor schedule id $id updated doctor schedule successfully.";
            logger($log_message);
            $success = '<div class="success-message w-100">Doctor Schedule Data Updated Successfully Updated</div>';
        }

        $output = array(
            'error'		=>	$error,
            'success'	=>	$success
        );

        echo json_encode($output);
    }

    if ($_POST["action"] == 'delete') {
        $query = "DELETE FROM doctor_schedule_table WHERE doctor_schedule_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $_POST['id']);
        $id = $_POST['id'];
        if(mysqli_stmt_execute($stmt)) {
            $log_message = "Staff with id $user_id assigned doctor schedule id $id deleted doctor schedule successfully.";
            logger($log_message);
            echo '<div class="success-message w-100">Doctor Schedule has been Deleted</div>';
        } else {
            // If execution failed, display an error message
            echo '<div class="error-message w-100">Error: Unable to execute statement. ' . mysqli_error($conn) . '</div>';
        }
    }

    if ($_POST["action"] == 'change_status') {
        $data = array(
            ':doctor_schedule_status' => $_POST['next_status']
        );

        $stmt = mysqli_prepare($conn, "UPDATE doctor_schedule_table SET doctor_schedule_status = ? WHERE doctor_schedule_id = ?");
        mysqli_stmt_bind_param($stmt, 'si', $data[':doctor_schedule_status'], $_POST['id']);
        $id = $_POST['id'];
        if (mysqli_stmt_execute($stmt)) {
            $log_message = "Staff with id $user_id assigned doctor schedule id $id changed status of doctor schedule.";
            logger($log_message);
            echo '<div class="success-message w-100">Doctor Schedule Status change to ' . $_POST['next_status'] . '</div>';
        }
        else {
            echo "Query Failed:".mysqli_error($conn);
        }


    }



}
