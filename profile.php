<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
//PHPMailer
require "PHPMailer/src/Exception.php";
require "PHPMailer/src/PHPMailer.php";
require "PHPMailer/src/SMTP.php";

include "connection.php";
include "log_function.php";

$currentDate = new DateTime();
$maxDate = $currentDate->modify('-10 years')->format('Y-m-d');

$query = "SELECT * FROM patient_table WHERE patient_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['patient_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach ($rows as $row) {
    $_SESSION['patient_email_address'] = $row['patient_email_address'];
    //echo $_SESSION['patient_email_address'];
}

include "header.php";

$email_ERR = "";
$p_first_ERR = "";
$p_last_ERR = "";
$p_dob_ERR  = "";
$p_gender_ERR = "";
$p_contact_ERR = "";
$p_address_ERR = "";
$p_p_code_ERR = "";

$p_id = $_SESSION['patient_id'];

function clean_input($string)
{
    $string = trim($string);
    $string = stripslashes($string);
    $string = htmlspecialchars($string);
    return $string;
}

if (isset($_POST['edit_profile'])) {
    $patient_email_address = clean_input($_POST['patient_email_address']);
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
        $p_contact_ERR = "It must be in the form of '+2547...' or '07...'01....' followed by 8 digits.";
    }

    // Patient postal address validation
    if (empty($patient_postal_address)) {
        $p_address_ERR = "Patient postal address field is required";
    } elseif (!preg_match("/^\d{0,5}$/", $patient_postal_address)) {
        $p_address_ERR = "Not more than 5 digits";
    }

    // Patient postal code validation
    if (empty($patient_postal_code)) {
        $p_p_code_ERR = "Patient postal code field is required";
    } elseif (!preg_match("/^\d{0,5}$/", $patient_postal_code)) {
        $p_p_code_ERR = "Not more than 5 digits";
    }

    // If all validations pass, proceed with editing
    if (empty($email_ERR) && empty($p_first_ERR) && empty($p_last_ERR) && empty($p_dob_ERR) && empty($p_gender_ERR) && empty($p_contact_ERR) && empty($p_address_ERR) && empty($p_p_code_ERR)) {
        //check email if it is being updated
        if ($patient_email_address !== $_SESSION['patient_email_address']) {

            $patient_verification_code = rand(1000,9999);
            $update_query  = "UPDATE patient_table SET patient_verification_code = ? WHERE patient_id = ?";
            $update_verification_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_verification_stmt, 'ii', $patient_verification_code, $_SESSION['patient_id']);

            if (mysqli_stmt_execute($update_verification_stmt)) {
                //send verification code using PhpMailer
                $mail = new PHPMailer(true);

                try {
                    //server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // SMTP server
                    $mail->SMTPAuth = true;
                    $mail->Username = 'markmulli001@gmail.com';
                    $mail->Password = 'ruasjdwtqzvhzgnj';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    //Recipients
                    $mail->setFrom('markmulli001@gmail.com', 'DAMS');
                    $mail->addAddress($patient_email_address); // recipient's email address

                    //Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Email Verification Code';
                    $mail->Body = "Your verification code for an email update is : $patient_verification_code";

                    if($mail->send()){
                        $_SESSION['new_email'] = $patient_email_address;



                        $log_message = "Patient with email $patient_email_address receives verification code for updating email address";
                        logger($log_message);

                        echo '<script>
                            alert("Seems like you want to update your email address. A verification code has been sent to your new email address. Please check your email.");
                            window.location.href = "update_patientProfile.php";
                            </script>';
                    }

                } catch (Exception $e) {
                    $_SESSION['err'] = "Error sending verification code. Please contact support.";
                    // You can redirect if needed
                    header("Location: profile.php");
                    exit();
                }
            } else {$_SESSION['err'] = "Error in updating your email address. Please try again.";
                $log_mes = "Patient with email $patient_email_address declines sending verification code for updating email address";
                logger($log_mes);
                header("Location:profile.php");
                exit();
            }

        }
        else {
            $log_message = "Patient with id $p_id updates other personal information.";
            logger($log_message);
            performUpdate($conn, $patient_first_name, $patient_last_name, $patient_dob, $patient_gender, $patient_contact_no, $patient_postal_address, $patient_postal_code, $_SESSION['patient_id']);
        }

    }
    else {
        $_SESSION['err'] = "Error in updating your profile. Please confirm your details";
    }
}


// Function to perform the actual update in the database
function performUpdate($conn, $first_name, $last_name, $dob, $gender, $contact_no, $address, $postal_code, $patient_id) {
    $update_query = "UPDATE patient_table SET 
                    patient_first_name = ?,
                    patient_last_name = ?,
                    patient_date_of_birth = ?,
                    patient_gender = ?,
                    patient_phone_no = ?,
                    patient_address = ?,
                    patient_postal_code = ?
                    WHERE patient_id = ?";

    $update_stmt = mysqli_prepare($conn, $update_query);

    mysqli_stmt_bind_param($update_stmt, 'sssssssi',  $first_name, $last_name, $dob, $gender, $contact_no, $address, $postal_code, $patient_id);

    if (mysqli_stmt_execute($update_stmt)) {
        $_SESSION['success'] = "Profile updated successfully.";
        header("Location: profile.php"); // Redirect to the profile page
        exit();
    } else {
        $_SESSION['err'] = "Error in updating your profile. Please try again.";
    }
}

