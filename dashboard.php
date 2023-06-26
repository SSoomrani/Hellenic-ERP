<?php
    session_start();

    $table_name = $_SESSION["current_table"] = "invoices";

    require 'dbh/dbh.php';
    require 'dbh/initialise.php';
    require 'dbh/customer_data.php';    

    $filter = "";

    $invoices_due_today = get_row_count($conn, "SELECT * FROM invoices WHERE delivery_date = curdate()");
    $invoices_due_week = get_row_count($conn, "SELECT * FROM invoices WHERE delivery_date < curdate() + 7 AND delivery_date >= curdate()");
    $products_expiring_month = get_row_count($conn, "SELECT * FROM stocked_items WHERE expiry_date >= curdate() AND expiry_date < curdate() + INTERVAL 1 MONTH");
    $products_expiring_week = get_row_count($conn, "SELECT * FROM stocked_items WHERE expiry_date >= curdate() AND expiry_date < curdate() + INTERVAL 1 WEEK");
    $income_today = get_row_contents($conn, "SELECT SUM(total) AS total_sum FROM invoices WHERE created_at = curdate()")[0][0];
    $income_week = get_row_contents($conn, "SELECT SUM(total) AS total_sum FROM invoices WHERE created_at >= curdate() - INTERVAL 1 WEEK")[0][0];
    $amount_overdue = get_row_count($conn, "SELECT * FROM `invoices` WHERE `status` = 'overdue'");
    $amount_overdue_week = get_row_count($conn, "SELECT * FROM `invoices` WHERE YEARWEEK(created_at) = YEARWEEK(CURDATE()) AND `status` = 'overdue'");
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title><?php echo(ucfirst($table_name)); ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="css/new_styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <script src="js/form_handler.js"></script>
    <script src="js/table_handler.js"></script>
    <script src="js/element_loader.js"></script>
    <script src="js/data_handler.js"></script>
</head>

<body>
    <div id="nav-placeholder"></div>
    <div class="main main-content">
        <h5>Pages / <p class="inline-shallow"><?php echo(ucfirst($table_name)); ?></p>
        </h5>
        <div id="widget-placeholder" class="grid-container">
            <div class="card item12">
                <h6 class="table-header">Invoices due for delivery today</h6>
                <?php include 'templates/table.php'; ?>
            </div>
            <div class="card item13">
                <h6 class="table-header">Products expiring soon</h6>
                <?php $table_name = "stocked_items"; ?>
                <?php include 'templates/table.php'; ?>
            </div>
        </div>
    </div>
    <div id="form-placeholder">
        <?php include 'templates/forms.php'; ?>
        <?php include 'templates/add_form.php'; ?>
        <?php include 'templates/edit_form.php'; ?>
    </div>
</body>

</html>
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadElement("sidenav.html", "nav-placeholder");
    loadElement("widgets.html", "widget-placeholder", populateWidgets);
    loadElement("toolbar.html", "widget-placeholder", setToolbar);
    searchTableFilter(getTables()[0], findColumnIndexByName(getTables()[0], "Delivery Date"), new Date()
    .toJSON().slice(0, 10), true);
    searchTableDateFilter(getTables()[1], findColumnIndexByName(getTables()[1], "Expiry Date"), 1, true);
    clearEditColumns(getTables());
    removeEmptyTable();
});

function populateWidgets() {
    configureWidgets(1, "Invoices Due Today", "outgoing_mail", "<?php echo($invoices_due_today); ?>",
        "<?php echo($invoices_due_week); ?>", " due this week.");
    configureWidgets(2, "Products Expiring This Week", "hourglass_empty", "<?php echo($products_expiring_week); ?>",
        "<?php echo($products_expiring_month); ?>", " expiring this month.");
    configureWidgets(3, "Income Today", "payments", "£<?php echo($income_today); ?>", "£<?php echo($income_week); ?>",
        " this week.");
    configureWidgets(4, "Outstanding Invoices", "markunread_mailbox", <?php echo($amount_overdue) ?>,
        <?php echo($amount_overdue_week) ?>, " from this week");
    fixFilter()
}

function setToolbar() {
    disableToolbarButton(1);
    setToolbarIcon(4, "open_in_new");
    setToolbarOnClick(4, goto);
}
</script>