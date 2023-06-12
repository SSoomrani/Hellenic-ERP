<?php
    session_start();

    $_SESSION["current_table"] = "invoices";
    $table_name = "invoices";

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

    $edit_error_info = get_edit_error_info($conn, $table_name);
?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8">
	<title>Invoices</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="css/new_styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <script src="js/form_handler.js"></script>
    <script src="js/table_handler.js"></script>
    <script src="js/element_loader.js"></script>
</head>
<body>
    <div id="nav-placeholder">

    </div>
    <div class="main main-content">
        <h5>Pages / <p class="inline-shallow">Invoices</p></h5>
        <div class="grid-container">
            <div class="card item1">
                <div class="card-header">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">receipt_long</i>
                    </div>
                    <p class="text-end">Today's Invoices</p>
                    <h6 id="invoices-today" class="text-end"><?php echo($amount_today); ?></h6>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer">
                    <p id="inv-day-diff">
                        <span id="inv-day-diff-num" class="text-sm font-weight-bolder">10</span>
                       more than yesterday
                    </p>
                </div>
            </div>
            <div class="card item2">
                <div class="card-header">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">timer</i>
                    </div>
                    <p class="text-end">Pending Invoices</p>
                    <h6 id="money-today" class="text-end"><?php echo($amount_pending); ?></h6>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer">
                    <p>
                        <span class="text-sm font-weight-bolder"><?php echo($amount_pending_week); ?></span>
                        from this week
                    </p>
                </div>
            </div>
            <div class="card item3">
                <div class="card-header">
                    <div onclick="displayOverdue()" class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">markunread_mailbox</i>
                    </div>
                    <p class="text-end">Outstanding Invoices</p>
                    <h6 id="invoices-today" class="text-end"><?php echo($amount_overdue); ?></h6>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer">
                    <p>                        
                        <span class="text-sm font-weight-bolder"><?php echo($amount_overdue_week) ?></span>
                        from this week</p>
                </div>
            </div>  
            <div class="card item4">
                <div class="card-header">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">check</i>
                    </div>
                    <p class="text-end">Completed Today</p>
                    <h6 id="invoices-today" class="text-end"><?php echo($amount_completed_today); ?></h6>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer">
                    <p>                        
                        <span class="text-sm font-weight-bolder"><?php echo($amount_completed_week); ?></span>
                        from this week</p>
                </div>
            </div>
            <div class="card item5 grid-button">
                <div onclick="document.getElementById('add-form').style.display='block'" class="card-section">
                    <i class="material-icons opacity-10">note_add</i>
                </div>
            </div>
            <div class="card item6 grid-button">
                <div onclick="deleteMode()" class="card-section">
                    <i id="delete-button" class="material-icons opacity-10">&#xe872;</i>
                </div>
            </div>
            <div class="card item7 grid-button">
                <div class="card-section">
                    <i class="material-icons opacity-10">picture_as_pdf</i>
                </div>
            </div>
            <div class="card item8 grid-button">
                <div class="card-section">
                    <i class="material-icons opacity-10">help_outline</i>
                </div>
            </div>  
            <div class="item9 grid-exemption input-group input-group-outline">
                <input id="filter" style="font-family:Source Code Pro, FontAwesome" placeholder=" &#xF002;  Search for entries..." type="search" class="form-control search-bar-widget" onkeyup="searchTable()"></input>
            </div>
            <div class="card item10 grid-button">
                <div onclick="clearFilters()" class="card-section">
                    <i class="material-icons opacity-10">clear</i>
                </div>
            </div>
            <div class="card item11 grid-button">
                <div onclick="document.getElementById('filter-form').style.display='block'" class="card-section">
                    <i class="material-icons opacity-10">filter_list</i>
                </div>
            </div>
            <div class="card item12">
                <table id="tableView">
                    <tr>
                        <?php foreach($formatted_names as $key => $value): ?>
                            <?php if($key != 0): ?>
                                <th onclick="sortTable(<?php echo $key; ?>)"><?php echo $formatted_names[$key]; ?></th>
                            <?php else: ?>
                                <th onclick="selectAll()"><?php echo $formatted_names[$key]; ?></th>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                    <?php foreach($rows as $key => $row): ?>
                        <tr>
                            <?php foreach($field_names as $field_key => $field_name): ?>
                                <?php if ($field_key == 0): ?>
                                    <td onclick="select(<?php echo($key) ?>)"><?php echo $rows[$key][$field_names[$field_key]]; ?></td>
                                <?php else: ?>
                                    <td onclick="select(this)"><?php echo $rows[$key][$field_name]; ?></td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <td class="edit-column" onclick="displayEditForm(<?php echo($key); ?>)"><i class="material-icons">edit</i></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
    <div id="form-placeholder">
        <?php include 'templates/forms.php'; ?>
    </div>                             
</body>
</html>