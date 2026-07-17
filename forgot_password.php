<?php
session_start();
include("config.php");

if(isset($_POST['send_otp'])){

    $email = strtolower(trim($_POST['email']));

    // Secure prepared statement
    $stmt = mysqli_prepare($conn, "SELECT user_id FROM users WHERE LOWER(email)=?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0){

        // Stronger OTP generation
        $otp = random_int(100000, 999999);

        // Update OTP securely
        $update_stmt = mysqli_prepare($conn, "UPDATE users SET otp=? WHERE LOWER(email)=?");
        mysqli_stmt_bind_param($update_stmt, "ss", $otp, $email);
        mysqli_stmt_execute($update_stmt);

        $_SESSION['reset_email'] = $email;
        $_SESSION['demo_otp'] = $otp; // Demo display only

        header("Location: verify_otp.php");
        exit();

    } else {
        $error_message = "Email not found.";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
<title>FinSight - Forgot Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-4">
<div class="card shadow p-4">

<h4 class="text-center mb-3">Forgot Password</h4>

<?php if(isset($error_message)): ?>
<div class="alert alert-danger text-center">
<?php echo $error_message; ?>
</div>
<?php endif; ?>

<form method="POST">
<div class="mb-3">
<label>Enter Registered Email</label>
<input type="email" name="email" class="form-control" required>
</div>

<button type="submit" name="send_otp" class="btn btn-primary w-100">
Send OTP
</button>
</form>

</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
