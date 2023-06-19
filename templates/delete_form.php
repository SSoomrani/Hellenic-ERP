<?php
    $table_info = get_table_info($conn, $table_name);
    $formatted_names = $table_info[0];
    $field_names = $table_info[1];
    $editable_formatted_names = $table_info[2];
    $editable_field_names = $table_info[3];
    $required_fields = $table_info[4];
?>
<div id="delete-form" class="popup-form">
    <form class="popup-form-content-small animate" action="dbh/manageData.php" method="post">
        <input type="hidden" id="delete_id" name="id" value="">
        <input type="hidden" name="table_name" value="<?php echo($table_name);?>">
        <div class="popup-form-container" id="editForm">
            <p>Are you sure you want to delete?</p>
        </div>
        <div class="popup-form-container-small popup-form-container-footer">
            <p onclick=hideForm(this);>Cancel</p>
            <button name="delete" type="submit" style="float: right"><p>Delete</p></button>
        </div>
    </form>
</div>