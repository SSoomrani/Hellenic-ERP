<?php
    $table_info = get_table_info($conn, $table_name);
    $formatted_names = $table_info[0];
    $field_names = $table_info[1];
    $editable_formatted_names = $table_info[2];
    $editable_field_names = $table_info[3];
    $required_fields = $table_info[4];
?>

<div id="add-form" class="popup-form">
  <form class="popup-form-content animate" action="dbh/manageData.php" method="post">
    <input type="hidden" name="table_name" value="<?php echo($table_name);?>">
    <div class="popup-form-container" id="addForm">
      <p id="add_error"></p>
      <br>
      <?php foreach($editable_formatted_names as $key => $value): ?>
          <label><?php echo "$editable_formatted_names[$key]: "; ?></label>
          <br>
          <?php if (in_array($editable_field_names[$key], $required_fields)): ?>
            <input class="form-control" required id="<?php echo str_replace(' ', '', $editable_formatted_names[$key]); ?>" type="text" name="<?php echo($editable_field_names[$key]); ?>">
          <?php else: ?>
            <input class="form-control" id="<?php echo str_replace(' ', '', $editable_formatted_names[$key]); ?>" type="text" name="<?php echo($editable_field_names[$key]); ?>">
          <?php endif; ?>
      <?php endforeach; ?>
    </div>
    <div class="popup-form-container popup-form-container-footer">
      <p onclick=hideForm(this);>Cancel</p>
      <button name="add" type="submit" style="float: right"><p>Submit</p></button>
    </div>
  </form>
</div>