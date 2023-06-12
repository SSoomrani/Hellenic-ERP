<?php
    session_start();

    $table_name = $_SESSION["current_table"] = "stocked_items";

    require 'dbh/initialise.php';
    require 'dbh/customer_data.php';

    $table_info = get_table_info($conn, $table_name);
    $formatted_names = $table_info[0];
    $field_names = $table_info[1];
    $editable_formatted_names = $table_info[2];
    $editable_field_names = $table_info[3];

    $rows = get_table_contents($conn, $table_name);

    $item_names = get_row_contents($conn, "SELECT `item_name` FROM `items`");

    $edit_error_info = get_edit_error_info($conn, $table_name);
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <script src="js/form_handler.js"></script>
    <script src="js/table_handler.js"></script>
    <script src="js/element_loader.js"></script>
    <script src="js/data_handler.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
</head>
<body>
    <div id="nav-placeholder"></div>
    <div class="main main-content">
        <h5>Pages / <p class="inline-shallow"><?php echo(ucfirst($table_name)); ?></p></h5>
        <div id="widget-placeholder" class="grid-container">
            <div class="card item12">
                <?php include 'templates/table.php'; ?>
            </div>
        </div>
    </div>
    <div id="form-placeholder">
        <?php include 'templates/edit_form.php'; ?>
        <?php include 'templates/forms.php'; ?>
    </div> 
    <div id="add-form" class="popup-form">
        <form class="popup-form-content-medium animate" action="dbh/manageData.php" method="post">
            <input name="item_name" type="hidden" id="item-name"></input>
            <input type="hidden" name="table_name" value="<?php echo($table_name);?>">
            <div class="popup-form-container">
                <p id="add_error"></p>
                <br>
                <?php foreach($editable_formatted_names as $key => $value): ?>
                    <?php if ($editable_formatted_names[$key] != "Item ID"): ?>
                        <label><?php echo "$editable_formatted_names[$key]: "; ?></label>
                        <br>
                        <?php if (in_array($editable_field_names[$key], $required_fields)): ?>
                            <?php if ($editable_field_names[$key] == "item_name"): ?>
                                <select name="item_id" class="form-control" id="item-name-select" placeholder="Enter item name">
                                    <?php foreach($item_names as $key => $value): ?>
                                        <option value="<?php echo($key); ?>"><?php echo($item_names[$key][0]); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input class="form-control" required id="<?php echo str_replace(' ', '', $editable_formatted_names[$key]); ?>" type="text" name="<?php echo($editable_field_names[$key]); ?>">
                            <?php endif; ?>
                        <?php else: ?>
                            <input class="form-control" id="<?php echo str_replace(' ', '', $editable_formatted_names[$key]); ?>" type="text" name="<?php echo($editable_field_names[$key]); ?>">
                        <?php endif; ?>
                    <?php endif?>
                <?php endforeach; ?>
            </div>
            <div class="popup-form-container-small popup-form-container-footer">
            <p onclick=hideForm(this);>Close</p>
            <button name="add" type="submit" style="float: right"><p>Submit</p></button>
            </div>
        </form>
    </div>
</body>
</html>
<script>
    document.getElementById('item-name-select').value = "";
    watchAddSelect();

    $(document).ready(function () {
        $('select').selectize({
            sortField: 'text'
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        loadElement("sidenav.html", "nav-placeholder");
        loadElement("widgets.html", "widget-placeholder", populateWidgets);
        checkEditError();
    });


    function watchAddSelect() {
        var addSelect = document.getElementById('item-name-select');
        addSelect.onchange = (event) => {
            var value = event.target.options[event.target.selectedIndex].text;
            document.getElementById('item-name').value = value;
        }
    }
    function populateWidgets()
    {
        configureWidgets(1, "blank", "hourglass_empty", "blank", "blank", "blank");
        configureWidgets(2, "blank", "hourglass_empty", "blank", "blank", "blank");
        configureWidgets(3, "blank", "hourglass_empty", "blank", "blank", "blank");
        configureWidgets(4, "blank", "hourglass_empty", "blank", "blank", "blank");
    }

    function checkEditError() {
        var error = "<?php echo $edit_error_info[0]; ?>";
        var rowID = "<?php echo $edit_error_info[1]; ?>";
        if (error != "") {
            var errorMsg = document.getElementById("edit_error");
            errorMsg.innerText = error;
            if (rowID != -1) {
                displayEditForm(rowID - 1);
            }
            <?php session_unset(); ?>
        }
    }

</script>