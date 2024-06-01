<?php
include "header.php";
include "connectdb.php";
include "../log_function.php";

$verify_code_ERR = "";

$id = $_SESSION['admin_id'];

if (isset($_GET['verify'])) {
    $doctor_verification_code = $_GET['doctor_verification_code'];

    if(empty($doctor_verification_code)) {
        $verify_code_ERR = "Enter the verification code";
    }
    else {
        if(!preg_match("/^\d{4}$/",$doctor_verification_code)) {
            $verify_code_ERR = "Verification code must be of 4 digits";
        }
    }
    //if validation passes
    if(empty($verify_code_ERR)) {
        $query = "SELECT * FROM doctor_table WHERE doctor_verification_code = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt ,'s',$doctor_verification_code);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) > 0) {
            $verify_query = "UPDATE doctor_table SET doctor_email_address = ? WHERE doctor_verification_code = ? ";
            $verify_stmt = mysqli_prepare($conn, $verify_query);
            mysqli_stmt_bind_param($verify_stmt, 'ss', $_SESSION['new_email'],$doctor_verification_code);
            if(mysqli_stmt_execute($verify_stmt)) {
                // If the update is successful, clear session variables and redirect or display a success message
                unset($_SESSION['doctor_email_address']);
                unset($_SESSION['new_email']);
                $log_message = "doctor with id $id successfully updates email address.";
                logger($log_message);
                echo '<script>
                        alert("Your Email Address has been updated.");
                        window.location.href = "doctor_profile.php";
                        </script>';
            }

            else {
                $_SESSION['err'] = "Error while executing the query.";
            }


        }
        else {
            $_SESSION['err'] = "The verification code you entered is wrong. Please confirm in your email.";
        }
    }
}

//    if (isset($_SESSION['success_message'])) {
//        echo '<div class="success-message"> ' . $_SESSION['success_message'] . '</div>';
//        unset($_SESSION['success_message']);
//    }

?>

<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800">Verification</h1>

<?php
if (isset($_SESSION['err'])) {
    echo '<div class="error-message">' . $_SESSION['err'] . '</div>';
    unset($_SESSION['err']); // Clear the message
}
?>

<form action="" method="get">
    <div class="row">
        <div class="col-md-6">
            <div class="card-shadow">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-header-text">Verify Email</h6>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                            <label>Please enter the verification code received from your email address <span class="text-danger">*</span></label>
                                            <input type="text" id="doctor_verification_code" name="doctor_verification_code" class="form-control form-control-sm" placeholder="Enter verification code" autofocus>
                                            <div class="err"><?php echo $verify_code_ERR?></div>
                            </div>
                        </div>
                    </div>
                    <input type="submit" name="verify" id="btn" value="Verify"/>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
include "footer.php";
?>



