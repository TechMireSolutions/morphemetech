<?php
$servername = "localhost"; // Tumhara server name, local development ke liye 'localhost' hi rahega
$username = "morphemetech"; // Tumhara MySQL username
$password = "569ad51aa04f6"; // Tumhara MySQL password
$dbname = "morphemetech"; // Database ka naam jo tumne banaya hai

// Connection create karein
$conn = new mysqli($servername, $username, $password, $dbname);

// Connection check karein
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>