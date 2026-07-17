<?php
session_start();
include("config.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
}

if(isset($_GET['id'])){

    $income_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $sql = "DELETE FROM income 
            WHERE income_id='$income_id' 
            AND user_id='$user_id'";

    mysqli_query($conn, $sql);
}

header("Location: dashboard.php");
?>
