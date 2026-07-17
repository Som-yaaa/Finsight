<?php
session_start();
include("config.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
}

if(isset($_POST['add_expense'])){

    $user_id = $_SESSION['user_id'];
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    $sql = "INSERT INTO expenses (user_id, amount, category, description, date)
            VALUES ('$user_id', '$amount', '$category', '$description', '$date')";

    if(mysqli_query($conn, $sql)){
    $_SESSION['success'] = "Expense added successfully!";
    header("Location: dashboard.php");
    exit();
}
else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Expense</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include("navbar.php"); ?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow p-4">

        <h3 class="text-center mb-3">Add Expense</h3>

        <form method="POST">
          <div class="mb-3">
            <label>Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Category</label>
            <select name="category" class="form-control" required>
              <option value="">Select Category</option>
              <option>Food</option>
              <option>Travel</option>
              <option>Shopping</option>
              <option>Fees</option>
              <option>Other</option>
            </select>
          </div>

          <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
          </div>

          <div class="mb-3">
            <label>Date</label>
            <input type="date" name="date" class="form-control" required>
          </div>

          <button type="submit" name="add_expense" class="btn btn-danger w-100">
            Add Expense
          </button>
        </form>

      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
