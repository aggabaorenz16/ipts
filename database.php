<?php

$host = 'localhost';
$user = 'root';
$pass = 'admin';
$db = 'IPT';

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// echo "Connected successfully";

?>
