<?php
session_start();
include 'connectdb.php';
include "../log_function.php";

$email_err = "";
$passcode_err = "";

function clean_input($data) {
    $data = htmlspecialchars($data);
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}


if(isset($_POST['admin_login'])) {
    $email = clean_input($_POST['admin_email']);
    $password = $_POST['admin_password'];

    if (empty($email)) {
        $email_err = "Please enter your email address";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email address";
    }
    if (empty($password)) {
        $passcode_err = "Please enter your passcode";
    } elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&.])[A-Za-z\d@$!%*#?&.]{8,16}$/", $password)) {
        $passcode_err = "Invalid Passcode. Must contain at least 1 uppercase letter, 1 lowercase letter, 1 digit, 1 special character, and be 8-16 characters long.";
    }

    if (empty($email_err) && empty($passcode_err)) {
        //login procedure
        sleep(2);

        $query = "SELECT * FROM admin_table WHERE admin_email_address = ?";
        $stmt = mysqli_prepare($conn,$query);
        mysqli_stmt_bind_param($stmt, "s",$email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 0 ) {
            $doc_query = "SELECT * FROM doctor_table WHERE doctor_email_address = ?";
            $doc_stmt = mysqli_prepare($conn,$doc_query);
            mysqli_stmt_bind_param($doc_stmt,"s",$email);
            mysqli_stmt_execute($doc_stmt);
            $doc_result = mysqli_stmt_get_result($doc_stmt);

            if (mysqli_num_rows($doc_result) == 0) {
                $_SESSION['error'] = "Wrong Email Address";
                $log_message = "Staff with email $email entered the wrong email address.";
                logger($log_message);
            }

            else {
                $rows = mysqli_fetch_all($doc_result, MYSQLI_ASSOC);
                foreach ($rows as $row) {
                    if ($row["doctor_status"] == 'On Leave') {
                        $_SESSION['error'] = "Your account is inactive, Please contact admin";
                        $log_message = "Doctor with email $email tried to log in while on leave.";
                        logger($log_message);
                    }
                    else {
                        //confirm if passcode matches, with time it will be hashed
                        if(password_verify($password, $row["doctor_password"])) {
                            $_SESSION['admin_id'] = $row['doctor_id'];
                            $_SESSION['type'] = 'Doctor';
                            $log_message = "Doctor with email $email logged successfully into the system.";
                            logger($log_message);
                            header("Location:doctor_schedule.php");
                            exit();
                        }
                        else {
                            $_SESSION['error'] = "Wrong doctor password";
                            $log_message = "Doctor with email $email keyed in the wrong password.";
                            logger($log_message);
                        }
                    }
                }
            }

        }

        else {
            $admin_result = mysqli_fetch_all($result, MYSQLI_ASSOC);
            foreach ($admin_result as $admin_row) {
                if ($password == $admin_row["admin_password"]) {
                    $_SESSION['admin_id'] = $admin_row['admin_id'];
                    $_SESSION['type'] = 'Admin';
                    $log_message = "Admin with email $email logged successfully into the system.";
                    logger($log_message);
                    header("location:dashboard.php");
                    exit();
                }
                else {
                    $_SESSION['error'] = "Wrong admin password";
                    $log_message = "Admin with email $email keyed in the wrong password.";
                    logger($log_message);
                }
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
           <h1 class="head">Doctor Appointment Management System</h1>
           <?php
           if (isset($_SESSION['error'])) {
               echo '<div class="error-message">' .$_SESSION['error']. '</div>';
               unset($_SESSION['error']);
           }
           ?>
           <div class="input">
               <input type="text" name="admin_email" placeholder="Enter Email Address...">
               <div class="error"><?php echo $email_err?></div>
           </div>
           <div class="input">
               <input type="password" name="admin_password" placeholder="Password">
               <div class="error"><?php echo $passcode_err?></div>
           </div>
           <div class="input">
               <button type="submit" class="button" name="admin_login">Login</button>
           </div>
           <div class="input">
               <a href="password_reset.php" style="float: right">Forgot Password?</a>
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

