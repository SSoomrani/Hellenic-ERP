<?php

function initialise() {
  if (isset($_SESSION['logged_in'])) {
    require 'dbh/dbh.php';
    require 'dbh/customer_data.php';
    require 'dbh/data_handler.php'; 
    return $conn;
  } else {
    header("Location: login.php");
  }
}

function clear_error_session() {
  unset($_SESSION['error_type']);
  unset($_SESSION['mysql_error']);
  unset($_SESSION['row_id']);
  unset($_SESSION['error_type']);
  unset($_SESSION['submitted_data']);
}

?>