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
    customers.forename,
    customers.surname,
    customers.delivery_address,
    customers.invoice_address,
    items.item_name,
    items.list_price,
    items_invoiced.quantity,
    customer_address.invoice_address_one,
    customer_address.invoice_address_two,
    customer_address.invoice_address_three
  FROM
    invoices
    INNER JOIN customers ON invoices.customer_id = customers.id
    INNER JOIN items_invoiced ON invoices.id = items_invoiced.invoice_id
    INNER JOIN items ON items_invoiced.item_id = items.id
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
   items_invoiced.quantity
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
?>