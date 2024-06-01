<?php
session_start();
include "header.php";
include "connection.php";
include "log_function.php";

$email_ERR = "";
$passcode_ERR = "";
$conf_pass_ERR = "";

function clean_input($string)
{
    $string = trim($string);
    $string = stripslashes($string);
    $string = htmlspecialchars($string);
    return $string;
}

if (isset($_POST['password_update'])) {
    $patient_email_address = clean_input($_POST['patient_email_address']);
    $patient_password = $_POST['new_password'];
    $patient_confirm_password = $_POST['confirm_password'];

    // Email validation
    if (empty($patient_email_address)) {
        $email_ERR = "Email is required";
    } elseif (!filter_var($patient_email_address, FILTER_VALIDATE_EMAIL)) {
        $email_ERR = "Invalid email address";
    }

    // Passcode validation
    if (empty($patient_password)) {
        $passcode_ERR = "Passcode is required";
    } elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&.]{8,16}$/", $patient_password)) {
        $passcode_ERR = "Invalid Passcode. Must contain at least 1 uppercase letter, 1 lowercase letter, 1 digit, 1 special character, and be 8-16 characters long.";
    }

    // Confirm passcode
    if (empty($patient_confirm_password)) {
        $conf_pass_ERR = "Confirm password is required";
    } elseif ($patient_password !== $patient_confirm_password) {
        $conf_pass_ERR = "Password does not match";
    }
    if (empty($email_ERR) && empty($passcode_ERR) && empty($conf_pass_ERR)) {
        //code
        if (isset($_POST['password_update'])) {
            $email = mysqli_real_escape_string($conn, $_POST['patient_email_address']);
            $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
            $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

            $token = mysqli_real_escape_string($conn, $_POST['password_token']);

            if (!empty($token)) {
                $check_token = "SELECT verify_token FROM patient_table WHERE verify_token = '$token' LIMIT 1";
                $check_token_run = mysqli_query($conn, $check_token);

                if (mysqli_num_rows($check_token_run) > 0) {
                    if ($new_password == $confirm_password) {
                        $hash = password_hash($new_password, PASSWORD_DEFAULT);
                        $update_password = "UPDATE patient_table SET patient_password = '$hash' WHERE verify_token ='$token' LIMIT 1";
                        $update_password_run = mysqli_query($conn, $update_password);

                        if ($update_password_run) {
                            $log_message = "$email successfully updated the password";
                            logger($log_message);
                            echo '<script> 
                            alert("New Password Successfully updated. Click OK to login");
                            window.location.href = "login.php";
                            </script>';

                        } else {
                            $_SESSION['error'] = "Did not update password. Something went wrong.";
                        }
                    }

                } else {
                    $_SESSION['error'] = "Invalid Token";
                    $log_message = "$email contains an invalid token. Might have tampered with the URL";
                    logger($log_message);
                }


            } else {
                $_SESSION['error'] = "No token available";
                $log_message = "$email did not receive a token for updating the password";
                logger($log_message);
            }
        }
    }
    // Check if success or error messages are present
    if (isset($_SESSION['status'])) {
        echo '<div class="success-message">' . $_SESSION['status'] . '</div>';
        unset($_SESSION['status']); // Clear the message
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']); // Clear the message
    }
}



?>

<section class="container">
    <header>Reset Password</header>
    <form class="form" id="form" method="post" action="">
        <input type="hidden" value="<?php if(isset($_GET['token'])){echo $_GET['token'];}?>" name="password_token">

        <div class="input-box">
            <label>Patient Email Address <span class="required">*</span></label>
            <input type="text" id="patient_email_address" name="patient_email_address" value="<?php if(isset($_GET['email'])){echo $_GET['email'];}?>" placeholder="example@gmail.com">
            <div class="error"><?php echo $email_ERR ?></div>
        </div>
        <div class="input-box">
            <label>New Password<span class="required">*</span></label>
            <input type="password" id="new_password" name="new_password"  placeholder="Type new passcode" autofocus>
            <div class="error"><?php echo $passcode_ERR ?></div>
        </div>
        <div class="input-box">
            <label>Confirm Password<span class="required">*</span></label>
            <input type="password" id="confirm_password" name="confirm_password"  placeholder="Retype new passcode">
            <div class="error"><?php echo $conf_pass_ERR ?></div>
        </div>
        <input type="submit" name="password_update" id="btn" value="Update Password"/>
    </form>
</section>

<?php
include "footer.php";
?>

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

