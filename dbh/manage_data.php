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
        var_dump($id_array);
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