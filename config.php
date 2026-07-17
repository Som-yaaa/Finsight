<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "student_finance_db";


$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Database connection error.");
}

?>
