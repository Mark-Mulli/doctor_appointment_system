<?php
$servername = "localhost";
$username = "root";
$password = "";
$db_name = "E-DOC";

//connect to the MySQL database
$conn = new mysqli($servername, $username, $password, $db_name);

if ($conn->connect_error) {
    die("CONNECTION FAILED: " . $conn->connect_error);
}
echo '';



