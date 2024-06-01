<?php
session_start();
include "header.php";
include "connection.php";
include "log_function.php";

$verify_code_ERR = "";

$p_id = $_SESSION['patient_id'];

if (isset($_GET['verify'])) {
    $patient_verification_code = $_GET['patient_verification_code'];

    if(empty($patient_verification_code)) {
        $verify_code_ERR = "Enter the verification code";
    }
    else {
        if(!preg_match("/^\d{4}$/",$patient_verification_code)) {
            $verify_code_ERR = "Verification code must be of 4 digits";
        }
    }
    //if validation passes
    if(empty($verify_code_ERR)) {
        $query = "SELECT * FROM patient_table WHERE patient_verification_code = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt ,'s',$patient_verification_code);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) > 0) {
            $verify_query = "UPDATE patient_table SET patient_email_address = ? WHERE patient_verification_code = ? ";
            $verify_stmt = mysqli_prepare($conn, $verify_query);
            mysqli_stmt_bind_param($verify_stmt, 'ss', $_SESSION['new_email'],$patient_verification_code);
            if(mysqli_stmt_execute($verify_stmt)) {
                // If the update is successful, clear session variables and redirect or display a success message
                unset($_SESSION['patient_email_address']);
                unset($_SESSION['new_email']);
                $log_message = "Patient with id $p_id has successfully updated the email address.";
                logger($log_message);
                echo '<script>
                        alert("Your Email Address has been updated.");
                        window.location.href = "profile.php";
                        </script>';
            }

            else {
                $_SESSION['err'] = "Error while executing the query.";
            }


        }
        else {
            $_SESSION['err'] = "The verification code you entered is wrong. Please confirm in your email.";
            //header("Location:update_patientProfile.php");
            $log_message = "Patient with id $p_id entered the wrong verification code.";
            logger($log_message);
        }
    }
}

//    if (isset($_SESSION['success_message'])) {
//        echo '<div class="success-message"> ' . $_SESSION['success_message'] . '</div>';
//        unset($_SESSION['success_message']);
//    }
if (isset($_SESSION['err'])) {
    echo '<div class="error-message">' . $_SESSION['err'] . '</div>';
    unset($_SESSION['err']); // Clear the message
}
?>

<section class="container">

    <header>Verification Code</header>
    <form action="" method="get" class="form">
        <div class="input-box">
            <label>Please enter the verification code received from your email address <span class="required">*</span></label>
            <input type="text" id="patient_verification_code" name="patient_verification_code" placeholder="Enter verification code" autofocus>
            <div class="error"><?php echo $verify_code_ERR?></div>
        </div>

        <input type="submit" name="verify" id="btn" value="Verify"/>
    </form>
</section>




<?php
include "footer.php";
?>



