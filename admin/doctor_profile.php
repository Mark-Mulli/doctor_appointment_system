<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//PHPMailer
require "../PHPMailer/src/Exception.php";
require "../PHPMailer/src/PHPMailer.php";
require "../PHPMailer/src/SMTP.php";

include 'header.php';
include 'connectdb.php';
include "../log_function.php";

$currentDate = new DateTime();
$maxDate = $currentDate->modify('-25 years')->format('Y-m-d');


$doctr_id = $_SESSION['admin_id'];

$query = "SELECT * FROM doctor_table WHERE doctor_id = ?";
$stmt = mysqli_prepare($conn,$query);
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['admin_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
foreach ($rows as $row) {
    $_SESSION['doctor_email_address'] = $row['doctor_email_address'];
}


function clean_input($data) {
    $data = htmlspecialchars($data);
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

$doc_email_addr_err = '';
$doc_name_err = '';
$doc_phone_err = '';
$doc_address_err = '';
$doc_dob_err = '';
$doc_degree_err = '';
$doc_specialty_err = '';
$doc_img_err = '';

if (isset($_POST['edit_button'])) {
    $doc_email = clean_input($_POST['doctor_email_address']);
    $doc_name = clean_input($_POST['doctor_name']);
    $doc_phone = clean_input($_POST['doctor_phone_no']);
    $doc_address = clean_input($_POST['doctor_address']);
    $doc_dob = clean_input($_POST['doctor_date_of_birth']);
    $doc_degree = clean_input($_POST['doctor_degree']);
    $doc_specialty = clean_input($_POST['doctor_expert_in']);
    //validate logic for file upload
    if(isset($_FILES['doctor_profile_image']) && $_FILES['doctor_profile_image']['name'] != '') {
        $doc_img = $_FILES['doctor_profile_image']['name'];
        $doc_img_ext = pathinfo($doc_img, PATHINFO_EXTENSION);
        if (!in_array(strtolower($doc_img_ext), ['jpg', 'jpeg', 'png'])) {
            $doc_img_err = "Image must be in JPG or PNG format.";
        }
    }

    if (empty($doc_email)) {
        $doc_email_addr_err = "Doctor email address field is required";
    } elseif (!filter_var($doc_email, FILTER_VALIDATE_EMAIL)) {
        $doc_email_addr_err = "Enter a valid email";
    }
    if (empty($doc_name)) {
        $doc_name_err = "Doctor name input is required";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/",$doc_name)) {
        $doc_name_err = "Enter a valid name";
    }

    if (empty($doc_phone)) {
        $doc_phone_err = "Enter your contact number";
    } elseif (!preg_match("/^(?:\+254|0)(?:7\d{8}|1\d{8})$/",$doc_phone)) {
        $doc_phone_err = "It must be in the form of '+2547...' or '07...' or '01..' followed by 8 digits.";
    }

    if (empty($doc_address)) {
        $doc_address_err = "Postal address field is required";
    } elseif (!preg_match("/^\d{5}$/",$doc_address)) {
        $doc_address_err = "It should consist of 5 digits";
    }

    if (empty($doc_dob)) {
        $doc_dob_err = "Date of Birth field is required";
    }

    if (empty($doc_degree)) {
        $doc_degree_err = "Please enter a doctor's degree";
    } elseif (!preg_match("/^[a-zA-Z\s()]+$/",$doc_degree)) {
        $doc_degree_err = "Enter a valid doctor's degree";
    }
    if (empty($doc_specialty)) {
        $doc_specialty_err = "Enter area of specialization";
    } elseif (!preg_match("/^[a-zA-Z\s()]+$/",$doc_specialty)) {
        $doc_specialty_err = "Enter a valid area of specialization";
    }

    if (empty($doc_name_err) && empty($doc_name_err) && empty($doc_phone_err) && empty($doc_address_err) && empty($doc_dob_err) && empty($doc_degree_err) && empty($doc_specialty_err)) {
            if ($doc_email !== $_SESSION['doctor_email_address']) {

                $doctor_verification_code = rand(1000,9999);
                $update_query  = "UPDATE doctor_table SET doctor_verification_code = ? WHERE doctor_id = ?";
                $update_verification_stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($update_verification_stmt, 'ii', $doctor_verification_code, $_SESSION['admin_id']);

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
                        $mail->addAddress($doc_email); // recipient's email address

                        //Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Email Verification Code';
                        $mail->Body = "Your verification code for an email update is : $doctor_verification_code";

                        if($mail->send()) {
                            $_SESSION['new_email'] = $doc_email;

                            $log_message = "Doctor with email $doc_email receives verification code for updating email address";
                            logger($log_message);
                            echo '<script>
                            alert("Seems like you want to update your email address. A verification code has been sent to your new email address. Please check your email.");
                            window.location.href = "verify_doctor.php";
                            </script>';
                        }
                    } catch (Exception $e) {
                        $_SESSION['err'] = "Error sending verification code. Please contact support.";
                        // You can redirect if needed
                        header("Location: doctor_profile.php");
                        exit();
                    }
                }
            } else {
                // Directory to store uploaded files
                $target_dir = "../images/";
                $doc_img = $_FILES['doctor_profile_image']['name'];
                // File path where the uploaded file will be stored
                $target_file = $target_dir . basename($doc_img);
                // Move the uploaded file to the specified directory
                if (move_uploaded_file($_FILES["doctor_profile_image"]["tmp_name"], $target_file)) {
                    $update_query = "UPDATE doctor_table SET doctor_name = ?, doctor_profile_image = ?, doctor_phone_no = ?, doctor_address = ?, doctor_date_of_birth = ?, doctor_degree = ?, doctor_expert_in = ?  WHERE doctor_id = ?";
                    $update_stmt = mysqli_prepare($conn, $update_query);
                    mysqli_stmt_bind_param($update_stmt, "sssssssi", $doc_name, $target_file, $doc_phone, $doc_address, $doc_dob, $doc_degree, $doc_specialty, $_SESSION['admin_id']);

                    if (mysqli_stmt_execute($update_stmt)) {
                        $_SESSION['success'] = "Profile updated successfully.";
                        $log_message = "Doctor with id $doctr_id updates personal information successfully.";
                        logger($log_message);
                    } else {
                        $_SESSION['err'] = "Error in updating your profile. Please try again.";
                    }
                }
                else {
                    // If no new file is uploaded, update the profile without changing the picture
                    $update_query = "UPDATE doctor_table SET doctor_name = ?, doctor_phone_no = ?, doctor_address = ?, doctor_date_of_birth = ?, doctor_degree = ?, doctor_expert_in = ?  WHERE doctor_id = ?";
                    $update_stmt = mysqli_prepare($conn, $update_query);
                    mysqli_stmt_bind_param($update_stmt, "ssssssi", $doc_name, $doc_phone, $doc_address, $doc_dob, $doc_degree, $doc_specialty, $_SESSION['admin_id']);

                    if (mysqli_stmt_execute($update_stmt)) {
                        $log_message = "Doctor with id $doctr_id updates personal information successfully.";
                        logger($log_message);
                        $_SESSION['success'] = "Profile updated successfully.";
                    } else {
                        $_SESSION['err'] = "Error in updating your profile. Please try again.";
                    }

                }
            }


    }





}





