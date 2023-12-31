<?php
session_start();

$table_name = $_SESSION["current_table"] = "invoices";
require 'dbh/initialise.php';
$conn = initialise();

$filter = "";
$error_info = get_error_info();
$submitted_data = get_submitted_data();

$rows = get_table_contents($conn, $table_name, $filter);
$types = get_types($conn, $table_name);
run_query($conn, "SET information_schema_stats_expiry = 0");
$next_ID = get_row_contents($conn, "SELECT auto_increment from information_schema.tables WHERE table_name = 'invoices' AND table_schema = DATABASE()")[0][0];
$customer_names = get_row_contents($conn, "SELECT CONCAT(forename, ' ', surname) AS full_name FROM `customers`");
$customer_ids = get_row_contents($conn, "SELECT id FROM `customers`");
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
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title><?php echo ucfirst($table_name); ?></title>
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
        <h5>Pages / <p class="inline-shallow"><?php echo ucfirst($table_name); ?></p>
        </h5>
        <div id="widget-placeholder" class="grid-container">
            <div class="card item12">
                <?php include 'templates/table.php'; ?>
            </div>
        </div>
    </div>
    <div id="form-placeholder">
        <?php include 'templates/forms.php'; ?>
        <?php include 'templates/edit_form.php'; ?>
    </div>
    <div id="add-form-container" class="popup-form">
        <form class="popup-form-content animate" id="add-form" action="dbh/manage_data.php" method="post">
            <input type="hidden" name="table_name" value="<?php echo $table_name; ?>">
            <div class="popup-form-container">
                <input id="smart-mode" name="smart-mode" style="float: right" type="checkbox" checked>
                <label for="smart-mode" style="float: right">Smart Mode</label><br>
                <p id="add_error"></p>
                <br>
            <?php foreach ($editable_formatted_names as $key => $value): ?>
                <label><?php echo "$editable_formatted_names[$key]: "; ?></label>
                <br>
                <?php if (in_array($editable_field_names[$key], $required_fields)): ?>
                    <?php if ($editable_field_names[$key] == "customer_id"): ?>
                    <select name="customer_id" class="form-control" id="item-name-select" placeholder="Enter item name">
                        <option disabled selected value> --- Select Customer --- </option>
                        <?php foreach ($customer_names as $key => $value): ?>
                        <option value="<?php echo $customer_ids[$key][0]; ?>"><?php echo $customer_names[$key][0]; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php elseif ($editable_field_names[$key] == "title"): ?>
                    <input value="INV<?php echo $next_ID; ?>" class="form-control" required
                        id="<?php echo str_replace(' ', '', $editable_formatted_names[$key]); ?>" type="text"
                        name="<?php echo $editable_field_names[$key]; ?>" />
                    <?php elseif ($editable_field_names[$key] == "status"): ?>
                    <select name="<?php echo $editable_field_names[$key]; ?>" class="form-control" id="status-select">
                        <option disabled selected value> --- Select Invoice Status --- </option>
                        <option value="Pending">Pending</option>
                        <option value="Complete">Complete</option>
                        <option value="Overdue">Overdue</option>
                    </select>
                    <?php elseif ($types[$key] == "varchar(5)"): ?>
                        <select name="<?php echo $editable_field_names[$key]; ?>" class="form-control" required
                        id="<?php echo str_replace(' ', '', $editable_formatted_names[$key]); ?>">
                        <option disabled selected value> --- Select true / false --- </option>
                        <option value="true">True</option>
                        <option value="false">False</option>
                        </select>
                    <?php elseif ($types[$key] == "date"): ?>
                        <input type="date" class="form-control" required
                            id="<?php echo str_replace(' ', '', $editable_formatted_names[$key]); ?>" type="text"
                            name="<?php echo $editable_field_names[$key]; ?>">
                    <?php elseif ($editable_field_names[$key] == "VAT" || $editable_field_names[$key] == "net_value"): ?>
                    <input onkeyup="calculateTotal()" class="form-control" required
                        id="<?php echo str_replace(' ', '', $editable_formatted_names[$key]); ?>" type="text"
                        name="<?php echo $editable_field_names[$key]; ?>">
                    <?php else: ?>
                    <input class="form-control" required
                        id="<?php echo str_replace(' ', '', $editable_formatted_names[$key]); ?>" type="text"
                        name="<?php echo $editable_field_names[$key]; ?>">
                    <?php endif; ?>
                <?php else: ?>
                <input class="form-control"
                    id="<?php echo str_replace(' ', '', $editable_formatted_names[$key]); ?>" type="text"
                    name="<?php echo $editable_field_names[$key]; ?>">
                <?php endif; ?>
            <?php endforeach; ?>
            </div>
            <div class="popup-form-container-small popup-form-container-footer">
                <p onclick=hideForm(this);>Close</p>
                <button name="add" type="submit" style="float: right">
                    <p>Submit</p>
                </button>
            </div>
        </form>
    </div>
    <div id="email-invoice-form" class="popup-form">
        <form class="popup-form-content animate" action="dbh/manage_data.php" method="post">
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
    loadElement("widgets.html", "widget-placeholder", configure);
    loadElement("toolbar.html", "widget-placeholder");
});

