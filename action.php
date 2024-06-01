<?php
session_start();
include 'connection.php';
include "log_function.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
//PHPMailer
require "PHPMailer/src/Exception.php";
require "PHPMailer/src/PHPMailer.php";
require "PHPMailer/src/SMTP.php";


function Generate_appointment_no($conn)
{
    // Prepare statement to select the maximum appointment number
    $stmt = mysqli_prepare($conn, "SELECT MAX(appointment_number) as appointment_number FROM appointment_table");
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $appointment_number = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $appointment_number = $row["appointment_number"];
    }

    if ($appointment_number > 0) {
        return $appointment_number + 1;
    } else {
        return '1000';
    }
}

if (isset($_POST["action"])) {
    //check login functionality
    if($_POST["action"] == 'check_login')
    {
        if(isset($_SESSION['patient_id']))
        {
            echo 'dashboard.php';
        }
        else
        {
            echo 'login.php';
        }
    }

    if ($_POST["action"] == 'fetch_schedule') {
        $output = array();

        // Define columns
        $columns = array(
            0 => 'doctor_table.doctor_name',
            1 => 'doctor_table.doctor_degree',
            2 => 'doctor_table.doctor_expert_in',
            3 => 'doctor_schedule_table.doctor_schedule_date',
            4 => 'doctor_schedule_table.doctor_schedule_day',
            5 => 'doctor_schedule_table.doctor_schedule_start_time'
        );

        // Fetch records
        $query = "SELECT * FROM doctor_schedule_table INNER JOIN doctor_table ON doctor_table.doctor_id = doctor_schedule_table.doctor_id";

        // Search condition
        $search_query = '
        WHERE doctor_schedule_table.doctor_schedule_date >= "' . date('Y-m-d') . '" 
        AND doctor_schedule_table.doctor_schedule_status = "Active" 
        AND doctor_table.doctor_status = "Active" 
    ';

        if (isset($_POST["search"]["value"])) {
            $search_value = $_POST["search"]["value"];
            $search_query .= ' AND (doctor_table.doctor_name LIKE "%' . $search_value . '%" ';
            $search_query .= ' OR doctor_table.doctor_degree LIKE "%' . $search_value . '%" ';
            $search_query .= ' OR doctor_table.doctor_expert_in LIKE "%' . $search_value . '%" ';
            $search_query .= ' OR doctor_schedule_table.doctor_schedule_date LIKE "%' . $search_value . '%" ';
            $search_query .= ' OR doctor_schedule_table.doctor_schedule_day LIKE "%' . $search_value . '%" ';
            $search_query .= ' OR doctor_schedule_table.doctor_schedule_start_time LIKE "%' . $search_value . '%") ';
        }


        // Order by
        $order_query = '';
        if (isset($_POST["order"])) {
            $order_column = $columns[$_POST['order'][0]['column']];
            $order_dir = $_POST['order'][0]['dir'];
            $order_query .= ' ORDER BY ' . $order_column . ' ' . $order_dir;
        }

        // Limit
        $limit_query = '';
        if ($_POST["length"] != -1) {
            $limit_query .= ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
        }

        // Final query
        $final_query = $query . $search_query . $order_query . $limit_query;

        // Execute query
        $result = mysqli_query($conn, $final_query);

        // Fetch data
        $data = array();
        while ($row = mysqli_fetch_array($result)) {
            $sub_array = array();
            $sub_array[] = $row["doctor_name"];
            $sub_array[] = $row["doctor_degree"];
            $sub_array[] = $row["doctor_expert_in"];
            $sub_array[] = $row["doctor_schedule_date"];
            $sub_array[] = $row["doctor_schedule_day"];
            $sub_array[] = $row["doctor_schedule_start_time"].' - '. $row["doctor_schedule_end_time"];
            $sub_array[] = '<div align="center"><button type="button" name="get_appointment" class="btn btn-primary btn-sm get_appointment" data-doctor_id="' . $row["doctor_id"] . '" data-doctor_schedule_id="' . $row["doctor_schedule_id"] . '">Get Appointment</button></div>';
            $data[] = $sub_array;
        }

        // Response
        $output = array(
            "draw" => intval($_POST["draw"]),
            "recordsTotal" => intval(mysqli_num_rows(mysqli_query($conn, $query))),
            "recordsFiltered" => intval(mysqli_num_rows(mysqli_query($conn, $query . $search_query))),
            "data" => $data
        );

        echo json_encode($output);
    }

    if($_POST["action"] == 'make_appointment') {
        $patientId = $_SESSION['patient_id'];
        $doctorScheduleId = $_POST["doctor_schedule_id"];

        //patient data
        $stmt = mysqli_prepare($conn, "SELECT * FROM patient_table WHERE patient_id = ?");
        mysqli_stmt_bind_param($stmt,'i',$patientId);
        mysqli_stmt_execute($stmt);
        $patientResult = mysqli_stmt_get_result($stmt);

        //doctor schedule data
        $stmt = mysqli_prepare($conn, "SELECT * FROM doctor_schedule_table INNER JOIN doctor_table ON doctor_table.doctor_id = doctor_schedule_table.doctor_id WHERE doctor_schedule_table.doctor_schedule_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $doctorScheduleId);
        mysqli_stmt_execute($stmt);
        $doctorScheduleResult = mysqli_stmt_get_result($stmt);

        // Fetch booked slots from the database
        $stmt = mysqli_prepare($conn, "SELECT appointment_time FROM appointment_table WHERE doctor_schedule_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $doctorScheduleId);
        mysqli_stmt_execute($stmt);
        $booked_slots_result = mysqli_stmt_get_result($stmt);

        $booked_slots = [];
        while ($row = mysqli_fetch_assoc($booked_slots_result)) {
            $booked_slots[] = $row['appointment_time'];
        }


        // Function to generate time slots
        function generate_time_slots($start_time_str, $end_time_str, $interval, $booked_slots) {
            $start_time = new DateTime($start_time_str); // Convert start time to DateTime object
            $end_time = new DateTime($end_time_str); // Convert end time to DateTime object
            $current_time = clone $start_time; // Clone the start time to avoid modification of original object
            $time_slots = [];

            // Convert end time to DateTime object if it's not already
            if (!($end_time instanceof DateTime)) {
                $end_time = new DateTime($end_time);
            }

            while ($current_time < $end_time) {
                $slot = $current_time->format('H:i');
                $status = in_array($slot, $booked_slots) ? 'red' : 'green';
                $time_slots[$slot] = $status;
                $current_time->add(new DateInterval('PT' . $interval . 'M')); // Add interval
            }
            return $time_slots;
        }

        $html = '<h4 class="text-center">Patient Details</h4>
                <table class="table">';

        while ($patientRow = mysqli_fetch_assoc($patientResult)) {
            $html .= '<tr>
                <th width="40%" class="text-right">Patient Name</th>
                <td>' . $patientRow["patient_first_name"] . ' ' . $patientRow["patient_last_name"] . '</td>
             </tr>
             <tr>
                <th width="40%" class="text-right">Contact No.</th>
                <td>' . $patientRow["patient_phone_no"] . '</td>
             </tr>
             <tr>
                <th width="40%" class="text-right">Address</th>
                <td>' . $patientRow["patient_address"] . '</td>
             </tr>';
        }

        $html .= '</table>
          <hr />
          <h4 class="text-center">Appointment Details</h4>
          <p class="info-message"><span style="color: red">Red slot</span> indicates Booked.<br/> <span style="color: green">Green slot</span> indicates Available.</p>
          <table class="table">';

        while ($doctorScheduleRow = mysqli_fetch_assoc($doctorScheduleResult)) {
            // Generate time slots
            $time_slots = generate_time_slots($doctorScheduleRow["doctor_schedule_start_time"], $doctorScheduleRow["doctor_schedule_end_time"], $doctorScheduleRow["average_consulting_time"], $booked_slots);

            // Generate HTML for time slots representation
            $html .= '<tr>
                    <th width="40%" class="text-right">Doctor Name</th>
                    <td>' . $doctorScheduleRow["doctor_name"] . '</td>
                  </tr>
                  <tr>
                    <th width="40%" class="text-right">Appointment Date</th>
                    <td>' . $doctorScheduleRow["doctor_schedule_date"] . '</td>
                  </tr>
                  <tr>
                    <th width="40%" class="text-right">Appointment Day</th>
                    <td>' . $doctorScheduleRow["doctor_schedule_day"] . '</td>
                  </tr>
                   <tr>
                    <th width="40%" class="text-right">Available Time</th>
                    <td>' . $doctorScheduleRow["doctor_schedule_start_time"] . ' - ' . $doctorScheduleRow["doctor_schedule_end_time"] . '</td>
                  </tr>
                  <tr>
         
                    <th width="40%" class="text-right">Time Slots</th>
                    <td>';
            foreach ($time_slots as $slot => $status) {
                $html .= '<div class="time-slot ' . $status . '">' . $slot . '</div>';
            }
            $html .= '</td>
                  </tr>';
        }
        $html .= '</table>';
        echo $html;
    }

    if ($_POST["action"] == 'book_appointment') {
        $error = '';
        $data = array(
            ':patient_id'			=>	$_SESSION['patient_id'],
            ':doctor_schedule_id'	=>	$_POST['hidden_doctor_schedule_id']
        );

        $stmt = mysqli_prepare($conn, "SELECT * FROM appointment_table WHERE patient_id = ? AND doctor_schedule_id = ?");
        mysqli_stmt_bind_param($stmt,'ii', $data[':patient_id'], $data[':doctor_schedule_id']);
        mysqli_stmt_execute($stmt);

        $p_id = $data[':patient_id'];

        if (mysqli_num_rows(mysqli_stmt_get_result($stmt)) > 0) {
            $error = '<div class="error-message">You have already applied for appointment for this day, try for another day.</div>';
            $log_message = "Patient with id $p_id already applied for appointment for this day";
            logger($log_message);
        } else {
            // Fetch booked slots for the selected doctor's schedule
            $stmt = mysqli_prepare($conn, "SELECT appointment_time FROM appointment_table WHERE doctor_schedule_id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $_POST['hidden_doctor_schedule_id']);
            mysqli_stmt_execute($stmt);
            $booked_slots_result = mysqli_stmt_get_result($stmt);

            $booked_slots = [];
            while ($row = mysqli_fetch_assoc($booked_slots_result)) {
                $booked_slots[] = $row['appointment_time'];
            }
            // Check if the selected appointment time slot is among the booked slots
            $selected_time_slot = $_POST['selected_time_slot'] . ':00';
            if (in_array($selected_time_slot, $booked_slots)) {
                $error = '<div class="error-message">The selected time slot is already booked. Please choose another time slot.</div>';
            } else {
                //get patient email
                $stmt = mysqli_prepare($conn, "SELECT patient_email_address FROM patient_table WHERE patient_id = ?");
                mysqli_stmt_bind_param($stmt, 'i', $_SESSION['patient_id']);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $patient_email_row = mysqli_fetch_assoc($result); // Fetch the row as an associative array
                $patient_email_address = $patient_email_row['patient_email_address']; // Extract email address from the row

                // Fetch doctor schedule data
                $stmt = mysqli_prepare($conn, "SELECT * FROM doctor_schedule_table WHERE doctor_schedule_id = ?");
                mysqli_stmt_bind_param($stmt, 'i', $_POST['hidden_doctor_schedule_id']);
                mysqli_stmt_execute($stmt);
                $scheduleResult = mysqli_stmt_get_result($stmt);

                // Fetch appointment data
                $stmt = mysqli_prepare($conn, "SELECT COUNT(appointment_id) AS total FROM appointment_table WHERE doctor_schedule_id = ?");
                mysqli_stmt_bind_param($stmt, "i", $doctorScheduleId);
                mysqli_stmt_execute($stmt);
                $appointmentResult = mysqli_stmt_get_result($stmt);

                $totalDoctorAvailableMinute = 0;
                $averageConsultingTime = 0;
                $totalAppointment = 0;

                while ($scheduleRow = mysqli_fetch_assoc($scheduleResult)) {
                    $endTime = strtotime($scheduleRow["doctor_schedule_end_time"] . ':00');
                    $startTime = strtotime($selected_time_slot);
                    $totalDoctorAvailableMinute = ($endTime - $startTime) / 60;
                    $averageConsultingTime = $scheduleRow["average_consulting_time"];
                    $app_date = $scheduleRow['doctor_schedule_date'];
                }

                while ($appointmentRow = mysqli_fetch_assoc($appointmentResult)) {
                    $totalAppointment = $appointmentRow["total"];
                }

                $totalAppointmentMinuteUse = $totalAppointment * $averageConsultingTime;
                $appointmentTime = date("H:i", strtotime('+' . $totalAppointmentMinuteUse . ' minutes', $startTime));
                $status = '';

                $appointmentNumber = Generate_appointment_no($conn); // Assuming you have a function for generating appointment numbers

                if ($endTime > strtotime($appointmentTime . ':00')) {
                    $status = 'Booked';
                } else {
                    $status = 'Waiting';
                }

//                // Echo JavaScript code to output values to browser console
//                echo '<script>';
//                echo 'console.log("End Time: ' . strtotime($endTime) . '");';
//                echo 'console.log("Appointment Time: ' . strtotime($appointmentTime . ':00') . '");';
//                echo 'console.log("Status: '.$status.'");';
//                echo '</script>';

                $data = array(
                    'doctor_id' => $_POST['hidden_doctor_id'],
                    'patient_id' => $_SESSION['patient_id'],
                    'doctor_schedule_id' => $_POST['hidden_doctor_schedule_id'],
                    'appointment_number' => $appointmentNumber,
                    'reason_for_appointment' => $_POST['reason_for_appointment'],
                    'appointment_time' => $appointmentTime,
                    'status' => 'Booked'
                );

                $app_time = $data['appointment_time'];
                $doct_id = $data['doctor_id'];

                // Insert appointment
                $stmt = mysqli_prepare($conn, "INSERT INTO appointment_table (doctor_id, patient_id, doctor_schedule_id, appointment_number, reason_for_appointment, appointment_time, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "iiiisss", $data['doctor_id'], $data['patient_id'], $data['doctor_schedule_id'], $data['appointment_number'], $data['reason_for_appointment'], $data['appointment_time'], $data['status']);
                if (mysqli_stmt_execute($stmt)) {
                    //send verification code using PhpMailer
                    $mail = new PHPMailer(true);

                    try {
                        //server settings
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com'; // SMTP server
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'markmulli001@gmail.com';
                        $mail->Password   = 'ruas jdwt qzvh zgnj';
                        $mail->SMTPSecure = 'tls';
                        $mail->Port       = 587;

                        //Recipients
                        $mail->setFrom('markmulli001@gmail.com', 'DAMS');
                        $mail->addAddress($patient_email_address); // recipient's email address

                        //Content
                        $mail->isHTML(true);
                        $mail->Subject = 'APPOINTMENT BOOKING.';
                        $mail->Body    = "Your Appointment has been $status on this date $app_date at this time $app_time with Appointment No. $appointmentNumber.";

                        if($mail->send()) {
                            $log_message = "Patient with id $p_id has successfully booked appointment to this doctor with id $doct_id";
                            logger($log_message);
                        }

                        $_SESSION['appointment_message'] = '<div class="success-message w-100">Your Appointment has been <b>' . $status . '</b> with Appointment No. <b>' . $appointmentNumber . '</b>. Please check your email. </div>';

                    } catch (Exception $e) {
                        $error = '<div class="error-message">Error booking your appointment. Please contact support.</div>';
                    }

                } else {
                    $error = "Query Failed :".mysqli_error($conn);
                }
            }
        }
        echo json_encode(['error' => $error]);
    }


    if ($_POST["action"] == 'fetch_appointment') {
        $output = array();
        // Define columns
        $columns = array(
            0 => 'appointment_table.appointment_number',
            1 => 'doctor_table.doctor_name',
            2 => 'doctor_schedule_table.doctor_schedule_date',
            3 => 'appointment_table.appointment_time',
            4 => 'doctor_schedule_table.doctor_schedule_day',
            5 => 'appointment_table.status'
        );
        // Fetch records
        $query = "SELECT * FROM appointment_table INNER JOIN doctor_table ON doctor_table.doctor_id = appointment_table.doctor_id INNER JOIN doctor_schedule_table ON doctor_schedule_table.doctor_schedule_id = appointment_table.doctor_schedule_id";

        // Search condition
        $search_query = '
        WHERE appointment_table.patient_id = "'.$_SESSION["patient_id"].'"
    ';

        if (isset($_POST["search"]["value"])) {
            $search_value = $_POST["search"]["value"];
            $search_query .= ' AND (appointment_table.appointment_number LIKE "%' . $search_value . '%" ';
            $search_query .= ' OR doctor_table.doctor_name LIKE "%' . $search_value . '%" ';
            $search_query .= ' OR doctor_schedule_table.doctor_schedule_date LIKE "%' . $search_value . '%" ';
            $search_query .= ' OR appointment_table.appointment_time LIKE "%' . $search_value . '%" ';
            $search_query .= ' OR doctor_schedule_table.doctor_schedule_day LIKE "%' . $search_value . '%" ';
            $search_query .= ' OR appointment_table.status LIKE "%' . $search_value . '%") ';
        }

        // Order by
        $order_query = '';
        if (isset($_POST["order"])) {
            $order_column = $columns[$_POST['order'][0]['column']];
            $order_dir = $_POST['order'][0]['dir'];
            $order_query .= ' ORDER BY ' . $order_column . ' ' . $order_dir;
        }

        // Limit
        $limit_query = '';
        if ($_POST["length"] != -1) {
            $limit_query .= ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
        }

        // Final query
        $final_query = $query . $search_query . $order_query . $limit_query;

        // Execute query
        $result = mysqli_query($conn, $final_query);

        // Fetch data
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $sub_array = array();

            $sub_array[] = $row["appointment_number"];
            $sub_array[] = $row["doctor_name"];
            $sub_array[] = $row["doctor_schedule_date"];
            $sub_array[] = $row["appointment_time"];
            $sub_array[] = $row["doctor_schedule_day"];

            $status = '';
            switch ($row["status"]) {
                case 'Booked':
                    $status = '<span class="badge badge-warning">' . $row["status"] . '</span>';
                    break;
                case 'In Process':
                    $status = '<span class="badge badge-primary">' . $row["status"] . '</span>';
                    break;
                case 'Completed':
                    $status = '<span class="badge badge-success">' . $row["status"] . '</span>';
                    break;
                case 'Rescheduled':
                    $status = '<span class="badge badge-danger">' . $row["status"] . '</span>';
                    break;
            }
            $sub_array[] = $status;
            $sub_array[] = '<a href="download.php?id=' . $row["appointment_id"] . '" class="btn btn-danger btn-sm" target="_blank"><i class="fa fa-file-pdf-o"></i> PDF</a>';

            // Disable buttons based on status
            $disable_buttons = ($row["status"] == 'In Process' || $row["status"] == 'Completed') ? 'disabled' : '';
            $sub_array[] = '<button type="button" name="reschedule_appointment" class="btn btn-danger btn-sm reschedule_appointment" data-doctor_id="' . $row["doctor_id"] . '" data-doctor_schedule_id="' . $row["doctor_schedule_id"] . '" data-id="' . $row["appointment_id"] . '" data-appointment_number="' . $row["appointment_number"] . '" ' . $disable_buttons . '><i class="fa fa-calendar"></i></button>';
            $data[] = $sub_array;
        }

        // Response
        $output = array(
            "draw" => intval($_POST["draw"]),
            "recordsTotal" => intval(mysqli_num_rows(mysqli_query($conn, $query))),
            "recordsFiltered" => intval(mysqli_num_rows(mysqli_query($conn, $query . $search_query))),
            "data" => $data
        );

        echo json_encode($output);
    }

    if($_POST["action"] == 'rescheduling_appointment') {
        $patientId = $_SESSION['patient_id'];
        $doctorScheduleId = $_POST["doctor_schedule_id"];

        //patient data
        $stmt = mysqli_prepare($conn, "SELECT * FROM patient_table WHERE patient_id = ?");
        mysqli_stmt_bind_param($stmt,'i',$patientId);
        mysqli_stmt_execute($stmt);
        $patientResult = mysqli_stmt_get_result($stmt);

        //doctor schedule data
        $stmt = mysqli_prepare($conn, "SELECT * FROM doctor_schedule_table INNER JOIN doctor_table ON doctor_table.doctor_id = doctor_schedule_table.doctor_id WHERE doctor_schedule_table.doctor_schedule_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $doctorScheduleId);
        mysqli_stmt_execute($stmt);
        $doctorScheduleResult = mysqli_stmt_get_result($stmt);

        // Fetch booked slots from the database
        $stmt = mysqli_prepare($conn, "SELECT appointment_time FROM appointment_table WHERE doctor_schedule_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $doctorScheduleId);
        mysqli_stmt_execute($stmt);
        $booked_slots_result = mysqli_stmt_get_result($stmt);

        $booked_slots = [];
        while ($row = mysqli_fetch_assoc($booked_slots_result)) {
            $booked_slots[] = $row['appointment_time'];
        }


        // Function to generate time slots
        function generate_time_slots($start_time_str, $end_time_str, $interval, $booked_slots) {
            $start_time = new DateTime($start_time_str); // Convert start time to DateTime object
            $end_time = new DateTime($end_time_str); // Convert end time to DateTime object
            $current_time = clone $start_time; // Clone the start time to avoid modification of original object
            $time_slots = [];

            // Convert end time to DateTime object if it's not already
            if (!($end_time instanceof DateTime)) {
                $end_time = new DateTime($end_time);
            }

            while ($current_time < $end_time) {
                $slot = $current_time->format('H:i');
                $status = in_array($slot, $booked_slots) ? 'red' : 'green';
                $time_slots[$slot] = $status;
                $current_time->add(new DateInterval('PT' . $interval . 'M')); // Add interval
            }
            return $time_slots;
        }

        $html = '<h4 class="text-center">Patient Details</h4>
                <table class="table">';

        while ($patientRow = mysqli_fetch_assoc($patientResult)) {
            $html .= '<tr>
                <th width="40%" class="text-right">Patient Name</th>
                <td>' . $patientRow["patient_first_name"] . ' ' . $patientRow["patient_last_name"] . '</td>
             </tr>
             <tr>
                <th width="40%" class="text-right">Contact No.</th>
                <td>' . $patientRow["patient_phone_no"] . '</td>
             </tr>
             <tr>
                <th width="40%" class="text-right">Address</th>
                <td>' . $patientRow["patient_address"] . '</td>
             </tr>';
        }

        $html .= '</table>
          <hr />
          <h4 class="text-center">Appointment Details</h4>
          <p class="info-message"><span style="color: red">Red slot</span> indicates Booked.<br/> <span style="color: green">Green slot</span> indicates Available.</p>
          <table class="table">';

        while ($doctorScheduleRow = mysqli_fetch_assoc($doctorScheduleResult)) {
            // Generate time slots
            $time_slots = generate_time_slots($doctorScheduleRow["doctor_schedule_start_time"], $doctorScheduleRow["doctor_schedule_end_time"], $doctorScheduleRow["average_consulting_time"], $booked_slots);

            // Generate HTML for time slots representation
            $html .= '<tr>
                    <th width="40%" class="text-right">Doctor Name</th>
                    <td>' . $doctorScheduleRow["doctor_name"] . '</td>
                  </tr>
                  <tr>
                    <th width="40%" class="text-right">Appointment Date</th>
                    <td>' . $doctorScheduleRow["doctor_schedule_date"] . '</td>
                  </tr>
                  <tr>
                    <th width="40%" class="text-right">Appointment Day</th>
                    <td>' . $doctorScheduleRow["doctor_schedule_day"] . '</td>
                  </tr>
                   <tr>
                    <th width="40%" class="text-right">Available Time</th>
                    <td>' . $doctorScheduleRow["doctor_schedule_start_time"] . ' - ' . $doctorScheduleRow["doctor_schedule_end_time"] . '</td>
                  </tr>
                  <tr>
                    <th width="40%" class="text-right">Time Slots</th>
                    <td>';
            foreach ($time_slots as $slot => $status) {
                $html .= '<div class="time-slot ' . $status . '">' . $slot . '</div>';
            }
            $html .= '</td>
                  </tr>';
        }
        $html .= '</table>';
        echo $html;
    }

    if ($_POST["action"] == 'reschedule_appointment') {
        $error = '';
        $success = '';

        $p_id = $_SESSION['patient_id'];
        // Fetch the appointment details including the appointment date
        $stmt = mysqli_prepare($conn, "SELECT appointment_time, doctor_schedule_id FROM appointment_table WHERE appointment_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $_POST['hidden_appointment_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $appointment = mysqli_fetch_assoc($result);

        // Get the appointment date from the doctor schedule
        $stmt = mysqli_prepare($conn, "SELECT doctor_schedule_date, doctor_schedule_start_time FROM doctor_schedule_table WHERE doctor_schedule_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $appointment['doctor_schedule_id']);
        mysqli_stmt_execute($stmt);
        $schedule_result = mysqli_stmt_get_result($stmt);
        $schedule = mysqli_fetch_assoc($schedule_result);

        $date = $schedule['doctor_schedule_date'];

//        // Encode the variables as JSON strings
//        $appointment_json = json_encode($appointment);
//        $schedule_json = json_encode($schedule);
//
//        // Output the JSON strings to the browser console
//        echo "<script>";
//        echo "console.log('Appointment:', $appointment_json);";
//        echo "console.log('Schedule:', $schedule_json);";
//        echo "</script>";

        // Combine the appointment date and time for comparison
        $appointment_datetime = strtotime($schedule['doctor_schedule_date'] . ' ' . $appointment['appointment_time']);
        // Set the timezone to "Africa/Nairobi"
        $timezone = new DateTimeZone('Africa/Nairobi');

        // Create a DateTime object with the current date and time in the specified timezone
        $current_time = new DateTime('now', $timezone);

        // Get the Unix timestamp for the current date and time
        $unix_timestamp = $current_time->getTimestamp();

        $difference_hours = ($appointment_datetime - $unix_timestamp) / (60 * 60);

        // Check if the appointment is within 12 hours
        if ($difference_hours <= 12) {
            // If within 12 hours, return an error message
            $error = '<div class="error-message w-100">You cannot reschedule the appointment before the 12-hour window of the scheduled time.</div>';
            $log_message = "Patient with id $p_id tries to reschedule appointment before 12 hours of scheduled time";
            logger($log_message);
        } else {

            //get patient email
            $stmt = mysqli_prepare($conn, "SELECT patient_email_address FROM patient_table WHERE patient_id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $_SESSION['patient_id']);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $patient_email_row = mysqli_fetch_assoc($result); // Fetch the row as an associative array
            $patient_email_address = $patient_email_row['patient_email_address']; // Extract email address from the row

            // Check if the selected appointment time slot is among the booked slots
            $selected_time_slot = $_POST['new_selected_time_slot'] . ':00';

            $appointTime = strtotime($selected_time_slot);

            $appointmentTime = date('H:i:s', $appointTime);

            $appointmentNumber = $_POST['hidden_appointment_number'];

            //get appointment date


            $data = array(
                'appointment_id' => $_POST['hidden_appointment_id'],
                'appointment_time' => $appointmentTime,
                'status' => 'Rescheduled'
            );

            $id = $data['appointment_id'];
            $time = $data['appointment_time'];
            $status = $data['status'];

            // Update appointment time
            $stmt = mysqli_prepare($conn, "UPDATE appointment_table SET appointment_time = ?, status = ? WHERE appointment_id = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $time, $status, $id);

            if (mysqli_stmt_execute($stmt)) {
                //send verification code using PhpMailer
                $mail = new PHPMailer(true);

                try {
                    //server settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com'; // SMTP server
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'markmulli001@gmail.com';
                    $mail->Password   = 'ruas jdwt qzvh zgnj';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                    //Recipients
                    $mail->setFrom('markmulli001@gmail.com', 'DAMS');
                    $mail->addAddress($patient_email_address); // recipient's email address

                    //Content
                    $mail->isHTML(true);
                    $mail->Subject = 'APPOINTMENT RESCHEDULE.';
                    $mail->Body    = "Your Appointment has been $status to this date $date at this time $time with Appointment No. $appointmentNumber";

                    if($mail->send()) {
                        $log_message = "Patient with id $p_id successfully rescheduled appointment $appointmentNumber.";
                        logger($log_message);
                        $success = '<div class="success-message w-100">Your Appointment has been <b>' . $data['status'] . '</b> with Appointment No. <b>' . $appointmentNumber . '</b>. Please check your email for more information. </div>';
                    }



                } catch (Exception $e) {
                    $error = '<div class="error-message">Error rescheduling your appointment. Please contact support.</div>';
                }
            } else {
                $error = "Query Failed :" . mysqli_error($conn);
            }
        }

        $output = array(
            'error'     => $error,
            'success'   => $success
        );

        echo json_encode($output);

    }

    if ($_POST["action"] == 'fetch_booked_slots') {
        $doctorScheduleId = $_POST["doctor_schedule_id"];

        // Fetch booked slots from the database for the given doctor schedule id
        $stmt = mysqli_prepare($conn, "SELECT appointment_time FROM appointment_table WHERE doctor_schedule_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $doctorScheduleId);
        mysqli_stmt_execute($stmt);
        $booked_slots_result = mysqli_stmt_get_result($stmt);

        $booked_slots = [];
        while ($row = mysqli_fetch_assoc($booked_slots_result)) {
            $booked_slots[] = $row['appointment_time'];
        }

        // Return booked slots data in JSON format
        echo json_encode($booked_slots);
    }

}
