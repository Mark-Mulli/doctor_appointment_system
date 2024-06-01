<?php
session_start();
    include "connection.php";
    include "header.php";

$currentDate = new DateTime();
$maxDate = $currentDate->modify('-10 years')->format('Y-m-d');

$email_ERR = "";
$passcode_ERR = "";
$conf_pass_ERR = "";
$p_first_ERR = "";
$p_last_ERR = "";
$p_dob_ERR  = "";
$p_gender_ERR = "";
$p_contact_ERR = "";
$p_address_ERR = "";
$p_p_code_ERR = "";

function clean_input($string)
{
    $string = trim($string);
    $string = stripslashes($string);
    $string = htmlspecialchars($string);
    return $string;
}

if (isset($_POST['register_submit'])) {
    $patient_email_address = clean_input($_POST['patient_email_address']);
    $patient_password = $_POST['patient_password'];
    $patient_confirm_password = $_POST['patient_confirm_password'];
    $patient_first_name = clean_input($_POST['patient_first_name']);
    $patient_last_name = clean_input($_POST['patient_last_name']);
    $patient_dob = clean_input($_POST['patient_dob']);
    $patient_gender = clean_input($_POST['patient_gender']);
    $patient_contact_no = clean_input($_POST['patient_contact_no']);
    $patient_postal_address = clean_input($_POST['patient_postal_address']);
    $patient_postal_code = clean_input($_POST['patient_postal_code']);

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

    // Patient first name validation
    if (empty($patient_first_name)) {
        $p_first_ERR = "First name field is required";
    } elseif (!preg_match("/^[a-zA-Z]+$/", $patient_first_name)) {
        $p_first_ERR = "Enter a valid first name.";
    }

    // Patient last name validation
    if (empty($patient_last_name)) {
        $p_last_ERR = "Last name field is required";
    } elseif (!preg_match("/^[a-zA-Z]+$/", $patient_last_name)) {
        $p_last_ERR = "Enter a valid last name.";
    }

    // Patient dob validation
    if (empty($patient_dob)) {
        $p_dob_ERR = "Date of Birth field is required";
    }

    // Patient gender validation
    if (empty($patient_gender)) {
        $p_gender_ERR = "Patient gender field is required";
    }

    // Patient contact number validation
    if (empty($patient_contact_no)) {
        $p_contact_ERR = "Patient contact number field is required";
    } elseif (!preg_match("/^(?:\+254|0)(?:7\d{8}|1\d{8})$/", $patient_contact_no)) {
        $p_contact_ERR = "It must be in the form of '+2547...' or '07...' or '01...' followed by 8 digits.";
    }

    // Patient postal address validation
    if (empty($patient_postal_address)) {
        $p_address_ERR = "Patient postal address field is required";
    } elseif (!preg_match("/^\d{5}$/", $patient_postal_address)) {
        $p_address_ERR = "Must be of 5 digits";
    }

    // Patient postal code validation
    if (empty($patient_postal_code)) {
        $p_p_code_ERR = "Patient postal code field is required";
    } elseif (!preg_match("/^\d{5}$/", $patient_postal_code)) {
        $p_p_code_ERR = "Must be of 5 digits";
    }

    // If all validations pass, proceed with registration
    if (empty($email_ERR) && empty($passcode_ERR) && empty($conf_pass_ERR) && empty($p_first_ERR) && empty($p_last_ERR) && empty($p_dob_ERR) && empty($p_gender_ERR) && empty($p_contact_ERR) && empty($p_address_ERR) && empty($p_p_code_ERR)) {
        include "signup.php";
    }
}


// Check if success or error messages are present
//if (isset($_SESSION['success_message'])) {
//    echo '<div class="success-message">' . $_SESSION['success_message'] . '</div>';
//    unset($_SESSION['success_message']); // Clear the message
//}
if (isset($_SESSION['error_message'])) {
    echo '<div class="error-message">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']); // Clear the message
}
?>

<section class="container">
    <header>Register</header>
    <form action="" method="post" class="form">
        <div class="input-box">
            <label>Patient Email Address <span class="required">*</span></label>
            <input type="text" id="patient_email_address" name="patient_email_address"  placeholder="example@gmail.com" autofocus >
            <div class="error"><?php echo $email_ERR ?></div>
        </div>
        <div class="input-box">
            <label>Patient Password <span class="required">*</span></label>
            <input type="password" id="patient_password" name="patient_password" placeholder="Type passcode" >
            <div class="error"><?php echo $passcode_ERR ?></div>
        </div>
        <div class="input-box">
            <label>Confirm Password <span class="required">*</span></label>
            <input type="password" id="patient_confirm_password" name="patient_confirm_password" placeholder="Retype passcode" >
            <div class="error"><?php echo $conf_pass_ERR ?></div>
        </div>

        <div class="column">
            <div class="input-box">
                <label>Patient First Name <span class="required">*</span></label>
                <input type="text" id="patient_first_name" name="patient_first_name" placeholder="John" >
                <div class="error"><?php echo $p_first_ERR ?></div>
            </div>
            <div class="input-box">
                <label>Patient Last Name <span class="required">*</span></label>
                <input type="text" id="patient_last_name" name="patient_last_name" placeholder="Doe" >
                <div class="error"><?php echo $p_last_ERR ?></div>
            </div>
        </div>
        <div class="column">
            <div class="input-box">
                <label>Patient Date of Birth <span class="required">*</span></label>
                <input type="date" id="patient_dob" name="patient_dob" min="1940-01-01" max="<?php echo $maxDate; ?>" >
                <div class="error"><?php echo $p_dob_ERR ?></div>
            </div>
            <div class="input-box">
                <label>Patient Gender <span class="required">*</span></label>
                <select id="patient_gender" name="patient_gender">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <div class="error"><?php echo $p_gender_ERR ?></div>
            </div>
        </div>

        <div class="input-box">
            <label>Patient Contact No. <span class="required">*</span></label>
            <input type="text" id="patient_contact_no" name="patient_contact_no" placeholder="0712345678" >
            <div class="error"><?php echo $p_contact_ERR ?></div>
        </div>
        <div class="input-box">
            <label>Patient Postal Address <span class="required">*</span></label>
            <input type="text" id="patient_postal_address" name="patient_postal_address" placeholder="Enter Street Address" >
            <div class="error"><?php echo $p_address_ERR ?></div>
        </div>
        <div class="input-box">
            <label>Patient Postal Code <span class="required">*</span></label>
            <input type="text" id="patient_postal_code" name="patient_postal_code" placeholder="Enter Postal Code" >
            <div class="error"><?php echo $p_p_code_ERR ?></div>
        </div>



        <input type="submit" name="register_submit" id="btn" value="Register"/>

        <p>
            <a href="login.php">Login</a>
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

