<?php
$servername = "localhost"; // Tumhara server name, local development ke liye 'localhost' hi rahega
$username = "u703068112_morphemetech"; // Tumhara MySQL username
$password = "Morphemetech110$$"; // Tumhara MySQL password
$dbname = "u703068112_morphemetech"; // Database ka naam jo tumne banaya hai

// Bypass DB for local UI testing (Dummy Classes)
class DummyResult {
    public $num_rows = 0;
    public function fetch_assoc() { return false; }
}
class DummyConnection {
    public $connect_error = null;
    public function query($sql) { return new DummyResult(); }
    public function prepare($sql) { return false; }
    public function close() {}
}

try {
    // Throw exceptions on error so we can catch them locally
    mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);
    $conn = new mysqli($servername, $username, $password, $dbname);
} catch (Exception $e) {
    // Connection failed (e.g., local server), use dummy connection to load UI
    $conn = new DummyConnection();
}
?>