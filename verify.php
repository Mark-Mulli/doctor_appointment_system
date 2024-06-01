<?php
session_start();
    include "header.php";
    include "connection.php";
    include "log_function.php";

    $verify_code_ERR = "";

    if (isset($_GET['verify'])) {
        $patient_verification_code = $_GET['patient_verification_code'];

        if(empty($patient_verification_code)) {
            $verify_code_ERR = "Enter the verification code";
        }
        else {
            if(!preg_match("/^\d{6}$/",$patient_verification_code)) {
                $verify_code_ERR = "Verification code must be of 6 digits";
            }
        }
        //if validation passes
        if(empty($verify_code_ERR)) {
            $query = "SELECT * FROM patient_table WHERE patient_verification_code = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt ,'s',$patient_verification_code);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            $patient_email = $row['patient_email_address'];

            if(mysqli_num_rows($result) > 0) {
                $email_verify = "Yes";
                $verify_query = "UPDATE patient_table SET email_verify = ? WHERE patient_verification_code = ? ";
                $verify_stmt = mysqli_prepare($conn, $verify_query);
                mysqli_stmt_bind_param($verify_stmt, 'ss', $email_verify,$patient_verification_code);
                if(mysqli_stmt_execute($verify_stmt)) {
                    $log_message = "$patient_email verified email address for login.";
                    logger($log_message);
                    echo '<script>
                        alert("Your Email has been verified, now you can log in into system.");
                        window.location.href = "login.php";
                        </script>';
                }
            }
            else {
                echo '<div class="error-message">The verification code you entered is wrong. Please confirm in your email.</div>';
                $log_message = "$patient_email entered the incorrect verification code";
                logger($log_message);
                //header("Location:verify.php");
            }
        }
    }

//    if (isset($_SESSION['success_message'])) {
//        echo '<div class="success-message"> ' . $_SESSION['success_message'] . '</div>';
//        unset($_SESSION['success_message']);
//    }
    if (isset($_SESSION['error_message'])) {
        echo '<div class="error-message"> ' . $_SESSION['error_message'] . '</div>';
        unset($_SESSION['error_message']);
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

<script>
    // Function to hide message after 5 seconds
    function displayMessage() {
        var message = document.querySelector(".error-message,.success-message");
        if (message) {
            setTimeout(function () {
                message.style.display = 'none';
            }, 1500); // 5000 milliseconds (5 seconds)
        }
    }

    // Call the function when the page loads
    window.onload = displayMessage;
</script>


