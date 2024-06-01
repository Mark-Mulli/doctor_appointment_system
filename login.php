<?php
    session_start();
    include "connection.php";
    include "header.php";
    include "log_function.php";

function clean_input($string)
{
    $string = trim($string);
    $string = stripslashes($string);
    $string = htmlspecialchars($string);
    return $string;
}

$email_ERR = "";
$passcode_ERR = "";

if (isset($_POST['login_submit'])) {
    $patient_email_address = clean_input($_POST['patient_email_address']);
    $patient_password = $_POST['patient_password'];

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

    if(empty($email_ERR) && empty($passcode_ERR)) {
        $query = "SELECT * FROM patient_table WHERE patient_email_address = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $patient_email_address);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if( mysqli_num_rows($result) > 0) {

            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

            foreach ($rows as $row) {

                if ($row['email_verify'] == "Yes") {
                    // Verify the password using password_verify
                    if (password_verify($patient_password, $row['patient_password'])) {
                        // Login successful
                        $_SESSION['patient_id'] = $row['patient_id'];
                        $_SESSION['patient_name'] = $row['patient_first_name'] . ' ' . $row['patient_last_name'];

                        $log_message = "$patient_email_address successfully logged in the system.";
                        logger($log_message);

                        // Redirect to a welcome page or perform any other desired actions
                        header("Location: dashboard.php");
                        exit();
                    }
                    else {
                        $_SESSION['error'] = "Invalid password";

                        $log_message = "$patient_email_address failed to login due to invalid passcode.";
                        logger($log_message);
                    }
                }
                else {
                    $_SESSION['error'] = "Please first verify your email address";

                    $log_message = "$patient_email_address did not verify email address while trying to login.";
                    logger($log_message);
                }
            }
        }
        else {
            $_SESSION['error'] = "Wrong Email Address";

            $log_message = "$patient_email_address entered wrong email address that does not exist in the database.";
            logger($log_message);
        }



        mysqli_stmt_close($stmt);

    }
}


if (isset($_SESSION['error'])) {
    echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}


?>

<section class="container">
    <header>Login</header>
    <form class="form" id="form" method="post" action="">
        <div class="input-box">
            <label>Patient Email Address <span class="required">*</span></label>
            <input type="text" id="patient_email_address" name="patient_email_address"  placeholder="example@gmail.com" autofocus>
            <div class="error"><?php echo $email_ERR ?></div>
        </div>
        <div class="input-box">
            <label>Patient Password <span class="required">*</span></label>
            <input type="password" id="patient_password" name="patient_password" placeholder="Type passcode">
            <div class="error"><?php echo $passcode_ERR ?></div>
        </div>
        <input type="submit" name="login_submit" id="btn" value="Login"/>
        <p style="display: flex; justify-content: space-between;">
            <a href="register.php">Register</a>
            <a href="password_reset.php" style="text-align: right">Forgot Password?</a>
        </p>
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
