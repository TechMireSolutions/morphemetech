<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
include '../db_connection.php';

$job = null;
$page_title = "Add New Job";

// Agar edit request hai to job details fetch karein
if (isset($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
    $job_id = intval($_GET['edit_id']);
    $page_title = "Edit Job Listing";
    $sql = "SELECT * FROM jobs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $job = $result->fetch_assoc();
    $stmt->close();
}

// Form submission handle karein
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;
    $title = $_POST['title'];
    $location = $_POST['location'];
    $type = $_POST['type'];
    $description = $_POST['description'];

    if ($job_id > 0) {
        // Edit existing job
        $sql = "UPDATE jobs SET title=?, location=?, type=?, description=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $title, $location, $type, $description, $job_id);
    } else {
        // Add new job
        $sql = "INSERT INTO jobs (title, location, type, description) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $title, $location, $type, $description);
    }
    
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header("Location: jobs.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .form-container {
            width: 80%;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-container input, .form-container select, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-container button {
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-container .cancel-btn {
            background-color: #6c757d;
            text-decoration: none;
            padding: 10px 15px;
            display: inline-block;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2><?php echo $page_title; ?></h2>
    <form action="manage_job.php" method="POST">
        <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job['id'] ?? ''); ?>">
        
        <label for="title">Job Title:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($job['title'] ?? ''); ?>" required>
        
        <label for="location">Location:</label>
        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($job['location'] ?? ''); ?>" required>
        
        <label for="type">Job Type:</label>
        <select id="type" name="type" required>
            <option value="Full-Time" <?php echo ($job['type'] ?? '') == 'Full-Time' ? 'selected' : ''; ?>>Full-Time</option>
            <option value="Part-Time" <?php echo ($job['type'] ?? '') == 'Part-Time' ? 'selected' : ''; ?>>Part-Time</option>
            <option value="Internship" <?php echo ($job['type'] ?? '') == 'Internship' ? 'selected' : ''; ?>>Internship</option>
            <option value="Contract" <?php echo ($job['type'] ?? '') == 'Contract' ? 'selected' : ''; ?>>Contract</option>
        </select>
        
        <label for="description">Job Description:</label>
        <textarea id="description" name="description" rows="10" required><?php echo htmlspecialchars($job['description'] ?? ''); ?></textarea>
        
        <button type="submit">Save Job</button>
        <a href="jobs.php" class="cancel-btn">Cancel</a>
    </form>
</div>

</body>
</html>