<?php
include '../db_connection.php';

// Search query aur filters ko get karein
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
$location = isset($_GET['location']) ? $conn->real_escape_string($_GET['location']) : '';
$employment_type = isset($_GET['employment_type']) ? $conn->real_escape_string($_GET['employment_type']) : '';

// Base SQL query
$sql = "SELECT * FROM jobs WHERE 1=1";

// Filters ko query mein add karein
if (!empty($search_query)) {
    $sql .= " AND (title LIKE '%$search_query%' OR description LIKE '%$search_query%')";
}
if (!empty($category)) {
    $sql .= " AND category = '$category'";
}
if (!empty($location)) {
    $sql .= " AND location = '$location'";
}
if (!empty($employment_type)) {
    $sql .= " AND employment_type = '$employment_type'";
}

$sql .= " ORDER BY category, title";

$result = $conn->query($sql);
$jobs = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $jobs[$row['category']][] = $row;
    }
}

// Result ko JSON format mein encode karke bhej dein
header('Content-Type: application/json');
echo json_encode($jobs);

$conn->close();
?>