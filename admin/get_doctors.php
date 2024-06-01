<?php

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
            0 => 'doctor_name',
            1 => 'doctor_status'
        );

// Fetch records
        $query = "SELECT * FROM doctor_table";
        $totalData = mysqli_num_rows(mysqli_query($conn, $query));
        $totalFiltered = $totalData;

// Search condition
        if (!empty($_POST['search']['value'])) {
            $searchValue = $_POST['search']['value'];
            $query .= " WHERE doctor_email_address LIKE '%" . $searchValue . "%' OR doctor_name LIKE '%" . $searchValue . "%' OR doctor_phone_no LIKE '%" . $searchValue . "%' OR doctor_date_of_birth LIKE '%" . $searchValue . "%' OR doctor_degree LIKE '%" . $searchValue . "%'  OR doctor_expert_in LIKE '%" . $searchValue . "%' OR doctor_status LIKE '%" . $searchValue . "%'";
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
            $sub_array = array();
            $sub_array[] = $row["doctor_name"];
            $sub_array[] = '<img src="' . $row["doctor_profile_image"] . '" class="img-thumbnail" width="75" />';
            $sub_array[] = $row["doctor_email_address"];
            $sub_array[] = $row["doctor_phone_no"];
            $sub_array[] = $row["doctor_expert_in"];
            $status = '';
            if ($row["doctor_status"] == 'Active') {
                $status = '<button type="button" name="status_button" class="btn btn-primary btn-sm status_button" data-id="' . $row["doctor_id"] . '" data-status="' . $row["doctor_status"] . '">Active</button>';
            } else {
                $status = '<button type="button" name="status_button" class="btn btn-warning btn-sm status_button" data-id="' . $row["doctor_id"] . '" data-status="' . $row["doctor_status"] . '">On Leave</button>';
            }
            $sub_array[] = $status;
            $sub_array[] = '
			<div align="center">
			<button type="button" name="view_button" class="btn btn-info btn-circle btn-sm view_button" data-id="' . $row["doctor_id"] . '"><i class="fas fa-eye"></i></button>
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="' . $row["doctor_id"] . '"><i class="fas fa-edit"></i></button>
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="' . $row["doctor_id"] . '"><i class="fas fa-times"></i></button>
			</div>
			';
            $data[] = $sub_array;
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
        $query = "SELECT * FROM doctor_table WHERE doctor_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $_POST['doctor_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $data['doctor_email_address'] = $row['doctor_email_address'];
            $data['doctor_name'] = $row['doctor_name'];
            $data['doctor_profile_image'] = $row['doctor_profile_image'];
            $data['doctor_phone_no'] = $row['doctor_phone_no'];
            $data['doctor_address'] = $row['doctor_address'];
            $data['doctor_date_of_birth'] = $row['doctor_date_of_birth'];
            $data['doctor_degree'] = $row['doctor_degree'];
            $data['doctor_expert_in'] = $row['doctor_expert_in'];
        }

        echo json_encode($data);
    }

    //delete
    if ($_POST["action"] == 'delete') {

        $query = "DELETE FROM doctor_table WHERE doctor_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $_POST['id']);
        $id = $_POST['id'];
        if (mysqli_stmt_execute($stmt)) {
            $log_message = "Doctor data with id $id deleted successfully by admin";
            logger($log_message);
            echo '<div class="success-message w-100">Doctor Data Deleted</div>';
        } else {
            // If execution failed, display an error message
            echo '<div class="error-message w-100">Error: Unable to execute statement. ' . mysqli_error($conn) . '</div>';
        }

    }
    //add
    if ($_POST["action"] == 'Add') {
        $error = '';
        $success = '';
        $data = array(
            ':doctor_email_address' => $_POST["doctor_email_address"]
        );

        $email = $data[':doctor_email_address'];


        $stmt = mysqli_prepare($conn, "SELECT * FROM doctor_table WHERE doctor_email_address = ?");
        mysqli_stmt_bind_param($stmt, 's', $data[':doctor_email_address']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error = '<div class="error-message w-100">Email Address Already Exists</div>';
            $log_message = "Adding email address with this $email already exists";
            logger($log_message);
        } else {
            $doctor_profile_image = '';
            if ($_FILES['doctor_profile_image']['name'] != '') {
                $allowed_file_format = array("jpg", "png");

                $file_extension = pathinfo($_FILES["doctor_profile_image"]["name"], PATHINFO_EXTENSION);

                if (!in_array(strtolower($file_extension), $allowed_file_format)) {
                    $error = "<div class='error-message w-100'>Upload valid file. jpg, png</div>";
                } else if (($_FILES["doctor_profile_image"]["size"] > 2000000)) {
                    $error = "<div class='error-message w-100'>File size exceeds 2MB</div>";
                } else {
                    $new_name = rand() . '.' . $file_extension;

                    $destination = '../images/' . $new_name;

                    move_uploaded_file($_FILES['doctor_profile_image']['tmp_name'], $destination);

                    $doctor_profile_image = $destination;
                }
            } else {
                $character = $_POST["doctor_name"][0];
                $path = "../images/" . time() . ".png";
                $image = imagecreate(200, 200);
                $red = rand(0, 255);
                $green = rand(0, 255);
                $blue = rand(0, 255);
                imagecolorallocate($image, 230, 230, 230);
                $textcolor = imagecolorallocate($image, $red, $green, $blue);
                imagettftext($image, 100, 0, 55, 150, $textcolor, '../font/arial.ttf', $character);
                imagepng($image, $path);
                imagedestroy($image);
                $doctor_profile_image = $path;
            }

            if ($error == '') {
                $timezone = new DateTimeZone("Africa/Nairobi");
                $now = new DateTime('now', $timezone);
                $verification_code = rand(100000, 999999);
                $data = array(
                    ':doctor_email_address' => clean_input($_POST["doctor_email_address"]),
                    ':doctor_password' => $_POST["doctor_password"],
                    ':doctor_name' => clean_input($_POST["doctor_name"]),
                    ':doctor_profile_image' => $doctor_profile_image,
                    ':doctor_phone_no' => clean_input($_POST["doctor_phone_no"]),
                    ':doctor_address' => clean_input($_POST["doctor_address"]),
                    ':doctor_date_of_birth' => clean_input($_POST["doctor_date_of_birth"]),
                    ':doctor_degree' => clean_input($_POST["doctor_degree"]),
                    ':doctor_expert_in' => clean_input($_POST["doctor_expert_in"]),
                    ':doctor_status' => 'Active',
                    ':doctor_verification_code' => $verification_code,
                    ':doctor_added_on' => $now->format('Y-m-d H:i:s')
                );

                $passcode = $data[':doctor_password'];
                $doc_email = $data[':doctor_email_address'];
                $passcode_hash = password_hash($data[':doctor_password'], PASSWORD_DEFAULT);

                $query = "INSERT INTO doctor_table (doctor_email_address, doctor_password, doctor_name, doctor_profile_image, doctor_phone_no, doctor_address, doctor_date_of_birth, doctor_degree, doctor_expert_in, doctor_status,doctor_verification_code, doctor_added_on) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'ssssssssssis', $data[':doctor_email_address'], $passcode_hash, $data[':doctor_name'], $data[':doctor_profile_image'], $data[':doctor_phone_no'], $data[':doctor_address'], $data[':doctor_date_of_birth'], $data[':doctor_degree'], $data[':doctor_expert_in'], $data[':doctor_status'], $data[':doctor_verification_code'], $data[':doctor_added_on']);
                if (mysqli_stmt_execute($stmt)) {
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
                        $mail->addAddress($data[':doctor_email_address']); // recipient's email address

                        //Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Email Verification Code';
                        $email_template = "
                              <h3>Your verification code for your email is : $verification_code and your passcode is $passcode.</h3>
                              <br/><br/>
                              <p>Please click on <a href='http://localhost:8080/doctor_appointment_system/admin/password_reset.php'>Forgot Password link</a> in the login section to change to a new password.</p>
                        ";
                        $mail->Body = $email_template;
                        if ($mail->send()) {
                            $log_message = "Doctor with email $doc_email added successfully.";
                            logger($log_message);
                            $success = '<div class="success-message w-100">Doctor data added Successfully</div>';
                        }


                    } catch (Exception $e) {
                        $error = '<div class="error-message w-100">Error sending verification code. Please contact support.</div>';
                    }
                } else {
                    $error = '<div class="error-message w-100">Error in adding doctor data.</div>';
                }
            }
        }
        $output = array(
            'error' => $error,
            'success' => $success
        );

        echo json_encode($output);
    }

    if ($_POST["action"] == 'Edit') {
        $error = '';
        $success = '';
        $data = array(
            ':doctor_email_address' => $_POST["doctor_email_address"],
            ':doctor_id' => $_POST['hidden_id']
        );

        $email = $data[':doctor_email_address'];
        $doc_id = $data[':doctor_id'];

        $stmt = mysqli_prepare($conn, "SELECT * FROM doctor_table WHERE doctor_email_address = ? AND doctor_id != ?");
        mysqli_stmt_bind_param($stmt, 'si', $data[':doctor_email_address'], $data[':doctor_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);


        if (mysqli_num_rows($result) > 0) {
            $error = '<div class="error-message w-100">Email Address Already Exists</div>';
            $log_message = "Updating email address with this $email assigned id $doc_id already exists";
            logger($log_message);
        } else {
            $doctor_profile_image = $_POST["hidden_doctor_profile_image"];

            if ($_FILES['doctor_profile_image']['name'] != '') {
                $allowed_file_format = array("jpg", "png");

                $file_extension = pathinfo($_FILES["doctor_profile_image"]["name"], PATHINFO_EXTENSION);

                if (!in_array(strtolower($file_extension), $allowed_file_format)) {
                    $error = "<div class='error-message w-100'>Upload valid file. jpg, png</div>";
                } else if (($_FILES["doctor_profile_image"]["size"] > 2000000)) {
                    $error = "<div class='error-message w-100'>File size exceeds 2MB</div>";
                } else {
                    $new_name = rand() . '.' . $file_extension;

                    $destination = '../images/' . $new_name;

                    move_uploaded_file($_FILES['doctor_profile_image']['tmp_name'], $destination);

                    $doctor_profile_image = $destination;
                }
            }

            if ($error == '') {
                // Fetch the current email address associated with the doctor ID
                $query = "SELECT doctor_email_address FROM doctor_table WHERE doctor_id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'i', $_POST['hidden_id']);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                $currentEmailAddress = $row['doctor_email_address'];

                $data = array(
                    ':doctor_email_address' => clean_input($_POST["doctor_email_address"]),
                    ':doctor_name' => clean_input($_POST["doctor_name"]),
                    ':doctor_profile_image' => $doctor_profile_image,
                    ':doctor_phone_no' => clean_input($_POST["doctor_phone_no"]),
                    ':doctor_address' => clean_input($_POST["doctor_address"]),
                    ':doctor_date_of_birth' => clean_input($_POST["doctor_date_of_birth"]),
                    ':doctor_degree' => clean_input($_POST["doctor_degree"]),
                    ':doctor_expert_in' => clean_input($_POST["doctor_expert_in"])
                );

                if ($data[':doctor_email_address'] !== $currentEmailAddress) {
                    $verification_code = rand(100, 999);
                    $update_query = "UPDATE doctor_table SET doctor_verification_code = ? WHERE doctor_id = ?";
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
                            $mail->addAddress($data[':doctor_email_address']); // recipient's email address

                            //Content
                            $mail->isHTML(true);
                            $mail->Subject = 'Email Verification Code';
                            $mail->Body = "Your verification code for an email update is : $verification_code";

                            if ($mail->send()) {
                                $query = "UPDATE doctor_table SET doctor_email_address = ? , doctor_verification_code = ? WHERE doctor_id = ?";
                                $stmt = mysqli_prepare($conn, $query);
                                mysqli_stmt_bind_param($stmt, 'sii', $data[':doctor_email_address'], $verification_code, $_POST['hidden_id']);
                                if(mysqli_stmt_execute($stmt)) {
                                    $log_message = "Doctor email address $email assigned id $doc_id was edited by admin";
                                    logger($log_message);
                                    $success = '<div class="success-message w-100">Doctor email field Updated</div>';
                                }
                            } else {
                                $error = '<div class="error-message w-100">Email provided is invalid or does not exist.</div>';
                            }

                        } catch (Exception $e) {
                            $error = '<div class="error-message w-100">Error sending verification code. Please contact support.</div>';
                        }
                    } else {
                        $error = '<div class="error-message w-100">Error in updating your email address. Please try again.</div>';
                    }
                } else {
                    $stmt = mysqli_prepare($conn, "UPDATE doctor_table SET doctor_name = ?, doctor_profile_image = ?, doctor_phone_no = ?, doctor_address = ?, doctor_date_of_birth = ?, doctor_degree = ?, doctor_expert_in = ? WHERE doctor_id = ?");
                    mysqli_stmt_bind_param($stmt, 'sssssssi', $data[':doctor_name'], $data[':doctor_profile_image'], $data[':doctor_phone_no'], $data[':doctor_address'], $data[':doctor_date_of_birth'], $data[':doctor_degree'], $data[':doctor_expert_in'], $_POST['hidden_id']);
                    mysqli_stmt_execute($stmt);

                    $success = '<div class="success-message w-100">Doctor Data Updated</div>';
                }
            }
        }

        $output = array(
            'error' => $error,
            'success' => $success
        );

        echo json_encode($output);
    }

    if ($_POST["action"] == 'change_status') {
        $data = array(
            ':doctor_status' => $_POST['next_status']
        );

        $status = $data[':doctor_status'];
        $stmt = mysqli_prepare($conn, "UPDATE doctor_table SET doctor_status = ? WHERE doctor_id = ?");
        mysqli_stmt_bind_param($stmt, 'si', $data[':doctor_status'], $_POST['id']);
        if (mysqli_stmt_execute($stmt)) {
            $id = $_POST['id'];
            $log_message = "Doctor status with id $id was changed to $status";
            logger($log_message);
            echo '<div class="success-message w-100">Class Status change to ' . $_POST['next_status'] . '</div>';
        }


    }
}




?>

