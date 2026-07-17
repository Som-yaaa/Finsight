<?php if(isset($_SESSION['user_id'])) { ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">

    <a class="navbar-brand fw-bold" href="dashboard.php">
      FinSight
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">

        <li class="nav-item">
          <a class="nav-link" href="profile.php">Profile</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">Dashboard</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="add_income.php">Add Income</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="add_expense.php">Add Expense</a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-danger" href="logout.php">Logout</a>
        </li>

      </ul>
    </div>

  </div>
</nav>

<?php } ?>
