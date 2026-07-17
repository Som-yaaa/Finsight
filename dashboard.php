<?php
session_start();

include("config.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
 exit();
}

$user_id = $_SESSION['user_id'];
// Get selected month (default current month)
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
if(!preg_match("/^\d{4}-\d{2}$/", $selected_month)){
    $selected_month = date('Y-m');
}


// Fetch expenses
$sql = "SELECT * FROM expenses 
        WHERE user_id='$user_id' 
        AND DATE_FORMAT(date, '%Y-%m') = '$selected_month'
        ORDER BY date DESC";

$result = mysqli_query($conn, $sql);

// Calculate total expense
$total_sql = "SELECT SUM(amount) AS total 
              FROM expenses 
              WHERE user_id='$user_id' 
              AND DATE_FORMAT(date, '%Y-%m') = '$selected_month'";

$total_result = mysqli_query($conn, $total_sql);
$total_row = mysqli_fetch_assoc($total_result);
$total_expense = $total_row['total'];

// Calculate total income
$income_sql = "SELECT SUM(amount) AS total_income 
               FROM income 
               WHERE user_id='$user_id' 
               AND DATE_FORMAT(date, '%Y-%m') = '$selected_month'";

$income_result = mysqli_query($conn, $income_sql);
$income_row = mysqli_fetch_assoc($income_result);
$total_income = $income_row['total_income'];

// Category-wise expense data
$category_sql = "SELECT category, SUM(amount) as total 
                 FROM expenses 
                 WHERE user_id='$user_id' 
                 AND DATE_FORMAT(date, '%Y-%m') = '$selected_month'
                 GROUP BY category";


$category_result = mysqli_query($conn, $category_sql);

$categories = [];
$category_totals = [];

while($row = mysqli_fetch_assoc($category_result)){
    $categories[] = $row['category'];
    $category_totals[] = $row['total'];
}


$total_income = $total_income ? $total_income : 0;
$total_expense = $total_expense ? $total_expense : 0;
$savings = $total_income - $total_expense;

// Financial Health Score Calculation
$savings_rate = ($total_income > 0) ? ($savings / $total_income) * 100 : 0;

// Score equals savings rate (capped at 100)
$health_score = min(round($savings_rate), 100);

if($health_score >= 40){
    $risk_level = "Financially Stable";
    $badge_color = "success";
}
elseif($health_score >= 20){
    $risk_level = "Moderate Stability";
    $badge_color = "warning";
}
else{
    $risk_level = "High Risk";
    $badge_color = "danger";
}



// Intelligent Insights
$insight_message = "";

if($total_income == 0){
    $insight_message = "No income recorded for this month.";
}
elseif($savings_rate < 0){
    $insight_message = "Warning: You are spending more than your income!";
}
elseif($savings_rate < 20){
    $insight_message = "Low savings detected. Try to reduce unnecessary expenses.";
}
elseif($savings_rate < 40){
    $insight_message = "Moderate savings. There is room for improvement.";
}
else{
    $insight_message = "Excellent! You are maintaining a healthy savings pattern.";
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>FinSight - Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body class="bg-light">
<?php include("navbar.php"); ?>


<div class="container mt-5">

    <h2 class="mb-4">
Welcome <?php echo htmlspecialchars($_SESSION['name']); ?> 👋
</h2>
<form method="GET" class="mb-4">
    <label>Select Month:</label>
    <input type="month" name="month" value="<?php echo $selected_month; ?>">
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
</form>


    <div class="row mb-4">

        <div class="col-md-4">
            <div class="card shadow p-3 text-center">
                <h5>Total Income</h5>
                <h3 class="text-success">₹ <?php echo $total_income; ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow p-3 text-center">
                <h5>Total Expense</h5>
                <h3 class="text-danger">₹ <?php echo $total_expense; ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow p-3 text-center">
                <h5>Savings</h5>
                <h3 class="text-primary">₹ <?php echo $savings; ?></h3>
            </div>
        </div>

    </div>

<div class="row mb-4 justify-content-center">
    <div class="col-md-6">
        <div class="card shadow p-4 text-center">

            <h5>Financial Health Score</h5>
            <h2 class="mb-3"><?php echo round($health_score); ?>/100</h2>

            <span class="badge bg-<?php echo $badge_color; ?> p-2">
                <?php echo $risk_level; ?>
            </span>

        </div>
    </div>
</div>



    <div class="alert alert-info text-center">
        <?php echo $insight_message; ?>
    </div>

    <div class="mb-4 text-center">
        
    </div>

    <h4>Your Expenses</h4>

    <table class="table table-bordered table-striped">
        <tr>
            <th>Amount</th>
            <th>Category</th>
            <th>Description</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
<?php if(mysqli_num_rows($result) == 0): ?>
<tr>
<td colspan="5" class="text-center text-muted">
No expenses recorded for this month.
</td>
</tr>
<?php endif; ?>

        <?php
        while($row = mysqli_fetch_assoc($result)){
        ?>
        <tr>

    <td>₹ <?php echo $row['amount']; ?></td>
   <td><?php echo htmlspecialchars($row['category']); ?></td>
<td><?php echo htmlspecialchars($row['description']); ?></td>

<td><?php echo htmlspecialchars($row['date']); ?></td>

    <td>
        <a href="delete_expense.php?id=<?php echo $row['expense_id']; ?>" 
           class="btn btn-danger btn-sm"
           onclick="return confirm('Are you sure you want to delete this expense?');">
           Delete
        </a>
    </td>
</tr>

        <?php
        }
        ?>

    </table>
<h4 class="mt-5">Your Income</h4>

<table class="table table-bordered table-striped">
<tr>
    <th>Amount</th>
    <th>Source</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php
$income_list_sql = "SELECT * FROM income 
                    WHERE user_id='$user_id' 
                    AND DATE_FORMAT(date, '%Y-%m') = '$selected_month'
                    ORDER BY date DESC";

$income_list_result = mysqli_query($conn, $income_list_sql);
?>

<?php if(mysqli_num_rows($income_list_result) == 0): ?>
<tr>
<td colspan="4" class="text-center text-muted">
No income recorded for this month.
</td>
</tr>
<?php endif; ?>

<?php
while($income_row = mysqli_fetch_assoc($income_list_result)){
?>
<tr>
    <td>₹ <?php echo $income_row['amount']; ?></td>
    <td><?php echo htmlspecialchars($income_row['source']); ?></td>
    <td><?php echo htmlspecialchars($income_row['date']); ?></td>
    <td>
        <a href="delete_income.php?id=<?php echo $income_row['income_id']; ?>" 
           class="btn btn-danger btn-sm"
           onclick="return confirm('Delete this income?');">
           Delete
        </a>
    </td>
</tr>
<?php } ?>



</table>


<h4 class="mt-5 text-center">Expense Distribution</h4>

<div class="row justify-content-center">
    <div class="col-md-6">
        <canvas id="expenseChart" ></canvas>
    </div>
</div>



</div>
<script>
const ctx = document.getElementById('expenseChart').getContext('2d');

const expenseChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($categories); ?>,
        datasets: [{
            label: 'Expenses',
            data: <?php echo json_encode($category_totals); ?>,
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF'
            ]
        }]
    },
      

});

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
