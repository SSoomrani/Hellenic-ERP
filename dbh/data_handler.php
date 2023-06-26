<?
 function get_table_contents($conn, $table_name, $filter) {
    if ($filter == "") {
      $query = $conn->query('SELECT * FROM '. $table_name);
      return $query->fetch_all(MYSQLI_ASSOC);
    } else {
      $query = $conn->query("SELECT * FROM ".$table_name." ".$filter);
      return $query->fetch_all(MYSQLI_ASSOC);
    }

 }

 function get_row_contents($conn, $query_string) {
    $query = $conn->query($query_string);
    $contents = $query->fetch_all();
    return $contents;
 }
function echo_query($conn, $query) {
  $query = $conn->query($query);
  var_dump($query->fetch_all());
}

function run_query($conn, $query) {
  $conn->query($query);
}

function get_types($conn, $table_name) {
  $query = $conn->query("SHOW FIELDS FROM ".$table_name);
  $contents = $query->fetch_all();
  foreach($contents as $item) {
    if ($item[5] == null) {
      $types[] = $item[1];
    }
  }
  return $types;
}

function get_tables($conn) {
  $query = $conn->query("SHOW TABLES");
  $contents = $query->fetch_all();
  return $contents;
}

function sum_values($values) {
  $total = 0;
  foreach ($values as $value) {
    $total += $value[0];
  }
  return $total;
}



?>