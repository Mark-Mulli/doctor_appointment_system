<?php
    include 'header.php';
    include 'connectdb.php';
    include "../log_function.php";


    $query = "SELECT * FROM admin_table WHERE admin_id = ?";
    $stmt = mysqli_prepare($conn,$query);
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['admin_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);


    function clean_input($data) {
        $data = htmlspecialchars($data);
        $data = trim($data);
        $data = stripslashes($data);
        return $data;
    }

    //validation of the fields
    $name_err = '';
    $email_address_err = '';
    $Hosp_name_err = '';
    $Hosp_address_err = '';
    $Hosp_contact_err = '';
    $Hosp_logo_err = '';

    if(isset($_POST['edit_button'])) {
        $admin_name = clean_input($_POST['admin_name']);
        $admin_email_address = clean_input($_POST['admin_email_address']);
        $hospital_name = clean_input($_POST['hospital_name']);
        $hospital_address = clean_input($_POST['hospital_address']);
        $hospital_contact_no = clean_input($_POST['hospital_contact_no']);
        //validate logic for file upload
        if(isset($_FILES['hospital_logo']) && $_FILES['hospital_logo']['name'] != '') {
            $hospital_logo = $_FILES['hospital_logo']['name'];
            $hospital_logo_ext = pathinfo($hospital_logo, PATHINFO_EXTENSION);
            if (!in_array($hospital_logo_ext, ['jpg', 'jpeg', 'png'])) {
                $Hosp_logo_err = "Hospital logo must be in JPG or PNG format.";
            }
        }

        //validation logic
        if (empty($admin_name)) {
            $name_err = "Admin Name is required";
        } elseif (!preg_match("/^[a-zA-Z\s]+$/",$admin_name)) {
            $name_err = "Enter a valid name";
        }

        if (empty($admin_email_address)) {
            $email_address_err = "Admin email is required";
        } elseif (!filter_var($admin_email_address, FILTER_VALIDATE_EMAIL)) {
            $email_address_err = "Enter a valid email";
        }

        if (empty($hospital_name)) {
            $Hosp_name_err = "Hospital name field is required";
        } elseif (!preg_match("/^[\w\s]+$/",$hospital_name)) {
            $Hosp_name_err = "Please enter a valid Hospital name consisting of alphanumeric characters and/or spaces.";
        }

        if (empty($hospital_address)) {
            $Hosp_address_err = "Hospital address field required";
        } elseif (!preg_match("/^\d+\s*,\s*\w+\s+\w+,\s*\w+\s+\w+$/", $hospital_address)) {
            $Hosp_address_err = "Sorry, the provided address format is invalid. Please enter a valid address in the format: '505, Namanga Road, Athi River'";
        }

        if (empty($hospital_contact_no)) {
            $Hosp_contact_err = "Hospital contact field is required";
        } elseif (!preg_match("/^(?:\+254|0)(?:7\d{8}|1\d{8})$/",$hospital_contact_no)) {
            $Hosp_contact_err = "It must be in the form of '+2547...' or '07...' or '01..' followed by 8 digits.";
        }

        // If all validations pass, proceed with editing
        if (empty($name_err) && empty($email_address_err) && empty($Hosp_name_err) && empty($Hosp_address_err) && empty($Hosp_contact_err)) {
            // Directory to store uploaded files
            $target_dir = "../images/";
            $hospital_logo = $_FILES['hospital_logo']['name'];
            // File path where the uploaded file will be stored
            $target_file = $target_dir . basename($hospital_logo);
            // Move the uploaded file to the specified directory
            if (move_uploaded_file($_FILES["hospital_logo"]["tmp_name"], $target_file)) {
                $update_query = "UPDATE admin_table SET admin_email_address = ?, admin_name = ?, hospital_name = ?, hospital_address = ?, hospital_contact_no = ?, hospital_logo = ? WHERE admin_id = ?";
                $update_stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($update_stmt, "ssssssi", $admin_email_address, $admin_name, $hospital_name, $hospital_address, $hospital_contact_no, $target_file, $_SESSION['admin_id']);

                if (mysqli_stmt_execute($update_stmt)) {
                    $log_message = "Admin successfully changed profile details.";
                    logger($log_message);
                    $_SESSION['success'] = "Profile updated successfully.";
                } else {
                    $_SESSION['err'] = "Error in updating your profile. Please try again.";
                }
            }
            else {
                // If no new file is uploaded, update the profile without changing the picture
                $update_query = "UPDATE admin_table SET admin_email_address = ?, admin_name = ?, hospital_name = ?, hospital_address = ?, hospital_contact_no = ? WHERE admin_id = ?";
                $update_stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($update_stmt, "sssssi", $admin_email_address, $admin_name, $hospital_name, $hospital_address, $hospital_contact_no, $_SESSION['admin_id']);

                if (mysqli_stmt_execute($update_stmt)) {
                    $log_message = "Admin successfully changed profile details.";
                    logger($log_message);
                    $_SESSION['success'] = "Profile updated successfully.";
                } else {
                    $_SESSION['err'] = "Error in updating your profile. Please try again.";
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
                    <div class="input-group d">
                        <label>Admin Name</label>
                        <input type="text" name="admin_name" id="admin_name" class="input">
                        <div class="error"><?php echo $name_err ?></div>
                    </div>
                    <div class="input-group d">
                        <label>Admin Email Address</label>
                        <input type="text" name="admin_email_address" id="admin_email_address" class="input">
                        <div class="error"><?php echo $email_address_err ?></div>
                    </div>
<!--                    <div class="input-group">-->
<!--                        <label>Password</label>-->
<!--                        <input type="text" name="admin_password" id="admin_password" class="input">-->
<!--                    </div>-->
                    <div class="input-group d">
                        <label>Hospital Name</label>
                        <input type="text" name="hospital_name" id="hospital_name" class="input">
                        <div class="error"><?php echo $Hosp_name_err ?></div>
                    </div>
                    <div class="input-group d">
                        <label>Hospital Address</label>
                        <input type="text" name="hospital_address" id="hospital_address" class="input">
                        <div class="error"><?php echo $Hosp_address_err ?></div>
                    </div>
                    <div class="input-group d">
                        <label>Hospital Contact No.</label>
                        <input type="text" name="hospital_contact_no" id="hospital_contact_no" class="input">
                        <div class="error"><?php echo $Hosp_contact_err ?></div>
                    </div>
                    <div class="input-group d">
                        <label>Hospital logo</label>
                        <br>
                        <input type="file" name="hospital_logo" id="hospital_logo">
                        <span id="uploaded_hospital_logo"></span>
                        <div class="error"><?php echo $Hosp_logo_err ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        <?php foreach($result as $row): ?>
        document.getElementById('admin_email_address').value = "<?php echo $row['admin_email_address']; ?>";
        document.getElementById('admin_name').value = "<?php echo $row['admin_name']; ?>";
        document.getElementById('hospital_name').value = "<?php echo $row['hospital_name']; ?>";
        document.getElementById('hospital_address').value = "<?php echo $row['hospital_address']; ?>";
        document.getElementById('hospital_contact_no').value = "<?php echo $row['hospital_contact_no']; ?>";

        <?php if($row['hospital_logo'] != ''): ?>
        document.getElementById("uploaded_hospital_logo").innerHTML = "<img src='<?php echo $row['hospital_logo']; ?>' class='img-thumbnail' width='100' /><input type='hidden' name='hidden_hospital_logo' value='<?php echo $row['hospital_logo']; ?>' />";
        <?php else: ?>
        document.getElementById("uploaded_hospital_logo").innerHTML = "<input type='hidden' name='hidden_hospital_logo' value='' />";
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
    include 'footer.php';
?>
