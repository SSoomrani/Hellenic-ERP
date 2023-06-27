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
function get_error_info() {
  $sql_error = "";
  if (isset($_SESSION['mysql_error'])) {
    $sql_error = $_SESSION['mysql_error'];
  }
  $row_id = -1;
  if (isset($_SESSION['row_id'])) {
    $row_id = $_SESSION['row_id'];
  }
  $type = "";
  if (isset($_SESSION['error_type'])) {
    $type = $_SESSION['error_type'];
  }
  return array ($sql_error, $row_id, $type);
}
function get_submitted_data() {
 $submitted_data = null;
 if (isset($_SESSION['submitted_data'])) {
    $submitted_data = $_SESSION['submitted_data'];
 }
 return $submitted_data;
}
function get_invoice_info($conn, $invoice_id) {
  $query_string = "SELECT
  invoices.title,
  invoices.due_date,
  invoices.net_value,
  invoices.total,
  invoices.vat,
  invoices.delivery_date,
  invoices.created_at,
  customers.forename,
  customers.surname,
  customers.outstanding_balance,
  customer_address.invoice_address_one,
  customer_address.invoice_address_two,
  customer_address.invoice_address_three,
  customer_address.invoice_postcode,
  customer_address.delivery_address_one,
  customer_address.delivery_address_two,
  customer_address.delivery_address_three,
  customer_address.delivery_postcode
FROM
  invoices
  INNER JOIN customers ON invoices.customer_id = customers.id
  INNER JOIN customer_address ON customer_address.customer_id = invoices.customer_id
WHERE
  invoices.id = ". $invoice_id;
  $query = $conn->query($query_string);
  $contents = $query->fetch_all();
  return $contents;
}
function get_invoice_products($conn, $invoice_id) {
  $query_string = "SELECT
  items.item_name,
  items.list_price,
  items_invoiced.quantity,
  items_invoiced.vat_charge
  FROM
  invoices
  INNER JOIN items_invoiced ON invoices.id = items_invoiced.invoice_id
  INNER JOIN items ON items_invoiced.item_id = items.id
  WHERE
  invoices.id = ". $invoice_id;
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

function pull_assoc($conn, $table_name) {
  $tables = get_tables($conn);
  foreach ($tables as $key => $table) {
    $query = $conn->query("SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME IS NOT NULL AND TABLE_SCHEMA = 'hellenic' AND TABLE_NAME = '".$tables[$key][0]."'");
    $contents[$tables[$key][0]] = $query->fetch_all();
  }
  var_dump($contents);
  $assoc_table = $contents[$table_name][0][2];
  $assoc_column = $contents[$table_name][0][1];
  $assoc_reference = $contents[$table_name][0][3];
  $query = $conn->query("SELECT * FROM '".$assoc_table."' WHERE '".$assoc_reference."' = ".$_POST[$assoc_column]."'");
  $results = $query->fetch_all();
}

?>