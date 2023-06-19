<?php
    session_start();

    require 'dbh/dbh.php';
    require 'dbh/initialise.php';
    require 'dbh/customer_data.php';

    //Run queries for widgets from initialise.php
    $invoice_info = get_invoice_info($conn, $_POST['print_row_id']);
    $products_length = count($invoice_info);
    $invoice_title = $invoice_info[0][0];
    $due_date = $invoice_info[0][1];
    $net_value = $invoice_info[0][2];
    $vat = $invoice_info[0][4];
    $delivery_date = $invoice_info[0][5];
    $forename = $invoice_info[0][6];
    $surnmame = $invoice_info[0][7];
    $delivery_address = $invoice_info[0][8];
    $invoice_address = $invoice_info[0][9];

    $product_info = get_invoice_products($conn, $_POST['print_row_id']);

    $total = 0;
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
            <div class="company">Hellenic Grocery LTD</div>
            <div id="company-address" class="address-details">
                Unit 15<br>
                Hilsea Industrial Estate<br>
                Limberline Spur<br>
                Portsmouth<br>
                PO3 5JW
            </div>
        </div>
        <div class="details">
            <div class="detail-row">
                <span class="label">Invoice Number:</span>
                <span id="invoice-title" class="value">INV-001</span>
            </div>
            <div class="detail-row">
                <span class="label">Invoice Date:</span>
                <span class="value"><?php echo(date("d-m-Y")) ?></span>
            </div>
            <br>
        </div>
        <div class="address">
            <div class="address-details"><b>Invoice Address:</b></div>
            <div class="address-details"><?php echo($invoice_address); ?></div>
        </div>
        <div class="items">
            <div class="item-row">
                <span class="item-header-name">Item Name</span>
                <span class="item-header-details">Quantity</span>
                <span class="item-header-details">Unit Price</span>
                <span class="item-header-details">Net Amount</span>
            </div>
            <hr class="dark horizontal my-0">
            <br>
            <?php foreach($product_info as $key => $row): ?>
            <div class="item-row">
                <span class="item-name"><?php echo($product_info[$key][0]); ?></span>
                <span class="item-quantity"><?php echo($product_info[$key][2]); ?></span>
                <span class="item-unit-price">£<?php echo($product_info[$key][1]); ?></span>
                <span class="item-total">£<?php echo($product_info[$key][1] * $product_info[$key][2]); ?></span>
                <?php $total += $product_info[$key][1] * $product_info[$key][2] ?>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="total">
            <span class="label">Total:</span>
            <span class="value">£<?php echo($total); ?></span>
        </div>
        <br>
        <div class="address">
            <div class="address-details"><b>Estimated Delivery Date:</b></div>
            <div class="address-details"><?php echo($delivery_date); ?></div>
        </div>
    </div>
</body>

</html>
<script>
console.log(selected);
</script>