function configure() {
    populateWidgets();
    dayDifferenceTotal();
    checkError();
}

function populateWidgets() {
    configureWidgets(1, "Today's Invoices", "receipt_long", <?php echo $amount_today; ?>, 10, " more than yesterday");
    configureWidgets(2, "Pending Invoices", "timer", <?php echo $amount_pending; ?>,
        <?php echo $amount_pending_week; ?>, " from this week");
    configureWidgets(3, "Outstanding Invoices", "markunread_mailbox", <?php echo $amount_overdue; ?>,
        <?php echo $amount_overdue_week; ?>, " from this week");
    configureWidgets(4, "Completed Today", "check", <?php echo $amount_completed_today; ?>,
        <?php echo $amount_completed_week; ?>, " from this week");
    document.getElementById("widget-box-3").setAttribute("onclick", "displayOverdue()");
}

function calculateTotal() {
    if (document.getElementById("smart-mode").checked) {
        var netValue = document.getElementById("NetValue").value;
        var VAT = document.getElementById("VAT").value;
        if ((isFloat(netValue) || isInt(netValue)) && !isNaN(netValue)) {
            if (!isFloat(VAT) || !isInt(VAT)) {
                VAT = (netValue * 0.2).toFixed(2);
                document.getElementById("VAT").value = VAT;
            }
            document.getElementById("Total").value = (parseFloat(netValue) + parseFloat(VAT)).toFixed(2);
        }
    }
}

function checkError() {
    var error = "<?php echo $error_info[0]; ?>";
    var rowID = "<?php echo $error_info[1]; ?>";
    var errorType = "<?php echo $error_info[2]; ?>";
    if (error != "") {
        if (errorType == "edit") {
            var errorMsg = document.getElementById("edit_error");
            errorMsg.innerText = error;
            if (rowID != -1) {
                displayEditForm(rowID - 1);
            }
        } else {
            var elements = document.getElementById("add-form").elements;
            var submittedData = <?php echo json_encode($submitted_data); ?>;
            for (var i = 0; i < elements.length-2; i++) {
                elements[i+2].value = submittedData[i];
            }
            var errorMsg = document.getElementById("add_error");
            errorMsg.innerText = error;
            document.getElementById('add-form-container').style.display='block';
        }
        <?php clear_error_session(); ?>
    }
}

function dayDifferenceTotal() {
    var difference = parseInt(<?php echo $today_yesterday_diff; ?>);
    var element = document.getElementById("widget-text-value-1");
    if (difference >= 0) {
        document.getElementById("widget-text-1").lastChild.textContent = " more than yesterday";
        element.innerText = difference;
        element.classList.add('text-success');
    } else {
        difference = Math.abs(difference);
        document.getElementById("widget-text-1").lastChild.textContent = " less than yesterday";
        element.innerText = Math.abs(difference);
        element.classList.add('text-unsuccess');
    }
}

function displayOverdue() {
    document.getElementById("column_select").value = "3";
    document.getElementById("advanced-filter").value = "overdue";
    filterTable();
    var table = getTables()[0];
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

function changeSmartMode() {

}
</script>