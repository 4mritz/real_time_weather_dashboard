<?php

$servername = "localhost";
$database = "aitfm";
$username = "root";
$password = "";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>