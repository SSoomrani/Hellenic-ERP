<?php //code to connect to the database

$server_name = "localhost";
$dB_username = "root";
$dB_password = "password";
$dB_name = "hellenic";

$conn = mysqli_connect($server_name, $dB_username, $dB_password, $dB_name);

if (!$conn) { //if the connection failed
    echo("Connection failed: ".mysqli_connect_error());
}
?>