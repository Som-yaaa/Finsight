<?php
session_start();
include("config.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Delete user's income
mysqli_query($conn, "DELETE FROM income WHERE user_id='$user_id'");

// Delete user's expenses
mysqli_query($conn, "DELETE FROM expenses WHERE user_id='$user_id'");

// Delete user account
mysqli_query($conn, "DELETE FROM users WHERE user_id='$user_id'");

session_destroy();

header("Location: login.php");
exit();
?>
