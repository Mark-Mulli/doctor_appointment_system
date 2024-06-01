<?php
include "log_function.php";
//logout.php

session_start();

$id = $_SESSION['patient_id'];

$log_message = "Patient with id $id logged out of the system.";

logger($log_message);

session_destroy();

header("location:login.php");

?>
