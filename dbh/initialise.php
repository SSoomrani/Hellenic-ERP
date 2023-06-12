<?php

 function get_row_count($conn, $query_string) {
    $query = $conn->query($query_string);
    return $query->num_rows;
 }
 function get_table_info($conn, $table_name) {
    $query = $conn->query('SHOW FULL COLUMNS FROM '. $table_name);
    $formatted_names = array();
    $field_names = array();
    $editable_formatted_names = array();
    $editable_field_names = array();
    $required_fields = array();
    while($row = $query->fetch_assoc()) {
        $formatted_names[] = $row['Comment'];
        $field_names[] = $row['Field'];
        if ($row['Extra'] == null)
        {
          $editable_formatted_names[] = $row['Comment'];
          $editable_field_names[] = $row['Field'];
          if ($row['Null'] == "NO") {
            $required_fields[] = $row['Field'];
          }
        }
    }
    return array(
        0 => $formatted_names,
        1 => $field_names,
        2 => $editable_formatted_names,
        3 => $editable_field_names,
        4 => $required_fields,
    );
 }
 function get_table_contents($conn, $table_name) {
    $query = $conn->query('SELECT * FROM '. $table_name);
    return $query->fetch_all(MYSQLI_ASSOC);
 }

 function get_row_contents($conn, $query_string) {
    $query = $conn->query($query_string);
    $contents = $query->fetch_all();

    return $contents;
 }
 function get_edit_error_info() {
    $sql_error = "";
    if (isset($_SESSION['mysql_error'])) {
      $sql_error = $_SESSION['mysql_error'];
    }
    $row_id = -1;
    if (isset($_SESSION['row_id'])) {
      $row_id = $_SESSION['row_id'];
    }
    return array ($sql_error, $row_id);
 }
?>