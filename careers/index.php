<?php
// Database connection file ko include karein
include '../db_connection.php';

// Database se saari jobs fetch karein
$sql = "SELECT * FROM jobs ORDER BY category, title";
$result = $conn->query($sql);

// Jobs ko category-wise group karein
$jobs_by_category = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $jobs_by_category[$row['category']][] = $row;
    }
}

// Categories ko database se fetch karein
$categories_sql = "SELECT DISTINCT name FROM categories ORDER BY name ASC";
$categories_result = $conn->query($categories_sql);

// Locations ko database se fetch karein
$locations_sql = "SELECT DISTINCT location FROM jobs ORDER BY location ASC";
$locations_result = $conn->query($locations_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Careers at Morpheme Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/careers.css">
</head>
<body>

    <header class="header">
      <div class="header-container">
        <a href="index.html" class="header-logo">
          <img src="/assets/images/headerlogo1.png" alt="" />
        </a>

        <nav class="header-nav-center">
          <ul class="nav-links">
            <li><a href="#About">About</a></li>
            <li><a href="#Services">Services</a></li>
            <li><a href="#Team">Team</a></li>
            <li><a href="careers/index.php">Careers</a></li>
            <li><a href="#Contact">Contact Us</a></li>
          </ul>
        </nav>

        <button class="hamburger-menu" id="hamburgerMenu">
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>
    </header>

<main class="careers-page-content" style="padding-top: 95px">
    <div class="header-banner">
        </div>

    <div class="search-and-filter-bar">
        <input type="text" id="job-search" placeholder="Search jobs...">
        <select id="category-filter">
            <option value="">Category</option>
            <?php
            if ($categories_result->num_rows > 0) {
                while($row = $categories_result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['name']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                }
            }
            ?>
        </select>
        <select id="location-filter">
            <option value="">Location</option>
            <?php
            if ($locations_result->num_rows > 0) {
                while($row = $locations_result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['location']) . "'>" . htmlspecialchars($row['location']) . "</option>";
                }
            }
            ?>
        </select>
        <select id="employment-type-filter">
            <option value="">Employment Type</option>
            <option value="Full-time">Full-time</option>
            <option value="Part-time">Part-time</option>
            <option value="Internship">Internship</option>
            <option value="Contract">Contract</option>
        </select>
    </div>

    <div id="job-listings">
        <?php
        if (!empty($jobs_by_category)) {
            foreach ($jobs_by_category as $category => $jobs) {
                echo "<div class='job-category-section'>";
                echo "<h2>" . htmlspecialchars($category) . "</h2>";
                foreach ($jobs as $job) {
                    echo "<a href='job-details.php?id=" . htmlspecialchars($job['id']) . "' class='job-listing-item'>";
                    echo "<div class='job-info'>";
                    echo "<h3>" . htmlspecialchars($job['title']) . "</h3>";
                    echo "<p><span>" . htmlspecialchars($job['employment_type']) . "</span> | <span>" . htmlspecialchars($job['location']) . "</span></p>";
                    echo "</div>";
                    echo "<span class='arrow-icon'>&gt;</span>";
                    echo "</a>";
                }
                echo "</div>";
            }
        } else {
            echo "<p>No jobs available at the moment.</p>";
        }
        ?>
    </div>
</main>

<footer>
    </footer>

<script src="../assets/js/careers.js"></script>

</body>
</html>

<?php
$conn->close();
?>