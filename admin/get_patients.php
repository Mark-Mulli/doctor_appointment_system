<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//PHPMailer
require "../PHPMailer/src/Exception.php";
require "../PHPMailer/src/PHPMailer.php";
require "../PHPMailer/src/SMTP.php";
// Include database connection
include "connectdb.php";
include "../log_function.php";

function clean_input($string)
{
    $string = trim($string);
    $string = stripslashes($string);
    $string = htmlspecialchars($string);
    return $string;
}


if (isset($_POST["action"])) {
    if ($_POST["action"] == 'fetch') {
        // Define columns
        $columns = array(
            0 => 'patient_first_name',
            1 => 'patient_last_name',
            2 => 'patient_email_address',
            3 => 'patient_phone_no',
            4 => 'email_verify'
        );

// Fetch records
        $query = "SELECT * FROM patient_table";
        $totalData = mysqli_num_rows(mysqli_query($conn, $query));
        $totalFiltered = $totalData;

// Search condition
        if (!empty($_POST['search']['value'])) {
            $searchValue = $_POST['search']['value'];
            $query .= " WHERE patient_first_name LIKE '%" . $searchValue . "%' OR patient_last_name LIKE '%" . $searchValue . "%' OR patient_email_address LIKE '%" . $searchValue . "%' OR patient_phone_no LIKE '%" . $searchValue . "%' OR email_verify LIKE '%" . $searchValue . "%'";
            $totalFiltered = mysqli_num_rows(mysqli_query($conn, $query));
        }

// Order by
        $orderColumn = $columns[$_POST['order'][0]['column']];
        $orderDirection = $_POST['order'][0]['dir'];
        $query .= " ORDER BY " . $orderColumn . " " . $orderDirection;

// Limit
        $start = $_POST['start'];
        $length = $_POST['length'];
        $query .= " LIMIT " . $start . ", " . $length;

// Fetch records again with limit and search conditions
        $result = mysqli_query($conn, $query);

        $data = array();
        while ($row = mysqli_fetch_array($result)) {
            $sub_data = array();
            $sub_data[] = $row["patient_first_name"];
            $sub_data[] = $row["patient_last_name"];
            $sub_data[] = $row["patient_email_address"];
            $sub_data[] = $row["patient_phone_no"];
            $status = '';
            if($row["email_verify"] == 'Yes')
            {
                $status = '<span class="badge badge-success">Yes</span>';
            }
            else
            {
                $status = '<span class="badge badge-danger">No</span>';
            }
            $sub_data[] = $status;
            $sub_data[] = '
			<div align="center">
			<button type="button" name="view_button"  class="btn btn-info btn-circle btn-sm view_button" data-id="'.$row["patient_id"].'"><i class="fa fa-eye"></i></button>
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["patient_id"].'"><i class="fa fa-edit"></i></button>
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["patient_id"].'"><i class="fa fa-times"></i></button>
			</div>
			';
            $data[] = $sub_data;
        }

// Response
        $json_data = array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);
    }

    if ($_POST["action"] == 'fetch_single') {
        $query = "SELECT * FROM patient_table WHERE patient_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $_POST['patient_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $data['patient_email_address'] = $row['patient_email_address'];
            $data['patient_first_name'] = $row['patient_first_name'];
            $data['patient_last_name'] = $row['patient_last_name'];
            $data['patient_date_of_birth'] = $row['patient_date_of_birth'];
            $data['patient_gender'] = $row['patient_gender'];
            $data['patient_address'] = $row['patient_address'];
            $data['patient_postal_code'] = $row['patient_postal_code'];
            $data['patient_phone_no'] = $row['patient_phone_no'];
            $data['email_verify'] = $row['email_verify'] == 'Yes' ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
        }

        echo json_encode($data);
    }

    if($_POST["action"] == 'Edit') {
        $error = '';
        $success = '';
        $data = array(
            ':patient_email_address'    => $_POST["patient_email_address"],
            ':patient_id'               => $_POST['hidden_id']
        );

        $email = $data[':patient_email_address'];
        $pat_id = $data[':patient_id'];

        $stmt = mysqli_prepare($conn, "SELECT * FROM patient_table WHERE patient_email_address = ? AND patient_id != ?");
        mysqli_stmt_bind_param($stmt, 'si', $data[':patient_email_address'], $data[':patient_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);


        if (mysqli_num_rows($result) > 0) {
            $error = '<div class="error-message w-100">Email Address Already Exists</div>';
            $log_message = "Updating patient email address with this $email assigned id $pat_id already exists";
            logger($log_message);
        } else {
            if ($error == '') {
                // Fetch the current email address associated with the patient ID
                $query = "SELECT patient_email_address FROM patient_table WHERE patient_id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'i', $_POST['hidden_id']);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                $currentEmailAddress = $row['patient_email_address'];

                $data = array(
                    ':patient_email_address'        => clean_input($_POST["patient_email_address"]),
                    ':patient_first_name'           => clean_input($_POST["patient_first_name"]),
                    ':patient_last_name'            => clean_input($_POST["patient_last_name"]),
                    ':patient_phone_no'             => clean_input($_POST["patient_phone_no"]),
                    ':patient_address'              => clean_input($_POST["patient_address"]),
                    ':patient_postal_code'          => clean_input($_POST["patient_postal_code"]),
                    ':patient_date_of_birth'        => clean_input($_POST["patient_date_of_birth"]),
                    ':patient_gender'      => clean_input($_POST["patient_gender"])
                );

                if($data[':patient_email_address'] !== $currentEmailAddress ) {
                    $verification_code = rand(100, 999);
                    $update_query  = "UPDATE patient_table SET patient_verification_code = ? WHERE patient_id = ?";
                    $update_verification_stmt = mysqli_prepare($conn, $update_query);
                    mysqli_stmt_bind_param($update_verification_stmt, 'ii', $verification_code, $_POST['hidden_id']);

                    if (mysqli_stmt_execute($update_verification_stmt)) {
                        //send verification code using PhpMailer
                        $mail = new PHPMailer(true);

                        try {
                            //server settings
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com'; // SMTP server
                            $mail->SMTPAuth = true;
                            $mail->Username = 'markmulli001@gmail.com';
                            $mail->Password = 'ruasjdwtqzvhzgnj';
                            $mail->SMTPSecure = 'tls';
                            $mail->Port = 587;

                            //Recipients
                            $mail->setFrom('markmulli001@gmail.com', 'DAMS');
                            $mail->addAddress($data[':patient_email_address']); // recipient's email address

                            //Content
                            $mail->isHTML(true);
                            $mail->Subject = 'Email Verification Code';
                            $mail->Body = "Your verification code for an email update is : $verification_code";



                            if ($mail->send()) {
                                $query = "UPDATE patient_table SET patient_email_address = ? , patient_verification_code = ? WHERE patient_id = ?";
                                $stmt = mysqli_prepare($conn, $query);
                                mysqli_stmt_bind_param($stmt, 'sii', $data[':patient_email_address'], $verification_code, $_POST['hidden_id']);
                                if(mysqli_stmt_execute($stmt)) {
                                    $log_message = "Patient email address $email assigned id $pat_id was edited by admin";
                                    logger($log_message);
                                    $success = '<div class="success-message w-100">Patient email field Updated</div>';
                                }
                            }
                            else {
                                $error = '<div class="error-message w-100">Email provided is invalid or does not exist.</div>';
                            }

                        } catch (Exception $e) {
                            $error = '<div class="error-message w-100">Error sending verification code. Please contact support.</div>';
                        }
                    } else {
                        $error = '<div class="error-message w-100">Error in updating your email address. Please try again.</div>';
                    }

                } else {
                    $stmt = mysqli_prepare($conn, "UPDATE patient_table SET patient_first_name = ?, patient_last_name = ?, patient_date_of_birth = ?, patient_gender = ?, patient_address = ?, patient_postal_code = ?, patient_phone_no = ?  WHERE patient_id = ?");
                    mysqli_stmt_bind_param($stmt, 'sssssssi', $data[':patient_first_name'], $data[':patient_last_name'], $data[':patient_date_of_birth'], $data[':patient_gender'], $data[':patient_address'], $data[':patient_postal_code'], $data[':patient_phone_no'], $_POST['hidden_id']);
                    mysqli_stmt_execute($stmt);

                    $success = '<div class="success-message w-100">Patient Data Updated</div>';

                }
            }
        }

        $output = array(
            'error'     => $error,
            'success'   => $success
        );

        echo json_encode($output);

    }
    if($_POST["action"] == 'delete') {
        $query = "DELETE FROM patient_table WHERE patient_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt,'i', $_POST['id']);
        $id = $_POST['id'];
        if(mysqli_stmt_execute($stmt)) {
            $log_message = "Patient data with id $id deleted successfully by admin";
            logger($log_message);
            echo '<div class="success-message w-100">Patient Data Deleted</div>';
        } else {
            // If execution failed, display an error message
            echo '<div class="error-message w-100">Error: Unable to execute statement. ' . mysqli_error($conn) . '</div>';
        }
    }
}




?>

