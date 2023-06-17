<?php
    $table_info = get_table_info($conn, $table_name);
    $formatted_names = $table_info[0];
    $field_names = $table_info[1];
    $editable_formatted_names = $table_info[2];
    $editable_field_names = $table_info[3];
    $required_fields = $table_info[4];
?>

<div id="edit-form" class="popup-form">
  <form class="popup-form-content animate" action="dbh/manageData.php" method="post">
    <input type="hidden" id="edit-form-identity" name="id" value="">
    <input type="hidden" name="table_name" value="<?php echo($table_name);?>">
    <div class="popup-form-container" id="editForm">
      <p id="edit_error"></p>
      <br>
      <?php foreach($editable_formatted_names as $key => $value): ?>
          <label><?php echo "$editable_formatted_names[$key]: "; ?></label>
          <br>
          <input class="form-control" id="<?php echo strtoupper(str_replace(' ', '', $editable_formatted_names[$key]))."_edit"; ?>" type="text" name="<?php echo $editable_field_names[$key];?>" value="">
      <?php endforeach; ?>
      <div class="buttons">
      </div>
    </div>
    <div class="popup-form-container popup-form-container-footer">
      <p onclick=hideForm(this);>Cancel</p>
      <button name="append" type="submit" style="float: right"><p>Save</p></button>
    </div>
  </form>
</div>