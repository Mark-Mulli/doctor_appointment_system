<?php
include 'connection.php';
require_once 'class/pdf.php';

if (isset($_GET['id'])) {
    $html = '<div style="text-align: center;">'; // Center align the content

    $stmt = mysqli_prepare($conn, "SELECT hospital_name, hospital_address, hospital_contact_no, hospital_logo FROM admin_table");
    mysqli_stmt_execute($stmt);
    $hospital_data = mysqli_stmt_get_result($stmt);

    while ($hospital_row = mysqli_fetch_assoc($hospital_data)) {
        $html .= '<img src="' . substr($hospital_row['hospital_logo'], 3) . '" /><br />';
        $html .= '<h2>' . $hospital_row['hospital_name'] . '</h2>
		<p>' . $hospital_row['hospital_address'] . '</p>
		<p><b>Contact No. - </b>' . $hospital_row['hospital_contact_no'] . '</p>';
    }
    $html .= '<hr />';

    $stmt = mysqli_prepare($conn, "SELECT * FROM appointment_table WHERE appointment_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $_GET["id"]);
    mysqli_stmt_execute($stmt);
    $appointment_data = mysqli_stmt_get_result($stmt);

    while ($appointment_row = mysqli_fetch_assoc($appointment_data)) {

        $stmt = mysqli_prepare($conn, "SELECT * FROM patient_table WHERE patient_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $appointment_row["patient_id"]);
        mysqli_stmt_execute($stmt);
        $patient_data = mysqli_stmt_get_result($stmt);

        $stmt = mysqli_prepare($conn, "SELECT * FROM doctor_schedule_table INNER JOIN doctor_table ON doctor_table.doctor_id = doctor_schedule_table.doctor_id WHERE doctor_schedule_table.doctor_schedule_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $appointment_row["doctor_schedule_id"]);
        mysqli_stmt_execute($stmt);
        $doctor_schedule_data = mysqli_stmt_get_result($stmt);

        while ($patient_row = mysqli_fetch_assoc($patient_data)) {
            $html .= '
            <h4>Patient Details</h4>
            <p><b>Patient Name:</b> ' . $patient_row["patient_first_name"] . ' ' . $patient_row["patient_last_name"] . '</p>
			<p><b>Contact Number:</b> ' . $patient_row["patient_phone_no"] . '</p>
			<p><b>Postal Address:</b> ' . $patient_row["patient_address"] . '</p>
			<p><b>Postal Code:</b> ' . $patient_row["patient_postal_code"] . '</p>';

        }

        $html .= '<hr />
		<h4>Appointment Details</h4>
		<table border="0" cellpadding="5" cellspacing="5" width="100%">
		';

        while ($doctor_schedule_row = mysqli_fetch_assoc($doctor_schedule_data)) {
            $html .= '
			<tr>
				<th align="right" width="50%">Doctor Name</th>
				<td>' . $doctor_schedule_row["doctor_name"] . '</td>
			</tr>
			<tr>
				<th align="right" width="50%">Appointment Date</th>
				<td>' . $doctor_schedule_row["doctor_schedule_date"] . '</td>
			</tr>
			<tr>
				<th align="right" width="50%">Appointment Day</th>
				<td>' . $doctor_schedule_row["doctor_schedule_day"] . '</td>
			</tr>
				
			';
        }
        $html .= '
			<tr>
				<th align="right" width="50%">Appointment Time</th>
				<td>' . $appointment_row["appointment_time"] . '</td>
			</tr>
			<tr>
				<th align="right" width="50%">Reason for Appointment</th>
				<td>' . $appointment_row["reason_for_appointment"] . '</td>
			</tr>
			<tr>
				<th align="right" width="50%">Patient come into Hospital</th>
				<td>' . $appointment_row["patient_come_into_hospital"] . '</td>
			</tr>
			<tr>
				<th align="right" width="50%">Doctor Comment</th>
				<td>' . $appointment_row["doctor_comment"] . '</td>
			</tr>
		</table>
		';
    }
    $html .= '</div>';

    echo $html;

    $pdf = new Pdf();

    $pdf->loadHtml($html, 'UTF-8');
    $pdf->render();
    ob_end_clean();
    $pdf->stream($_GET["id"] . '.pdf', array('Attachment' => false));
    exit(0);
}
?>