?>

    <div class="container_fluid">
        <?php include "navbar.php";



        if (isset($_SESSION['err'])) {
            echo '<div class="error-message">' . $_SESSION['err'] . '</div>';
            unset($_SESSION['err']); // Clear the message
        } elseif (isset($_SESSION['success'])) {
            echo '<div class="success-message">' .$_SESSION['success'] . '</div>';
            unset($_SESSION['success']); // Clear the message
        }
        ?>



        <!--    display profile-->
        <div class="content-wrapper">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col" id="headerText">Profile Details</div>
                        <div class="col txt-r"><a href="#" class="btn btn-secondary" onclick="toggleEditForm()" >Edit</a></div>
                    </div>
                </div>

                <div class="card-body">

                    <!--    edit profile form-->
                    <form id="editForm" style="display: none;" action="" method="post" class="form">
                        <div class="info-message">To perform an email update, please edit the email field first without changing the other fields.</div>
                        <div class="input-box">
                            <label>Patient Email Address <span class="required">*</span></label>
                            <input type="text" id="patient_email_address" name="patient_email_address"  placeholder="example@gmail.com" autofocus >
                            <div class="error"><?php echo $email_ERR ?></div>
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



                        <input type="submit" name="edit_profile" id="btn" value="Edit"/>

                    </form>


                    <!--                display profile-->
                    <table class="table table-striped" id="profileDetails">
                        <?php
                        foreach ($result as $row) {
                            ?>
                            <tr>
                                <th class="txt-r">Patient Name</th>
                                <td><?php echo $row["patient_first_name"] . ' ' . $row["patient_last_name"]; ?></td>
                            </tr>
                            <tr>
                                <th class="txt-r">Email Address</th>
                                <td><?php echo $row["patient_email_address"]; ?></td>
                            </tr>
                            <tr>
                                <th class="txt-r">Postal Address</th>
                                <td><?php echo $row["patient_address"]; ?></td>
                            </tr>
                            <tr>
                                <th class="txt-r">Postal Code</th>
                                <td><?php echo $row["patient_postal_code"]; ?></td>
                            </tr>
                            <tr>
                                <th class="txt-r">Contact No.</th>
                                <td><?php echo $row["patient_phone_no"]; ?></td>
                            </tr>
                            <tr>
                                <th class="txt-r">Date of Birth</th>
                                <td><?php echo $row["patient_date_of_birth"]; ?></td>
                            </tr>
                            <tr>
                                <th class="txt-r">Gender</th>
                                <td><?php echo $row["patient_gender"]; ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleMenu() {
            var navlinks = document.getElementById("nav-links");
            var menu = document.getElementById("menu-icon");
            var close = document.getElementById("close-icon");
            //var message = document.querySelector(".error-message,.success-message");
            var content = document.querySelector(".content-wrapper");

            if (navlinks.style.left === "0px" || navlinks.style.left === "0%") {
                hideMenu();
            } else {
                showMenu();
            }

            function showMenu() {
                navlinks.style.left = "0";
                menu.style.display = "none";
                close.style.display = "block";
                //message.style.marginTop = "200px";
                content.style.marginTop = "350px";
            }

            function hideMenu() {
                navlinks.style.left = "-300%";
                menu.style.display = "block";
                close.style.display = "none";
                //message.style.marginTop = "1rem";
                content.style.marginTop = "0";
            }
            function hideMenuOnResize() {
                if (window.innerWidth > 909) {
                    hideMenu();
                }
            }
            // Add an event listener to the window resize event
            window.addEventListener('resize', hideMenuOnResize);
        }

        function toggleEditForm() {
            var profileDetails = document.getElementById("profileDetails");
            var editForm = document.getElementById("editForm");
            var headerText = document.getElementById("headerText");

            if (profileDetails.style.display === "" || profileDetails.style.display === "table") {
                profileDetails.style.display = "none";
                editForm.style.display = "block";
                fillEditForm(); // Call a function to fill the edit form with current data
                headerText.innerHTML = "Edit Profile Form"; // Change header text
                document.querySelector(".btn-secondary").innerHTML = "View"; // Change anchor text to 'View'
            } else {
                profileDetails.style.display = "table";
                editForm.style.display = "none";
                headerText.innerHTML = "Profile Details"; // Change header text back to original
                document.querySelector(".btn-secondary").innerHTML = "Edit"; // Change anchor text back to 'Edit'
            }
        }

        function fillEditForm() {
            // Fetch current data and fill the edit form fields
            <?php
            foreach ($result as $row) {
            ?>
            document.getElementById("patient_email_address").value = "<?php echo $row["patient_email_address"]; ?>";
            document.getElementById("patient_first_name").value = "<?php echo $row["patient_first_name"]; ?>";
            document.getElementById("patient_last_name").value = "<?php echo $row["patient_last_name"]; ?>";
            document.getElementById("patient_dob").value = "<?php echo $row["patient_date_of_birth"]; ?>";
            document.getElementById("patient_gender").value = "<?php echo $row["patient_gender"]; ?>";
            document.getElementById("patient_contact_no").value = "<?php echo $row["patient_phone_no"]; ?>";
            document.getElementById("patient_postal_address").value = "<?php echo $row["patient_address"]; ?>";
            document.getElementById("patient_postal_code").value = "<?php echo $row["patient_postal_code"]; ?>";
            <?php
            }
            ?>
        }

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

<?php
include "footer.php";
?>