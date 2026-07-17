<?php
session_start();
include("config.php");

if(isset($_POST['register'])){

    $name = trim($_POST['name']);
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {

        // Prepared statement for security
        $stmt = mysqli_prepare($conn, 
            "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");

        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);

        mysqli_stmt_execute($stmt);

        $_SESSION['success'] = "Registration successful! Please login.";
        header("Location: login.php");
        exit();

    } catch (mysqli_sql_exception $e) {

        if($e->getCode() == 1062){
            $error_message = "Account already exists with this email.";
        } else {
            $error_message = "Something went wrong. Please try again.";
        }

    }
}
?>


<!DOCTYPE html>
<html>
<head>
   <title>FinSight - Register</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="card shadow p-4">

        <h3 class="text-center mb-3">Create Account</h3>
 <?php if(isset($error_message)): ?>
        <div class="alert alert-danger text-center">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

        <form method="POST">
          <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>

          <button type="submit" name="register" class="btn btn-success w-100">
            Register
          </button>
        </form>

        <div class="text-center mt-3">
          <a href="login.php">Already have an account? Login</a>
        </div>

      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
