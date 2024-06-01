<?php
include "../log_function.php";
//logout.php

session_start();

$id = $_SESSION['admin_id'];

$log_message = "Staff with id $id logged out of the system.";

logger($log_message);

session_destroy();

header("location:index.php");

?>