?>

<!--page heading-->
<h1 class="heading"> Profile </h1>


<?php
if (isset($_SESSION['err'])) {
    echo '<div class="error-message">' . $_SESSION['err'] . '</div>';
    unset($_SESSION['err']); // Clear the message
} elseif (isset($_SESSION['success'])) {
    echo '<div class="success-message">' .$_SESSION['success'] . '</div>';
    unset($_SESSION['success']); // Clear the message
}
?>

<!--profile form-->
<form action="" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="column-10">
            <!--message-->
            <span id="message"></span>
            <div class="card-shadow">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-header-text">Profile</h6>
                        </div>
                        <div class="col" style="text-align: right">
                            <button class="edit-button" name="edit_button" id="edit_button"><i class="fa fa-edit"></i>Edit</button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="info-message w-100">To perform an email update, please edit the email field first without changing the other fields.</div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Doctor Email Address <span class="text-danger">*</span></label>
                                <input type="text" name="doctor_email_address" id="doctor_email_address" class="form-control"/>
                                <div class="err"><?php echo $doc_email_addr_err ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Doctor Name <span class="text-danger">*</span></label>
                                <input type="text" name="doctor_name" id="doctor_name" class="form-control"/>
                                <div class="err"><?php echo $doc_name_err ?></div>
                            </div>
                            <div class="col-md-6">
                                <label>Doctor Phone No. <span class="text-danger">*</span></label>
                                <input type="text" name="doctor_phone_no" id="doctor_phone_no" class="form-control"/>
                                <div class="err"><?php echo $doc_phone_err ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Doctor Address <span class="text-danger">*</span></label>
                                <input type="text" name="doctor_address" id="doctor_address" class="form-control" />
                                <div class="err"><?php echo $doc_address_err ?></div>
                            </div>
                            <div class="col-md-6">
                                <label>Doctor Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="doctor_date_of_birth" id="doctor_date_of_birth" class="form-control" min="1940-01-01" max="<?php echo $maxDate; ?>"/>
                                <div class="err"><?php echo $doc_dob_err ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Doctor Degree <span class="text-danger">*</span></label>
                                <select name="doctor_degree" id="doctor_degree" class="form-control">
                                    <option value="">Select Degree</option>
                                    <option value="Bachelor of Medicine and Bachelor of Surgery (MBBS)">Bachelor of Medicine and Bachelor of Surgery (MBBS)</option>
                                </select>
                                <div class="err"><?php echo $doc_degree_err ?></div>
                            </div>
                            <div class="col-md-6">
                                <label>Doctor Speciality <span class="text-danger">*</span></label>
                                <select name="doctor_expert_in" id="doctor_expert_in" class="form-control">
                                    <option value="">Select Speciality</option>
                                    <option value="Anatomical Pathology">Anatomical Pathology</option>
                                    <option value="Anesthesiology">Anesthesiology</option>
                                    <option value="Cardiology">Cardiology</option>
                                    <option value="Cardiovascular and Thoracic Surgery">Cardiovascular and Thoracic Surgery</option>
                                    <option value="Clinical Immunology or Allergy">Clinical Immunology or Allergy</option>
                                    <option value="Critical Care Medicine">Critical Care Medicine</option>
                                    <option value="Dermatology">Dermatology</option>
                                    <option value="Diagnostic Radiology">Diagnostic Radiology</option>
                                    <option value="Emergency Medicine">Emergency Medicine</option>
                                    <option value="Endocrinology and Metabolism">Endocrinology and Metabolism</option>
                                    <option value="Family Medicine">Family Medicine</option>
                                    <option value="Gastroenterology">Gastroenterology</option>
                                    <option value="General Internal Medicine">General Internal Medicine</option>
                                    <option value="General Surgery">General Surgery</option>
                                    <option value="General or Clinical Pathology">General or Clinical Pathology</option>
                                    <option value="Geriatric Medicine">Geriatric Medicine</option>
                                    <option value="Hematology">Hematology</option>
                                    <option value="Medical Biochemistry">Medical Biochemistry</option>
                                    <option value="Medical Genetics">Medical Genetics</option>
                                    <option value="Medical Microbiology and Infectious Diseases">Medical Microbiology and Infectious Diseases</option>
                                    <option value="Medical Oncology">Medical Oncology</option>
                                    <option value="Nephrology">Nephrology</option>
                                    <option value="Neurology">Neurology</option>
                                    <option value="Neurosurgery">Neurosurgery</option>
                                    <option value="Nuclear Medicine">Nuclear Medicine</option>
                                    <option value="Obstetrics or Gynecology">Obstetrics or Gynecology</option>
                                    <option value="Occupational Medicine">Occupational Medicine</option>
                                    <option value="Ophthalmology">Ophthalmology</option>
                                    <option value="Orthopedic Surgery">Orthopedic Surgery</option>
                                    <option value="Otolaryngology">Otolaryngology</option>
                                    <option value="Pediatrics">Pediatrics</option>
                                    <option value="Physical Medicine and Rehabilitation (PM and R)">Physical Medicine and Rehabilitation (PM and R)</option>
                                    <option value="Plastic Surgery">Plastic Surgery</option>
                                    <option value="Psychiatry">Psychiatry</option>
                                    <option value="Public Health and Preventive Medicine (PhPm)">Public Health and Preventive Medicine (PhPm)</option>
                                    <option value="Radiation Oncology">Radiation Oncology</option>
                                    <option value="Respirology">Respirology</option>
                                    <option value="Rheumatology">Rheumatology</option>
                                    <option value="Urology">Urology</option>
                                </select>
                                <div class="err"><?php echo $doc_specialty_err ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="input-group d">
                        <label>Doctor Image <span class="text-danger">*</span></label>
                        <br />
                        <div style="display: flex">
                            <input type="file" name="doctor_profile_image" id="doctor_profile_image"/>
                            <div id="uploaded_image"></div>
                        </div>
                        <div class="err"><?php echo $doc_img_err ?></div>
                        <input type="hidden" name="doctor_profile_image" id="hidden_doctor_profile_image" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        <?php foreach($result as $row): ?>
        document.getElementById('doctor_email_address').value = "<?php echo $row['doctor_email_address']; ?>";
        document.getElementById('doctor_name').value = "<?php echo $row['doctor_name']; ?>";
        document.getElementById('doctor_phone_no').value = "<?php echo $row['doctor_phone_no']; ?>";
        document.getElementById('doctor_address').value = "<?php echo $row['doctor_address']; ?>";
        document.getElementById('doctor_date_of_birth').value = "<?php echo $row['doctor_date_of_birth']; ?>";
        document.getElementById('doctor_degree').value = "<?php echo $row['doctor_degree']; ?>";
        document.getElementById('doctor_expert_in').value = "<?php echo $row['doctor_expert_in']; ?>";

        <?php if($row['doctor_profile_image'] != ''): ?>
        document.getElementById("uploaded_image").innerHTML = "<img src='<?php echo $row['doctor_profile_image']; ?>' class='img-thumbnail' width='100' /><input type='hidden' name='doctor_profile_image' value='<?php echo $row['doctor_profile_image']; ?>' />";
        <?php else: ?>
        document.getElementById("uploaded_image").innerHTML = "<input type='hidden' name='hidden_doctor_profile_image' value='' />";
        <?php endif; ?>
        <?php endforeach; ?>
    });

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
include 'footer.php'
?>
