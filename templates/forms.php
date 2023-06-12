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
    <div id="edit-form" class="popup-form">
      <form class="popup-form-content animate" action="dbh/manageData.php" method="post">
        <input type="hidden" id="identity" name="id" value="">
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
    <div id="filter-form" class="popup-form">
      <form class="popup-form-content-medium animate" action="dbh/manageData.php" method="post">
        <div class="popup-form-container">
            <h2>Advanced Filter</h2>
            <label>Select Column</label>
            <select class="form-control" id="column_select">
                <?php foreach($formatted_names as $key => $value): ?>
                    <option value="<?php echo($key); ?>"><?php echo(ucfirst($formatted_names[$key])); ?></option>
                <?php endforeach; ?>
            </select>
            <input id="advanced-filter" style="font-family:Source Code Pro, FontAwesome" placeholder=" &#xF002;  Search for entries..." type="search" class="form-control" onfocus=""></input>
        </div>
        <div class="popup-form-container-small popup-form-container-footer">
          <p onclick=hideForm(this);>Close</p>
          <p onclick=filterTable(); style="float: right">Search</p>
        </div>
      </form>
    </div>