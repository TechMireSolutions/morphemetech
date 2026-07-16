<?php
$servername = "localhost"; // Tumhara server name, local development ke liye 'localhost' hi rahega
$username = "u703068112_morphemetech"; // Tumhara MySQL username
$password = "Morphemetech110$$"; // Tumhara MySQL password
$dbname = "u703068112_morphemetech"; // Database ka naam jo tumne banaya hai

// Connection create karein
$conn = new mysqli($servername, $username, $password, $dbname);

// Connection check karein
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>