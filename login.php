<?php
session_start();
include("config.php");

if(isset($_POST['login'])){

    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    // Prepared statement for security
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE LOWER(email)=?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) == 1){

        $row = mysqli_fetch_assoc($result);

        if(password_verify($password, $row['password'])){
             unset($_SESSION['success']);

            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['name'] = $row['name'];

            header("Location: dashboard.php");
            exit();

        } else {
            $error_message = "Incorrect password.";
        }

    } else {
        $error_message = "Account does not exist.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>FinSight - Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1">


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="card shadow p-4">

     <h3 class="text-center mb-3">Welcome to FinSight</h3>

<?php if(isset($_SESSION['success'])): ?>
<div class="alert alert-success text-center">
    <?php 
        echo $_SESSION['success']; 
        unset($_SESSION['success']);
    ?>
</div>
<?php endif; ?>

<?php if(isset($error_message)): ?>
<div class="alert alert-danger text-center">
    <?php echo $error_message; ?>
</div>
<?php endif; ?>

        <form method="POST">
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
<div class="text-center mt-3">
<a href="forgot_password.php">Forgot Password?</a>
</div><br>
          <button type="submit" name="login" class="btn btn-primary w-100">Login</button>

<div class="text-center mt-2">
<a href="register.php">Don't have an account? Sign Up</a>
</div>

        </form>

      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
