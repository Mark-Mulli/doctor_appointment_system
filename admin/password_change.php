<?php
session_start();
include 'connectdb.php';

$email_ERR = "";
$passcode_ERR = "";
$conf_pass_ERR = "";

function clean_input($data) {
    $data = htmlspecialchars($data);
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}


if(isset($_POST['password_update'])) {
    $email = clean_input($_POST['admin_email']);
    $patient_password = $_POST['new_password'];
    $patient_confirm_password = $_POST['confirm_password'];

    // Email validation
    if (empty($email)) {
        $email_ERR = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
            $email = mysqli_real_escape_string($conn, $_POST['admin_email']);
            $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
            $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

            $token = mysqli_real_escape_string($conn, $_POST['password_token']);

            if (!empty($token)) {
                $check_token = "SELECT verify_token FROM doctor_table WHERE verify_token = '$token' LIMIT 1";
                $check_token_run = mysqli_query($conn, $check_token);
                if (mysqli_num_rows($check_token_run) > 0) {
                    if ($new_password == $confirm_password) {
                        $hash = password_hash($new_password, PASSWORD_DEFAULT);
                        $update_password = "UPDATE doctor_table SET doctor_password = '$hash' WHERE verify_token ='$token' LIMIT 1";
                        $update_password_run = mysqli_query($conn, $update_password);
                        if ($update_password_run) {
                            echo '<script> 
                            alert("New Password Successfully updated. Click OK to login");
                            window.location.href = "index.php";
                            </script>';

                        } else {
                            $_SESSION['error'] = "Did not update password. Something went wrong.";
                        }
                    }
                } else {
                    $_SESSION['error'] = "Invalid Token";
                }
            } else {
                $_SESSION['error'] = "No token available";
            }
        }
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="admin_assets/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>DAMS</title>
    <style>
        html,body {
            height:100%;
        }
        body {
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
            justify-content: center;
            text-align: center;
            color: #858796;
        }
        .form-signin {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: auto;
        }
        form .head {
            margin-bottom: 1rem;
            font-size: 1.75rem;
            font-weight: 400;
            line-height: 1.2;
            margin-top: 0;
        }
        .success-message, .error-message, .info-message {
            position: relative;
            padding: 0.75rem 1.25rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
            margin: 1rem auto;
            max-width: 700px;
            width: 100%;
        }
        .error-message {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        form .input {
            margin-bottom: 1rem;
        }
        .input input {
            display: block;
            position: relative;
            height: auto;
            font-size: 16px;
            width: 100%;
            padding: 10px;
            font-weight: 400;
            line-height: 1.5;
            color: #6e707e;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }

        .input input:focus {
            color: #6e707e;
            background-color: #fff;
            border-color: #bac8f3;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(78,115,223,.25);
        }
        .button {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 0.35rem;
            color: #fff;
            background-color: #4e73df;
            border: 1px solid #4e73df;
            text-align: center;
            vertical-align: middle;
            transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .button:hover {
            color: #fff;
            background-color: #2e59d9;
            border-color: #2653d4;
            text-decoration: none;
            cursor: pointer;
        }
        .input .error {
            color: #B94A48;
        }

    </style>
</head>
<body>
<main class="form-signin">
    <form action="" method="post">
        <input type="hidden" value="<?php if(isset($_GET['token'])){echo $_GET['token'];}?>" name="password_token">
        <h1 class="head">Reset Password</h1>
        <?php
        if (isset($_SESSION['status'])) {
            echo '<div class="success-message">' . $_SESSION['status'] . '</div>';
            unset($_SESSION['status']); // Clear the message
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="error-message">' .$_SESSION['error']. '</div>';
            unset($_SESSION['error']);
        }
        ?>
        <div class="input">
            <input type="text" name="admin_email" value="<?php if(isset($_GET['email'])){echo $_GET['email'];}?>" placeholder="Enter Email Address...">
            <div class="error"><?php echo $email_ERR?></div>
        </div>
        <div class="input">
            <input type="password" id="new_password" name="new_password"  placeholder="Type new passcode" autofocus>
            <div class="error"><?php echo $passcode_ERR ?></div>
        </div>
        <div class="input">
            <input type="password" id="confirm_password" name="confirm_password"  placeholder="Retype new passcode">
            <div class="error"><?php echo $conf_pass_ERR ?></div>
        </div>
        <div class="input">
            <button type="submit" class="button" name="password_update">Update Password</button>
        </div>
    </form>
</main>

</body>
</html>

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

