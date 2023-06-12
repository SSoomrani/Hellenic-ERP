<?php
session_start();

if (isset($_POST['add'])) {
    add();
}
if (isset($_POST['append'])) {
    append();
}
if (isset($_POST['delete'])) {
    delete();
}
if (isset($_POST['change_table'])) {
    change_table();
}
if (isset($_REQUEST['q'])) {
    change_table();
}
if (isset($_POST['email_invoices'])) {
    email_invoices();
}
else {
    echo("ERROR: Inconclusive call...");
}

function add_customer() {
    $forename = $_POST['forename'];
    $surname = $_POST['surname'];
    $phone_number_primary = $_POST['phone_number_primary'];
    $email = $_POST['email'];
    $customer_type = $_POST['customer_type'];

    require 'dbh.php';

    mysqli_query($conn, "INSERT INTO customers (forename, surname, phone_number_primary, email, customer_type) VALUES ('$forename', '$surname', '$phone_number_primary', '$email', '$customer_type')");
    header("Location: ./customer.php");
    exit();
}
function add() {
    require 'dbh.php';
    $data_names = array();
    $field_names = array();
    $table_name = $_POST['table_name'];
    $query = $conn->query('SHOW FULL COLUMNS FROM '. $table_name);
    while ($row = $query->fetch_assoc()) {
        if ($row['Extra'] == null) {
            $field_names[] = $row['Field'];
            $data_names[] = str_replace(' ', '', $row['Comment']);
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
            $query_string = $query_string. "'". $_POST[$field_names[$i]]. "', ";
        }
    }
    $query_string = substr($query_string, 0, -2). ");";

    try {
        $conn->query($query_string);
    }
    catch (Exception $e) {
        $_SESSION["mysql_error"] = "Error description: ". $conn -> error;
        $_SESSION["row_id"] = $_POST['id'];
    }
    echo($conn->error);
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
    }
    header("Location: {$_SERVER["HTTP_REFERER"]}");
    exit();
}
function delete() {
    require 'dbh.php';
    $tableName = $_POST['table_name'];
    $id = $_POST['id'];
    $query_string = "DELETE FROM $tableName WHERE ID = '$id'";
    try {
        $conn->query($query_string);
    }
    catch (Exception $e) {
        $_SESSION["mysql_error"] = "Error description: ". $conn -> error;
        $_SESSION["row_id"] = $_POST['id'];
    }
    header("Location: {$_SERVER["HTTP_REFERER"]}");
    exit();
}
function change_table() {
    $table_name = $_REQUEST["q"];
    $_SESSION["current_table"] = $table_name;
    echo("Table changed");
    // header("Location: ./view.php");
    // exit();
}
function email_invoices() {
    
}