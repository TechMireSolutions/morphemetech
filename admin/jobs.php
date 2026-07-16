<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
include '../db_connection.php';

// Check for delete action
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql_delete = "DELETE FROM jobs WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $delete_id);
    $stmt_delete->execute();
    $stmt_delete->close();
    header("Location: jobs.php");
    exit;
}

$sql = "SELECT id, title, location, type, created_at FROM jobs ORDER BY created_at DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs</title>
    <style>
        .admin-container {
            width: 95%;
            margin: 20px auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .action-btn {
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-size: 12px;
            margin-right: 5px;
        }
        .edit-btn { background-color: #17a2b8; }
        .delete-btn { background-color: #dc3545; }
        .add-job-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<div class="admin-container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Manage Job Listings</h2>
        <div style="text-align: right;">
            <a href="index.php" style="padding: 10px; margin-right: 5px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 4px;">View Applications</a>
            <a href="logout.php" style="padding: 10px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 4px;">Logout</a>
        </div>
    </div>
    
    <a href="manage_job.php" class="add-job-btn">Add New Job</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Job Title</th>
                <th>Location</th>
                <th>Type</th>
                <th>Created Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                    echo "<td>";
                    echo "<a href='manage_job.php?edit_id=" . $row['id'] . "' class='action-btn edit-btn'>Edit</a>";
                    echo "<a href='jobs.php?delete_id=" . $row['id'] . "' class='action-btn delete-btn' onclick='return confirm(\"Are you sure you want to delete this job listing?\");'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No job listings found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
<?php
$conn->close();
?>