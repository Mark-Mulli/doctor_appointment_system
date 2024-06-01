<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
//PHPMailer
require "PHPMailer/src/Exception.php";
require "PHPMailer/src/PHPMailer.php";
require "PHPMailer/src/SMTP.php";


include('connection.php');
include "log_function.php";

if (isset($_POST['register_submit'])) {
    $patient_email_address = $_POST['patient_email_address'];
    $patient_password = $_POST['patient_password'];
    $patient_confirm_password = $_POST['patient_confirm_password'];
    $patient_first_name = $_POST['patient_first_name'];
    $patient_last_name = $_POST['patient_last_name'];
    $patient_dob = $_POST['patient_dob'];
    $patient_gender = $_POST['patient_gender'];
    $patient_contact_no = $_POST['patient_contact_no'];
    $patient_postal_address = $_POST['patient_postal_address'];
    $patient_postal_code = $_POST['patient_postal_code'];

    $timezone = new DateTimeZone("Africa/Nairobi");
    $now = new DateTime('now', $timezone);
    $patient_added_on = $now->format('Y-m-d H:i:s');

    $patient_verification_code = rand(100000,999999);
    $email_verify = "No";


    $query = "select * from patient_table where patient_email_address = ?";
    //prepared statements
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's',$patient_email_address);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 0) {
        // Check hashed password
        if ($patient_password == $patient_confirm_password) {
            $hash = password_hash($patient_password, PASSWORD_DEFAULT);
            $insertQuery = "INSERT INTO patient_table (patient_email_address, patient_password, patient_first_name, patient_last_name, patient_date_of_birth, patient_gender, patient_address, patient_postal_code, patient_phone_no, patient_added_on, patient_verification_code, email_verify)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)";

            $insertStmt = mysqli_prepare($conn, $insertQuery);

            mysqli_stmt_bind_param($insertStmt, 'ssssssssssss', $patient_email_address,$hash,$patient_first_name,$patient_last_name,$patient_dob,$patient_gender,$patient_postal_address,$patient_postal_code,$patient_contact_no,$patient_added_on,$patient_verification_code,$email_verify);
            if (mysqli_stmt_execute($insertStmt)) {
                //send verification code using PhpMailer
                $mail = new PHPMailer(true);

                try {
                    //server settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com'; // SMTP server
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'markmulli001@gmail.com';
                     $mail->Password   = 'ruasjdwtqzvhzgnj';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                    //Recipients
                    $mail->setFrom('markmulli001@gmail.com', 'DAMS');
                    $mail->addAddress($patient_email_address); // recipient's email address

                    //Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Email Verification Code';
                    $mail->Body    = "Your verification code is: $patient_verification_code";

                    if ($mail->send()) {
                        $log_message = "$patient_first_name $patient_last_name registered with email $patient_email_address";
                        logger($log_message);
                        echo '<script> 
                            alert("Registration successful. Verification code sent to your email address.");
                            window.location.href = "verify.php";
                            </script>';
                    }


                } catch (Exception $e) {
                    echo '<div class="error-message">Error sending verification code. Please contact support.</div>';
                    // You can redirect if needed
                    header("Location: register.php");
                }
            }
//            else {
//                echo "Error: " . mysqli_error($conn);
//            }
        }
    }

    else {
        if (mysqli_num_rows($result)>0) {
            echo '<div class="error-message">Email already Exists.</div>';
        }
    }


}


