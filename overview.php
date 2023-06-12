<?php
    session_start();

    if (!isset($_SESSION["current_table"])) {
      $tableName = "customers";
    } else {
      $tableName = $_SESSION["current_table"];
    }
    $_SESSION["current_table"] = $tableName;

    require 'dbh.php';
    $query = $conn->query('SHOW FULL COLUMNS FROM '. $tableName);
    $clearNames = array();
    $fieldNames = array();
    $formFields = array();
    while($row = $query->fetch_assoc()) {
        $clearNames[] = $row['Comment'];
        $fieldNames[] = $row['Field'];
        if ($row['Extra'] == null)
        {
          $formFields[] = $row['Comment'];
        }
    }

    $query = $conn->query('SELECT * FROM '. $tableName);
    $rows = $query->fetch_all(MYSQLI_ASSOC);

    $query = $conn->query('SHOW TABLES;');
    $tables = $query->fetch_all(MYSQLI_ASSOC);

    $sqlError = "";
    if (isset($_SESSION['mysql_error'])) {
      $sqlError = $_SESSION['mysql_error'];
    }

    $rowID = -1;
    if (isset($_SESSION['row_id'])) {
      $rowID = $_SESSION['row_id'];
    }
?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8">
	<title>Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="new_styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro&display=swap" rel="stylesheet">
</head>
<body>
    <div class="sidenav border-radius-xl ms-3 my-3">
        <div class="sidenav-header">
            <h4>
            <i class="material-icons inline-icon">list</i>
            Hellenic Dashboard</h4>
        </div>
        <hr class="dark horizontal my-0">
        <ul>
            <li class="nav-item">
                <h5>PAGES</h5>
            </li>
            <li class="nav-item">
                <a href="./overview.php" class="transport-button">
                <i class="material-icons inline-icon">dashboard</i>
                Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="/view.php" class="transport-button">
                <i class="material-icons inline-icon">table_view</i>
                Tables</a>
            </li>
            <li class="nav-item">
                <a href="./invoices.php" class="transport-button">
                <i class="material-icons inline-icon">receipt_long</i>
                Invoices</a>
            </li>
            <li class="nav-item">
                <a class="transport-button">
                <i class="material-icons inline-icon">warehouse</i>
                Warehouses</a>
            </li>
        </ul>
    </div>
    <div class="main main-content">
        <h5>Pages / Dashboard</h5>
        <div class="grid-container">
            <div class="card item1">
                <div class="card-header">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">attach_money</i>
                    </div>
                    <p class="text-end">Today's Money</p>
                    <h6 id="money-today" class="text-end">£37K</h6>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer">
                    <p>
                        <span class="text-success text-sm font-weight-bolder">+55% </span>
                        than last week
                    </p>
                </div>
            </div>
            <div class="card item2">
                <div class="card-header">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">receipt_long</i>
                    </div>
                    <p class="text-end">Today's Invoices</p>
                    <h6 id="invoices-today" class="text-end">17</h6>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer">
                    <p>
                        <span class="text-unsuccess text-sm font-weight-bolder">-4% </span>
                        than last week
                    </p>
                </div>
            </div>
            <div class="card item3">
                <div class="card-header">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">credit_card</i>
                    </div>
                    <p class="text-end">Outstanding Balance</p>
                    <h6 id="invoices-today" class="text-end">£12.9K</h6>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer">
                    <p>                        
                        <span class="text-success text-sm font-weight-bolder">-12% </span>
                        than last week</p>
                </div>
            </div>  
            <div class="card item4">
            <div class="card-header">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">markunread_mailbox</i>
                    </div>
                    <p class="text-end">Outstanding Invoices</p>
                    <h6 id="invoices-today" class="text-end">5</h6>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer">
                    <p>                        
                        <span class="text-unsuccess text-sm font-weight-bolder">5% </span>
                        than last week</p>
                </div>
            </div>
            <div class="card item5">5</div>
            <div class="card">6</div>
            <div class="card">7</div>
            <div class="card">8</div>  
            <div class="card">9</div>
            <div class="card">10</div>
            <div class="card">11</div>
            </div>
        </div>
    </div>
</body>
</html>
<script>

</script>