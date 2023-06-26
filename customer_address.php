<?php
    session_start();

    $table_name = $_SESSION["current_table"] = "customer_address";

    require 'dbh/dbh.php';
    require 'dbh/initialise.php';
    require 'dbh/customer_data.php';

    $filter = "";

    $error_info = get_error_info();
    $submitted_data = get_submitted_data();
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
    </div>
</body>

</html>
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadElement("sidenav.html", "nav-placeholder");
    loadElement("toolbar.html", "widget-placeholder");
});

checkError();

function populateWidgets() {
    configureWidgets(1, "blank", "hourglass_empty", "blank", "blank", "blank");
    configureWidgets(2, "blank", "hourglass_empty", "blank", "blank", "blank");
    configureWidgets(3, "blank", "hourglass_empty", "blank", "blank", "blank");
    configureWidgets(4, "blank", "hourglass_empty", "blank", "blank", "blank");
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
                for (var i = 0, element; element = elements[i++];) {
                    element.value = submittedData[i-2];
                }
                var errorMsg = document.getElementById("add_error");
                errorMsg.innerText = error;
                document.getElementById('add-form-container').style.display='block';
            }
            <?php session_unset(); ?>
        }
    }
</script>