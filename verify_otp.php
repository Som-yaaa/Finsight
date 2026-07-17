<?php
session_start();
include("config.php");

if(!isset($_SESSION['reset_email'])){
    header("Location: login.php");
    exit();
}

$email = $_SESSION['reset_email'];

if(isset($_POST['verify'])){

    $entered_otp = trim($_POST['otp']);
    $new_password = $_POST['new_password'];

    // Secure SELECT
    $stmt = mysqli_prepare($conn, "SELECT otp FROM users WHERE LOWER(email)=?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if($user && $entered_otp == $user['otp']){

        $hashed = password_hash($new_password, PASSWORD_DEFAULT);

        // Secure UPDATE
        $update_stmt = mysqli_prepare($conn, 
            "UPDATE users SET password=?, otp=NULL WHERE LOWER(email)=?");
        mysqli_stmt_bind_param($update_stmt, "ss", $hashed, $email);
        mysqli_stmt_execute($update_stmt);

        // Clear only reset session
        unset($_SESSION['reset_email']);
        unset($_SESSION['demo_otp']);

        $_SESSION['success'] = "Password reset successful. Please login.";
        header("Location: login.php");
        exit();

    } else {
        $error_message = "Invalid OTP.";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
<title>FinSight - Verify OTP</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-4">
<div class="card shadow p-4">

<h4 class="text-center mb-3">Enter OTP</h4>

<!-- Demo OTP Display -->
<div class="alert alert-info text-center">
Demo OTP: <strong><?php echo $_SESSION['demo_otp']; ?></strong>
</div>

<?php if(isset($error_message)): ?>
<div class="alert alert-danger text-center">
<?php echo $error_message; ?>
</div>
<?php endif; ?>

<form method="POST">

<div class="mb-3">
<label>Enter OTP</label>
<input type="text" name="otp" class="form-control" required>
</div>

<div class="mb-3">
<label>New Password</label>
<input type="password" name="new_password" class="form-control" required>
</div>

<button type="submit" name="verify" class="btn btn-success w-100">
Reset Password
</button>

</form>

</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
