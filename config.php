<?php

$servername = "localhost"; // Database server
$username = "root"; // Database username
$password = ""; // Database password
$database = "employeedb"; // Database name

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}	
echo "Connected successfully";

?>
