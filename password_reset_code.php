<?php
include ('connection.php');
include "log_function.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
//PHPMailer
require "PHPMailer/src/Exception.php";
require "PHPMailer/src/PHPMailer.php";
require "PHPMailer/src/SMTP.php";

function send_password_reset($name, $email, $key) {
    //send verification code using PhpMailer
    $mail = new PHPMailer(true);
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
    $mail->addAddress($email); // recipient's email address

    //Content
    $mail->isHTML(true);
    $mail->Subject = 'RESET PASSWORD NOTIFICATION.';

    $email_template = "
          <h2>Hello $name</h2>
          <h3>You are receiving this email because we received a password reset request for your account.</h3>
          <br/><br/>
          <a href='https://3180-41-89-18-2.ngrok-free.app/doctor_appointment_system/password_change.php?token=$key&email=$email' role='link'>Click Here</a>
    ";

    $mail->Body = $email_template;
    $mail->send();
}

if(isset($_POST['password_reset'])) {
    $email = mysqli_real_escape_string($conn, $_POST['patient_email_address']);
    $token = md5(rand());

    $check_email = "SELECT patient_email_address, patient_first_name, patient_last_name  FROM patient_table WHERE patient_email_address = '$email' LIMIT 1";
    $check_email_run = mysqli_query($conn, $check_email);

    if (mysqli_num_rows($check_email_run) > 0) {

        $row = mysqli_fetch_array($check_email_run);
        $get_name = $row['patient_first_name'].' '. $row['patient_last_name'];
        $get_email = $row['patient_email_address'];


        $update_token = "UPDATE patient_table SET verify_token = '$token' WHERE patient_email_address = '$get_email' LIMIT 1";
        $update_token_run = mysqli_query($conn,$update_token);

        if ($update_token_run) {
            send_password_reset($get_name, $get_email, $token);
            $log_message = "$email was sent a password reset link.";
            logger($log_message);
            $_SESSION['status'] = "We e-mailed you a password reset link. Please check";
//            header("Location: password_reset.php");
//            exit(0);
        } else {
            $_SESSION['error'] = "Something went wrong. #1";
//            header("Location: password_reset.php");
//            exit(0);
        }


    } else {
        $_SESSION['error'] = "No email found";
        $log_message = "$email was not found to send a password reset link.";
        logger($log_message);
//        header("Location: password_reset.php");
//        exit(0);
    }
}
?>
