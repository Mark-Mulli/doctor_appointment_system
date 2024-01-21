<?php
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

    </style>
</head>
<body>
   <main class="form-signin">
       <form action="" method="">
           <h1 class="head">Doctor Appointment Management System</h1>
           <span id="error"></span>
           <div class="input">
               <input type="text" placeholder="Enter Email Address...">
           </div>
           <div class="input">
               <input type="password" placeholder="Password">
           </div>
           <div class="input">
               <button type="submit" class="button">Login</button>
           </div>
       </form>
   </main>

</body>
</html>

