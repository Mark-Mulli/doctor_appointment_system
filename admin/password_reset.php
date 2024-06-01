<?php
session_start();
include 'connectdb.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//PHPMailer
require "../PHPMailer/src/Exception.php";
require "../PHPMailer/src/PHPMailer.php";
require "../PHPMailer/src/SMTP.php";

$email_err = "";

function clean_input($data) {
    $data = htmlspecialchars($data);
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}


if(isset($_POST['password_reset'])) {
    $email = clean_input($_POST['admin_email']);


    if (empty($email)) {
        $email_err = "Please enter your email address";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email address";
    }

    if (empty($email_err) ) {
        function send_password_reset($name, $email, $key)
        {
            //send verification code using PhpMailer
            $mail = new PHPMailer(true);
            //server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'markmulli001@gmail.com';
            $mail->Password = 'ruas jdwt qzvh zgnj';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

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
          <a href='https://3180-41-89-18-2.ngrok-free.app/doctor_appointment_system/admin/password_change.php?token=$key&email=$email' role='link'>Click Here</a>
    ";

            $mail->Body = $email_template;
            $mail->send();
        }

        if (isset($_POST['password_reset'])) {
            $email = mysqli_real_escape_string($conn, $_POST['admin_email']);
            $token = md5(rand());

            $check_email = "SELECT doctor_email_address, doctor_name FROM doctor_table WHERE doctor_email_address = '$email' LIMIT 1";
            $check_email_run = mysqli_query($conn, $check_email);

            if (mysqli_num_rows($check_email_run) > 0) {

                $row = mysqli_fetch_array($check_email_run);
                $get_name = $row['doctor_name'];
                $get_email = $row['doctor_email_address'];


                $update_token = "UPDATE doctor_table SET verify_token = '$token' WHERE doctor_email_address = '$get_email' LIMIT 1";
                $update_token_run = mysqli_query($conn, $update_token);

                if ($update_token_run) {
                    send_password_reset($get_name, $get_email, $token);
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
//        header("Location: password_reset.php");
//        exit(0);
            }
        }
    }



}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="admin_assets/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>DAMS</title>
    <style>
        html,body {
            height:100%;
        }
        body {
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
            justify-content: center;
            text-align: center;
            color: #858796;
        }
        .form-signin {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: auto;
        }
        form .head {
            margin-bottom: 1rem;
            font-size: 1.75rem;
            font-weight: 400;
            line-height: 1.2;
            margin-top: 0;
        }
        .success-message, .error-message, .info-message {
            position: relative;
            padding: 0.75rem 1.25rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
            margin: 1rem auto;
            max-width: 700px;
            width: 100%;
        }
        .error-message {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        form .input {
            margin-bottom: 1rem;
        }
        .input input {
            display: block;
            position: relative;
            height: auto;
            font-size: 16px;
            width: 100%;
            padding: 10px;
            font-weight: 400;
            line-height: 1.5;
            color: #6e707e;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }

        .input input:focus {
            color: #6e707e;
            background-color: #fff;
            border-color: #bac8f3;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(78,115,223,.25);
        }
        .button {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 0.35rem;
            color: #fff;
            background-color: #4e73df;
            border: 1px solid #4e73df;
            text-align: center;
            vertical-align: middle;
            transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .button:hover {
            color: #fff;
            background-color: #2e59d9;
            border-color: #2653d4;
            text-decoration: none;
            cursor: pointer;
        }
        .input .error {
            color: #B94A48;
        }

    </style>
</head>
<body>
<main class="form-signin">
    <form action="" method="post">
        <h1 class="head">Reset Password</h1>
        <?php
        if (isset($_SESSION['status'])) {
            echo '<div class="success-message">' . $_SESSION['status'] . '</div>';
            unset($_SESSION['status']); // Clear the message
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="error-message">' .$_SESSION['error']. '</div>';
            unset($_SESSION['error']);
        }
        ?>
        <div class="input">
            <input type="text" name="admin_email" placeholder="Enter Email Address...">
            <div class="error"><?php echo $email_err?></div>
        </div>
        <div class="input">
            <button type="submit" class="button" name="password_reset">Send Password Reset Link</button>
        </div>
    </form>
</main>

</body>
</html>

<script>
    // Function to hide message after 5 seconds
    function displayMessage() {
        var message = document.querySelector(".error-message,.success-message");
        if (message) {
            setTimeout(function () {
                message.style.display = 'none';
            }, 4500); // 5000 milliseconds (5 seconds)
        }
    }

    // Call the function when the page loads
    window.onload = displayMessage;
</script>

