<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hanna";
$port = 3307; // Change this to the custom port you have set (3307 in your case)

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname, $port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Rest of your PHP code to interact with the database

// Close the connection when you're done
//mysqli_close($conn);
?>