<?php
// Database connection file ko include karein
include '../db_connection.php';

// URL se job ID ko surakshit tarike se lein
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$job = null; // Initialize $job to null

if ($job_id > 0) {
    // Database se job details fetch karein
    // Use prepared statements for security
    $sql = "SELECT * FROM jobs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    // Bind the job ID as an integer
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $job = $result->fetch_assoc();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $job ? htmlspecialchars($job['title']) . ' - Job Details' : 'Job Not Found'; ?> - Morpheme Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/careers.css">
    <style>
        /* Add any specific styles for job details page here */
        .job-description {
            margin-top: 20px;
            line-height: 1.6;
        }
        .job-description h2 {
            margin-bottom: 10px;
            color: #333;
        }
        .job-description .section-content {
            margin-bottom: 20px;
            padding-left: 15px; /* Indentation for lists etc. */
        }
    </style>
</head>
<body>

<header>
    </header>

<main class="job-details-page-content">
    <?php if ($job) : ?>
        <a href="./" class="back-link">&larr; See all jobs</a>
        <h1><?php echo htmlspecialchars($job['title']); ?></h1>
        <div class="job-meta">
            <span><?php echo htmlspecialchars($job['employment_type']); ?> | <?php echo htmlspecialchars($job['location']); ?></span>
        </div>

        <?php if (!empty($job['description'])) : ?>
            <div class="job-section job-description">
                <h2>Job Description</h2>
                <div class="section-content">
                    <?php
                        // Display description as HTML, not plain text, as it might contain formatting
                        echo $job['description'];
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <?php
        $sections = [
            'key_points' => 'Key Points',
            'main_responsibilities' => 'Main Responsibilities',
            'your_profile' => 'Your Profile',
            'required_experience' => 'Required Experience'
        ];

        foreach ($sections as $column => $title) {
            if (!empty($job[$column])) {
                echo "<div class='job-section'>";
                echo "<h2>" . htmlspecialchars($title) . "</h2>";
                echo "<div class='section-content'>";
                // Using nl2br to convert newlines to HTML line breaks if description is plain text
                // If description field contains HTML, nl2br might not be needed. Adjust as per your stored data.
                echo nl2br(htmlspecialchars($job[$column]));
                echo "</div>";
                echo "</div>";
            }
        }
        ?>

        <a href="apply.php?job_id=<?php echo htmlspecialchars($job['id']); ?>" class="apply-button">Apply now</a>
    <?php else : ?>
        <p>Sorry, the job you are looking for does not exist or could not be loaded.</p>
    <?php endif; ?>
</main>

<footer>
    </footer>

</body>
</html>
<?php
// Connection close karein
$conn->close();
?>