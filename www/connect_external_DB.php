<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cashback_hanna_external";

// Create connection
$conn_ex = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn_ex) {
    die("Connection failed: " . mysqli_connect_error());
}
