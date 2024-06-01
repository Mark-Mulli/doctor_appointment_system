<?php
session_start();
include 'connectdb.php';
include "../log_function.php";

$doc_id = $_SESSION['admin_id'];

if(isset($_POST["action"])) {
    if($_POST["action"] == 'fetch') {
        $output = array();

        if($_SESSION['type'] == 'Admin') {
            $order_column = array('appointment_table.appointment_number', 'patient_table.patient_first_name', 'doctor_table.doctor_name', 'doctor_schedule_table.doctor_schedule_date', 'appointment_table.appointment_time', 'doctor_schedule_table.doctor_schedule_day', 'appointment_table.status');

            $main_query = "SELECT * FROM appointment_table INNER JOIN doctor_table ON doctor_table.doctor_id = appointment_table.doctor_id INNER JOIN doctor_schedule_table ON doctor_schedule_table.doctor_schedule_id = appointment_table.doctor_schedule_id INNER JOIN patient_table ON patient_table.patient_id = appointment_table.patient_id";

            $search_query = '';

            if($_POST["is_date_search"] == "yes") {
                $search_query .= ' WHERE doctor_schedule_table.doctor_schedule_date BETWEEN ? AND ? AND (';
                $params = array($_POST["start_date"], $_POST["end_date"]);
            } else {
                $search_query .= ' WHERE ';
                $params = array();
            }

            if(isset($_POST["search"]["value"])) {
                $search_query .= 'appointment_table.appointment_number LIKE ? ';
                $search_query .= 'OR patient_table.patient_first_name LIKE ? ';
                $search_query .= 'OR patient_table.patient_last_name LIKE ? ';
                $search_query .= 'OR doctor_table.doctor_name LIKE ? ';
                $search_query .= 'OR doctor_schedule_table.doctor_schedule_date LIKE ? ';
                $search_query .= 'OR appointment_table.appointment_time LIKE ? ';
                $search_query .= 'OR doctor_schedule_table.doctor_schedule_day LIKE ? ';
                $search_query .= 'OR appointment_table.status LIKE ? ';
                $params = array_merge($params, array_fill(0, 8, '%' . $_POST["search"]["value"] . '%'));
            }

            if($_POST["is_date_search"] == "yes") {
                $search_query .= ') ';
            } else {
                $search_query .= '';
            }
        } else {
            $order_column = array('appointment_table.appointment_number', 'patient_table.patient_first_name', 'doctor_schedule_table.doctor_schedule_date', 'appointment_table.appointment_time', 'doctor_schedule_table.doctor_schedule_day', 'appointment_table.status');

            $main_query = "SELECT * FROM appointment_table INNER JOIN doctor_schedule_table ON doctor_schedule_table.doctor_schedule_id = appointment_table.doctor_schedule_id INNER JOIN patient_table ON patient_table.patient_id = appointment_table.patient_id";

            $search_query = ' WHERE appointment_table.doctor_id = ?';

            $params = array($_SESSION["admin_id"]);

            if($_POST["is_date_search"] == "yes") {
                $search_query .= ' AND doctor_schedule_table.doctor_schedule_date BETWEEN ? AND ?';
                $params[] = $_POST["start_date"];
                $params[] = $_POST["end_date"];
            } else {
                $search_query .= '';
            }
            if(isset($_POST["search"]["value"])) {
                $search_query .= ' AND (appointment_table.appointment_number LIKE ? ';
                $search_query .= 'OR patient_table.patient_first_name LIKE ? ';
                $search_query .= 'OR patient_table.patient_last_name LIKE ? ';
                $search_query .= 'OR doctor_schedule_table.doctor_schedule_date LIKE ? ';
                $search_query .= 'OR appointment_table.appointment_time LIKE ? ';
                $search_query .= 'OR doctor_schedule_table.doctor_schedule_day LIKE ? ';
                $search_query .= 'OR appointment_table.status LIKE ?) ';
                $params = array_merge($params, array_fill(0, 7, '%' . $_POST["search"]["value"] . '%'));
            }
        }

        $db = mysqli_connect("localhost", "root", "", "E-DOC");
        if(!$db) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $stmt = mysqli_stmt_init($db);

        if(isset($_POST["order"])) {
            $order_query = ' ORDER BY ' . $order_column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'];
        } else {
            $order_query = ' ORDER BY appointment_table.appointment_id DESC';
        }

        $limit_query = '';

        if($_POST["length"] != -1) {
            $limit_query .= ' LIMIT ?, ?';
            $params[] = intval($_POST['start']);
            $params[] = intval($_POST['length']);
        }

        $sql = $main_query . $search_query . $order_query . $limit_query;

        $stmt = mysqli_prepare($db, $sql);

        if($stmt) {
            if(count($params) > 0) {
                $types = str_repeat("s", count($params));
                mysqli_stmt_bind_param($stmt, $types, ...$params);
            }

            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);


            $data = array();

            while($row = mysqli_fetch_assoc($result)) {
                $sub_array = array();
                $sub_array[] = $row["appointment_number"];
                $sub_array[] = $row["patient_first_name"] . ' ' . $row["patient_last_name"];

                if($_SESSION['type'] == 'Admin') {
                    $sub_array[] = $row["doctor_name"];
                }
                $sub_array[] = $row["doctor_schedule_date"];
                $sub_array[] = $row["appointment_time"];
                $sub_array[] = $row["doctor_schedule_day"];
                $status = '';

                switch($row["status"]) {
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
                $sub_array[] = '<div align="center">
                            <button type="button" name="view_button" class="btn btn-info btn-circle btn-sm view_button" data-id="' . $row["appointment_id"] . '"><i class="fas fa-eye"></i></button>
                            </div>';
                $data[] = $sub_array;
            }

            $output = array(
                "draw"            =>  intval($_POST["draw"]),
                "recordsTotal"    =>  mysqli_num_rows($result),
                "recordsFiltered" =>  mysqli_num_rows($result),
                "data"            =>  $data
            );

            echo json_encode($output);
        }
        mysqli_stmt_close($stmt);
        mysqli_close($db);
    }

    if($_POST["action"] == 'fetch_single') {
        $stmt = mysqli_prepare($conn, "SELECT * FROM appointment_table WHERE appointment_id = ?");
        mysqli_stmt_bind_param($stmt,'i', $_POST["appointment_id"]);
        mysqli_stmt_execute($stmt);
        $appointment_data = mysqli_stmt_get_result($stmt);

        while($appointment_row = mysqli_fetch_assoc($appointment_data)) {
            $stmt = mysqli_prepare($conn, "SELECT * FROM patient_table WHERE patient_id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $appointment_row["patient_id"]);
            mysqli_stmt_execute($stmt);
            $patient_data = mysqli_stmt_get_result($stmt);

            $stmt = mysqli_prepare($conn, "SELECT * FROM doctor_schedule_table INNER JOIN doctor_table ON doctor_table.doctor_id = doctor_schedule_table.doctor_id WHERE doctor_schedule_table.doctor_schedule_id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $appointment_row["doctor_schedule_id"]);
            mysqli_stmt_execute($stmt);
            $doctor_schedule_data = mysqli_stmt_get_result($stmt);

            $html = '
			<h4 class="text-center">Patient Details</h4>
			<table class="table">
			';

            while ($patient_row = mysqli_fetch_assoc($patient_data)) {
                $html .= '
				<tr>
					<th width="40%" class="text-right">Patient Name</th>
					<td>' . $patient_row["patient_first_name"] . ' ' . $patient_row["patient_last_name"] . '</td>
				</tr>
				<tr>
					<th width="40%" class="text-right">Contact No.</th>
					<td>' . $patient_row["patient_phone_no"] . '</td>
				</tr>
				<tr>
					<th width="40%" class="text-right">Address</th>
					<td>' . $patient_row["patient_address"] . '</td>
				</tr>
				';
            }
            $html .= '
			</table>
			<hr />
			<h4 class="text-center">Appointment Details</h4>
			<table class="table">
				<tr>
					<th width="40%" class="text-right">Appointment No.</th>
					<td>' . $appointment_row["appointment_number"] . '</td>
				</tr>
			';
            while ($doctor_schedule_row = mysqli_fetch_assoc($doctor_schedule_data)) {

                $html .= '
				<tr>
					<th width="40%" class="text-right">Doctor Name</th>
					<td>' . $doctor_schedule_row["doctor_name"] . '</td>
				</tr>
				<tr>
					<th width="40%" class="text-right">Appointment Date</th>
					<td>' . $doctor_schedule_row["doctor_schedule_date"] . '</td>
				</tr>
				<tr>
					<th width="40%" class="text-right">Appointment Day</th>
					<td>' . $doctor_schedule_row["doctor_schedule_day"] . '</td>
				</tr>
				
				';
            }
            $html .= '
				<tr>
					<th width="40%" class="text-right">Appointment Time</th>
					<td>' . $appointment_row["appointment_time"] . '</td>
				</tr>
				<tr>
					<th width="40%" class="text-right">Reason for Appointment</th>
					<td>' . $appointment_row["reason_for_appointment"] . '</td>
				</tr>
			';
            $html .= '
        <tr id="appointment_status_row">
            <th width="40%" class="text-right">Appointment Status</th>
            <td id="appointment_status">' . $appointment_row["status"] . '</td>
        </tr>
';
            if ($appointment_row["status"] != '') {
                if ($_SESSION['type'] == 'Admin') {
                    if ($appointment_row['patient_come_into_hospital'] == 'Yes') {
                        if ($appointment_row["status"] == 'Completed') {
                            $html .= '
								<tr>
									<th width="40%" class="text-right">Patient come into Hospital</th>
									<td>Yes</td>
								</tr>
								<tr>
									<th width="40%" class="text-right">Doctor Comment</th>
									<td>' . $appointment_row["doctor_comment"] . '</td>
								</tr>
							';
                        } else {
                            $html .= '
								<tr>
									<th width="40%" class="text-right">Patient come into Hospital</th>
									<td>
										<select name="patient_come_into_hospital" id="patient_come_into_hospital" class="form-control" required>
											<option value="">Select</option>
											<option value="Yes" selected>Yes</option>
										</select>
									</td>
								</tr
							';
                        }
                    } else {
                        $html .= '
							<tr>
								<th width="40%" class="text-right">Patient come into Hospital</th>
								<td>
									<select name="patient_come_into_hospital" id="patient_come_into_hospital" class="form-control" required>
										<option value="">Select</option>
										<option value="Yes">Yes</option>
									</select>
								</td>
							</tr
						';
                    }
                }

                if ($_SESSION['type'] == 'Doctor') {
                    if ($appointment_row["patient_come_into_hospital"] == 'Yes') {
                        if ($appointment_row["status"] == 'In Process') {
                            $html .= '
								<tr>
									<th width="40%" class="text-right">Doctor Comment</th>
									<td>
										<textarea name="doctor_comment" id="doctor_comment" class="form-control" rows="8" required>' . $appointment_row["doctor_comment"] . '</textarea>
									</td>
								</tr
							';
                        } else {
                            $html .= '
								<tr>
									<th width="40%" class="text-right">Doctor Comment</th>
									<td>' . $appointment_row["doctor_comment"] . '</td>
								</tr
							';
                        }
                    }
                }
            }

            $html .= '
			</table>
			';
        }
        echo $html;
    }

    if ($_POST["action"] == 'change_appointment_status') {
        if($_SESSION['type'] == 'Admin') {
            $data = array(
                'status'							=>	'In Process',
                'patient_come_into_hospital'		=>	'Yes',
                'appointment_id'					=>	$_POST['hidden_appointment_id']
            );

            $id = $data['appointment_id'];

            $stmt = mysqli_prepare($conn, "UPDATE appointment_table SET status = ?, patient_come_into_hospital = ? WHERE appointment_id = ?");
            mysqli_stmt_bind_param($stmt,'ssi', $data['status'],$data['patient_come_into_hospital'],$data['appointment_id']);
            if (mysqli_stmt_execute($stmt)) {
                $log_message = "Appointment with id $id changed status to In Process by admin";
                logger($log_message);
                echo '<div class="success-message w-100">Appointment Status change to In Process</div>';
            }


        }
        if($_SESSION['type'] == 'Doctor') {
            if(isset($_POST['doctor_comment'])) {
                $data = array(
                    'status'							=>	'Completed',
                    'doctor_comment'					=>	$_POST['doctor_comment'],
                    'appointment_id'					=>	$_POST['hidden_appointment_id']
                );
                $id = $data['appointment_id'];
                $stmt = mysqli_prepare($conn, "UPDATE appointment_table SET status = ?, doctor_comment = ? WHERE appointment_id = ?");
                mysqli_stmt_bind_param($stmt,'ssi', $data['status'],$data['doctor_comment'],$data['appointment_id']);
                if (mysqli_stmt_execute($stmt)) {
                    $log_message = "Appointment with id $id changed status to completed by doctor with id $doc_id";
                    logger($log_message);
                    echo '<div class="success-message w-100">Appointment Completed</div>';
                }
            }
        }
    }


}
