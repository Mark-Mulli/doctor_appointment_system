<?php
session_start();
include('header.php');
include ('connection.php');

function clean_input($string)
{
    $string = trim($string);
    $string = stripslashes($string);
    $string = htmlspecialchars($string);
    return $string;
}

$email_ERR = "";

if (isset($_POST['password_reset'])) {
    $patient_email_address = clean_input($_POST['patient_email_address']);

    // Email validation
    if (empty($patient_email_address)) {
        $email_ERR = "Email is required";
    } elseif (!filter_var($patient_email_address, FILTER_VALIDATE_EMAIL)) {
        $email_ERR = "Invalid email address";
    }

    if (empty($email_ERR)) {
        include "password_reset_code.php";
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
        <div class="input-box">
            <label>Patient Email Address <span class="required">*</span></label>
            <input type="text" id="patient_email_address" name="patient_email_address"  placeholder="example@gmail.com" autofocus>
            <div class="error"><?php echo $email_ERR ?></div>
        </div>
        <input type="submit" name="password_reset" id="btn" value="Send Password Reset Link"/>
    </form>
</section>

<?php
include ('footer.php');
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
