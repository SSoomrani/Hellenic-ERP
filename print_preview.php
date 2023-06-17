<?php
    session_start();

    require 'dbh/dbh.php';
    require 'dbh/initialise.php';
    require 'dbh/customer_data.php';

    //Run queries for widgets from initialise.php
    $data = get_invoice_info($conn, $_POST['print_row_id']);
    $invoice_title = $data[0];
    $due_date = $data[1];
    $
    $edit_error_info = get_error_info();
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title><?php echo(ucfirst($table_name)); ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="css/invoice_styles.css">
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
    <div class="invoice">
        <div class="header">
            <h2>Sales Invoice</h2>
        </div>
        <div class="address">
            <div class="company">Your Company Name</div>
            <div class="address-details">
                123 Main Street<br>
                City, State, ZIP<br>
                Country
            </div>
        </div>
        <div class="details">
            <div class="detail-row">
                <span class="label">Invoice Number:</span>
                <span class="value">INV-001</span>
            </div>
            <div class="detail-row">
                <span class="label">Invoice Date:</span>
                <span class="value">June 16, 2023</span>
            </div>
        </div>
        <div class="items">
            <div class="item-row">
                <span class="item-name">Product 1</span>
                <span class="item-quantity">2</span>
                <span class="item-price">$10.00</span>
            </div>
            <div class="item-row">
                <span class="item-name">Product 2</span>
                <span class="item-quantity">3</span>
                <span class="item-price">$15.00</span>
            </div>
        </div>
        <div class="total">
            <span class="label">Total:</span>
            <span class="value">$70.00</span>
        </div>
    </div>
</body>

</html>
<script>
console.log(selected);
</script>