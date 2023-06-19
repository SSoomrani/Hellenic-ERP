<?php
    session_start();

    $table_name = $_SESSION["current_table"] = "invoices";

    require 'dbh/dbh.php';
    require 'dbh/initialise.php';
    require 'dbh/customer_data.php';

    $table_info = get_table_info($conn, "invoices");
    $formatted_names = $table_info[0];
    $field_names = $table_info[1];
    $editable_formatted_names = $table_info[2];
    $editable_field_names = $table_info[3];

    $rows = get_table_contents($conn, $table_name);

    $amount_pending = get_row_count($conn, "SELECT * FROM `invoices` WHERE `status` = 'pending'");
    $amount_pending_week = get_row_count($conn, "SELECT * FROM `invoices` WHERE YEARWEEK(created_at) = YEARWEEK(CURDATE()) AND `status` = 'pending'");
    $amount_pending_today = get_row_count($conn, "SELECT * FROM invoices WHERE DATE(created_at) = DATE(CURDATE()) AND `status` = 'pending'");
    $amount_overdue = get_row_count($conn, "SELECT * FROM `invoices` WHERE `status` = 'overdue'");
    $amount_overdue_week = get_row_count($conn, "SELECT * FROM `invoices` WHERE YEARWEEK(created_at) = YEARWEEK(CURDATE()) AND `status` = 'overdue'");
    $amount_yesterday = get_row_count($conn, "SELECT * FROM invoices WHERE DATE(created_at) = CURDATE() - INTERVAL 1 DAY");
    $amount_today = get_row_count($conn, "SELECT * FROM invoices WHERE DATE(created_at) = CURDATE()");
    $amount_completed_today = get_row_count($conn, "SELECT * FROM invoices WHERE DATE(created_at) = CURDATE() AND `status` = 'complete'");
    $amount_completed_week = get_row_count($conn, "SELECT * FROM invoices WHERE YEARWEEK(created_at) = YEARWEEK(CURDATE()) AND `status` = 'complete'");
    $today_yesterday_diff = $amount_today - $amount_yesterday;
    $customer_identifiers = get_customer_names($conn);

    $edit_error_info = get_error_info();
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
                <?php include 'templates/table.php'; ?>
            </div>
        </div>
    </div>
    <div id="form-placeholder">
        <?php include 'templates/forms.php'; ?>
        <?php include 'templates/add_form.php'; ?>
        <?php include 'templates/edit_form.php'; ?>
        <?php include 'templates/delete_form.php'; ?>
    </div>
    <div id="email-invoice-form" class="popup-form">
        <form class="popup-form-content animate" action="dbh/manageData.php" method="post">
            <input id="selectedCount" type="hidden" value=""></input>
            <div id="email-form-container" class="popup-form-container">
                <p class="text-unsuccess" id="select-error"></p>
                <h2>Email Invoices</h2>
                <div class="card" style="padding: 10px;">
                    <div id="added-elements"></div>
                </div>
            </div>
            <div class="popup-form-container-small popup-form-container-footer">
                <p onclick=cleanHideForm(this);>Close</p>
                <button name="email_invoices" type="submit" style="float: right">
                    <p>Send</p>
                </button>
        </form>
    </div>
</body>

</html>
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadElement("sidenav.html", "nav-placeholder");
    loadElement("widgets.html", "widget-placeholder", populateWidgets);
});

checkEditError();

function populateWidgets() {
    configureWidgets(1, "Today's Invoices", "receipt_long", <?php echo($amount_today) ?>, 10, " more than yesterday");
    configureWidgets(2, "Pending Invoices", "timer", <?php echo($amount_pending) ?>,
        <?php echo($amount_pending_week) ?>, " from this week");
    configureWidgets(3, "Outstainding Invoices", "markunread_mailbox", <?php echo($amount_overdue) ?>,
        <?php echo($amount_overdue_week) ?>, " from this week");
    configureWidgets(4, "Completed Today", "check", <?php echo($amount_completed_today); ?>,
        <?php echo($amount_completed_week); ?>, " from this week");
    document.getElementById("widget-box-3").setAttribute("onclick", "displayOverdue()");
    dayDifferenceTotal();
}

function displayOverdue() {
    document.getElementById("column_select").value = "3";
    document.getElementById("advanced-filter").value = "overdue";
    filterTable();
    var table = document.getElementById("tableView");
    var rows = table.rows;
    var columnLength = rows[0].cells.length - 1;
    for (i = 0; i < columnLength - 1; i++) {
        if (rows[0].getElementsByTagName("TH")[i].innerText == "Status" || rows[0].getElementsByTagName("TH")[i]
            .innerText == "Print Status") {
            rows[0].getElementsByTagName("TH")[i].style.display = "none";
            for (k = 1; k < rows.length; k++) {
                rows[k].getElementsByTagName("TD")[i].style.display = "none";
            }
        }
    }
    rows[0].getElementsByTagName("TH")[0].innerText = "Select All";
    for (i = 1; i < rows.length; i++) {
        rows[i].getElementsByTagName("TD")[columnLength + 1].lastChild.innerHTML = "&#xe0e1;";
        var array = <?php echo json_encode($customer_identifiers); ?>;
        rows[i].getElementsByTagName("TD")[columnLength + 1].setAttribute("onclick", "displayEmailForm(" + JSON
            .stringify(array) + ");");
    }
}

function notifyOverdueInvoices() {

}

function checkEditError() {
    var error = "<?php echo $edit_error_info[0]; ?>";
    var rowID = "<?php echo $edit_error_info[1]; ?>"
    if (error != "") {
        var errorMsg = document.getElementById("edit_error");
        errorMsg.innerText = error;
        if (rowID != -1) {
            displayEditForm(rowID - 1);
        }
        <?php session_unset(); ?>
    }
}

function dayDifferenceTotal() {
    var difference = "<?php echo($today_yesterday_diff); ?>";
    var element = document.getElementById("widget-text-value-1");
    if (difference >= 0) {
        document.getElementById("widget-text-value-1").lastChild.textContent = " more than yesterday";
        element.innerText = difference;
        element.classList.add('text-success');
    } else {
        difference = Math.abs(difference);
        document.getElementById("widget-text-value-1").lastChild.textContent = " less than yesterday";
        element.innerText = Math.abs(difference);
        element.classList.add('text-unsuccess');
    }
}
</script>