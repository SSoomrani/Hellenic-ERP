<?php
session_start();

if (isset($_POST['delete'])) {
    delete();
}
if (isset($_POST['add'])) {
    add();
}
if (isset($_POST['append'])) {
    append();
}
else {
    echo("ERROR: Inconclusive call...");
}

function add() {
    require 'dbh.php';
    $data_names = array();
    $field_names = array();
    $submitted_data = array();
    $table_name = $_POST['table_name'];

    $query = $conn->query('SHOW FULL COLUMNS FROM '. $table_name);
    while ($row = $query->fetch_assoc()) {
        if ($row['Extra'] == null) {
            $field_names[] = $row['Field'];
            $data_names[] = str_replace(' ', '', $row['Comment']);
        }
    }
    foreach ($field_names as $key => $field_name) {
        if (str_contains($field_names[$key], "date")) {
           $submitted_data[] = check_date($_POST[$field_names[$key]]);
        } else {
            $submitted_data[] = $_POST[$field_names[$key]];
        }
    }
    $query_string = 'INSERT INTO '. $table_name. ' (';
    for ($i = 0; $i < sizeof($field_names); $i++) {
        if ($_POST[$field_names[$i]] != "") {
            $query_string = $query_string. $field_names[$i]. ", ";
        }
    }
    $query_string = substr($query_string, 0, -2). ") VALUES (";
    for ($i = 0; $i < sizeof($field_names); $i++) {
        if ($_POST[$field_names[$i]] != "") {
            $query_string = $query_string. "'". $submitted_data[$i]. "', ";
        }
    }
    $query_string = substr($query_string, 0, -2). ");";
    try {
        $conn->query($query_string);
        synchronise($conn, $table_name);
    }
    catch (Exception $e) {
        $_SESSION["mysql_error"] = "Error description: ". $conn -> error;
        $_SESSION["error_type"] = "add";
        $_SESSION["submitted_data"] = $submitted_data;
    }
    header("Location: {$_SERVER["HTTP_REFERER"]}");
    exit();
}
function append() {
    require 'dbh.php';

    $dataNames = array();
    $fieldNames = array();
    $tableName = $_POST['table_name'];
    $query = $conn->query('SHOW FULL COLUMNS FROM '. $tableName);

    while($row = $query->fetch_assoc()) {
        if ($row['Extra'] == null) {
            $fieldNames[] = $row['Field'];
            $dataNames[] = str_replace(' ', '', $row['Comment']);
        }
    }
    $queryString = "UPDATE ". $tableName. " SET ";

    for ($i = 0; $i < sizeof($fieldNames) - 1; $i++) {
        if ($_POST[$fieldNames[$i]] == "") {
            $queryString = $queryString. $fieldNames[$i]. " = ";
            $queryString = $queryString. "NULL, ";
        }
        else {
            $queryString = $queryString. $fieldNames[$i]. " = '";
            $queryString = $queryString. $_POST[$fieldNames[$i]]. "', ";
        }
    }
    if ($_POST[$fieldNames[sizeof($fieldNames) - 1]] == "") {
        $queryString = $queryString. $fieldNames[sizeof($fieldNames) - 1]. " = ";
        $queryString = $queryString. "NULL WHERE ID = ". $_POST['id']. ";";
    }
    else {
        $queryString = $queryString. $fieldNames[sizeof($fieldNames) - 1]. " = '";
        $queryString = $queryString. $_POST[$fieldNames[sizeof($fieldNames) - 1]]. "' WHERE ID = ". $_POST['id']. ";";
    }
    try {
        $conn->query($queryString);
    }
    catch (Exception $e) {
        $_SESSION["mysql_error"] = "Error description: ". $conn -> error;
        $_SESSION["row_id"] = $_POST['id'];
        $_SESSION["error_type"] = "edit";
    }
    header("Location: {$_SERVER["HTTP_REFERER"]}");
    exit();
}
function delete() {
    require 'dbh.php';
    $tableName = $_POST['table_name'];
    $ids = $_POST['id'];
    if (str_contains($ids, ",")) {
        $id_array = explode(',', $ids);
        foreach ($id_array as $id) {
            $query_string = "DELETE FROM $tableName WHERE ID = '$id'";
            try {
                $conn->query($query_string);
            }
            catch (Exception $e) {
                $_SESSION["mysql_error"] = "Error description: ". $conn -> error;
                $_SESSION["row_id"] = $_POST['id'];
                header("Location: {$_SERVER["HTTP_REFERER"]}");
                exit();
            }
        }
    }
    else {
        $query_string = "DELETE FROM $tableName WHERE ID = '$ids'";
        try {
            $conn->query($query_string);
        }
        catch (Exception $e) {
            $_SESSION["mysql_error"] = "Error description: ". $conn -> error;
            $_SESSION["row_id"] = $_POST['id'];
        }
    }
    header("Location: {$_SERVER["HTTP_REFERER"]}");
    exit();
}

function check_date($original_date) {
    return date("Y-m-d", strtotime($original_date));
}
function synchronise($conn, $table_name) {
    run_query($conn, "SET information_schema_stats_expiry = 0");
    $id = get_row_contents($conn, "SELECT auto_increment from information_schema.tables WHERE table_name = '".$table_name."' AND table_schema = DATABASE()")[0][0] - 1;
    switch ($table_name) {
        case "items_invoiced":
            $invoice_id = get_row_contents($conn, "SELECT invoice_id FROM items_invoiced WHERE id = '".$id."'")[0][0];
            $invoiced_item_data = get_row_contents($conn, "SELECT item_id, quantity, vat_charge FROM items_invoiced WHERE invoice_id = '".$invoice_id."'");
            $total = 0;
            foreach ($invoiced_item_data as $key => $value) {
                $current_total = 0;
                $price[] = get_row_contents($conn, "SELECT list_price FROM items WHERE id = '".$invoiced_item_data[$key][0]."'")[0][0];
                $current_total += $price[$key] * $invoiced_item_data[$key][1];
                if ($invoiced_item_data[$key][2] == "1") {
                  $current_total += ($current_total * 0.2);
                }
                $total += $current_total;
            }
            run_query($conn, "UPDATE invoices SET total = '".$total."', VAT = '".($total * 0.2)."', net_value = '".$total-($total*0.2)."' WHERE id = '".$invoice_id."'");
            break;
    }
}
function get_row_contents($conn, $query_string) {
    $query = $conn->query($query_string);
    $contents = $query->fetch_all();
    return $contents;
 }
 function run_query($conn, $query) {
    $conn->query($query);
  }
?>