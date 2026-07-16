<?php
session_start();

// Database connection file include karein
include '../db_connection.php';

// Function to handle login (for a simple admin panel)
function handle_login() {
    $username = 'admin';
    $password = 'password'; 

    if (isset($_POST['username']) && isset($_POST['password'])) {
        if ($_POST['username'] === $username && $_POST['password'] === $password) {
            $_SESSION['logged_in'] = true;

            // If Remember Me is checked → set cookies for 7 days
            if (!empty($_POST['remember'])) {
                setcookie("admin_username", $username, time() + (86400 * 7), "/");
                setcookie("admin_password", $password, time() + (86400 * 7), "/");
            }

            header("Location: index.php"); // Redirect to the admin page
            exit();
        } else {
            return "Invalid username or password.";
        }
    }
    return "";
}

// --- Auto login using cookies ---
if (!isset($_SESSION['logged_in']) && isset($_COOKIE['admin_username']) && isset($_COOKIE['admin_password'])) {
    if ($_COOKIE['admin_username'] === 'admin' && $_COOKIE['admin_password'] === 'password') {
        $_SESSION['logged_in'] = true;
    }
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$login_error = "";
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $login_error = handle_login();
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        // Display login form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - MorphemeTech</title>
    <link rel="shortcut icon" href="/assets/images/footerlogo.png" type="image/x-icon" />
    <style>
         @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #007bff;
            --primary-dark: #0056b3;
            --background-dark: #0A1F44;
            --card-background: #152B55;
            --text-light: #f0f4f8;
            --input-border: #3c5478;
            --error-red: #ff6b6b;
            --box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-dark);
            color: var(--text-light);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            overflow: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://morphemetech.co.uk/assets/images/whatrwedo.jpeg');
            background-size: cover;
            background-position: center;
            filter: blur(8px) brightness(0.6);
            z-index: -1;
            animation: fadeIn 2s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .login-container {
            background-color: var(--card-background);
            padding: 40px;
            border-radius: 12px;
            box-shadow: var(--box-shadow);
            width: 90%;
            max-width: 400px;
            animation: slideIn 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            transition: transform 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px) scale(0.95);
                opacity: 0;
            }
            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }
        
        .login-container:hover {
            transform: translateY(-5px);
        }

        .logo {
            margin-bottom: 20px;
            animation: bounceIn 1s ease-out;
        }

        @keyframes bounceIn {
            from, 20%, 40%, 60%, 80%, to {
                animation-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
            }
            0% { opacity: 0; transform: scale3d(.3, .3, .3); }
            20% { transform: scale3d(1.1, 1.1, 1.1); }
            40% { transform: scale3d(.9, .9, .9); }
            60% { opacity: 1; transform: scale3d(1.03, 1.03, 1.03); }
            80% { transform: scale3d(.97, .97, .97); }
            to { opacity: 1; transform: scale3d(1, 1, 1); }
        }

        .logo img {
            max-width: 150px;
            filter: brightness(0) invert(1);
        }

        h2 {
            font-weight: 600;
            margin-bottom: 25px;
            color: var(--text-light);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .input-group {
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            background-color: transparent;
            border: 1px solid var(--input-border);
            border-radius: 8px;
            color: var(--text-light);
            font-size: 16px;
            transition: all 0.3s ease;
            outline: none;
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.3);
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background-image: linear-gradient(45deg, var(--primary-color), #0d6efd);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        }

        .error-message {
            color: var(--error-red);
            font-size: 14px;
            margin-bottom: 15px;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0% { transform: translateX(0); }
            20% { transform: translateX(-10px); }
            40% { transform: translateX(10px); }
            60% { transform: translateX(-10px); }
            80% { transform: translateX(10px); }
            100% { transform: translateX(0); }
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .login-container {
                padding: 30px;
            }
            h2 {
                font-size: 24px;
            }
            input[type="text"],
            input[type="password"] {
                font-size: 14px;
            }
            .btn-login {
                font-size: 16px;
            }
        }
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .remember-me input {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="/assets/images/headerlogo1.png" alt="MorphemeTech Logo" />
        </div>
        <h2>Admin Login</h2>
        <?php if (!empty($login_error)) echo '<p class="error-message">' . htmlspecialchars($login_error) . '</p>'; ?>
    <form action="index.php" method="POST">
            <div class="input-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="remember-me">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember Me</label>
            </div>
            <button type="submit" class="btn-login">Login</button>
        </form>
    </div>
</body>
</html>
<?php
        exit(); // Stop script execution
    }
}

// Log out logic
if (isset($_GET['logout'])) {
    session_destroy();

    // Delete cookies when logging out
    if (isset($_COOKIE['admin_username'])) {
        setcookie("admin_username", "", time() - 3600, "/");
    }
    if (isset($_COOKIE['admin_password'])) {
        setcookie("admin_password", "", time() - 3600, "/");
    }

    header("Location: index.php");
    exit();
}

// Default view set karein
$view = isset($_GET['view']) ? $_GET['view'] : 'applications';
$message = ''; // Initialize message variable

// Check for redirection message
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}

// Handle Delete Application Action
if ($view == 'applications' && isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);

    // Fetch file paths BEFORE deleting the record
    $sql_files = "SELECT cv_path, work_sample_path FROM applications WHERE id = ?";
    $stmt_files = $conn->prepare($sql_files);
    $stmt_files->bind_param("i", $id);
    $stmt_files->execute();
    $result_files = $stmt_files->get_result();
    $files = $result_files->fetch_assoc();
    $stmt_files->close();

    // Delete application record
    $sql_delete = "DELETE FROM applications WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id);
    if ($stmt_delete->execute()) {
        // Delete associated files if they exist
        if ($files) {
            if (!empty($files['cv_path']) && file_exists('../' . $files['cv_path'])) {
                unlink('../' . $files['cv_path']);
            }
            if (!empty($files['work_sample_path']) && file_exists('../' . $files['work_sample_path'])) {
                unlink('../' . $files['work_sample_path']);
            }
        }
        $message = "Application deleted successfully!";
    } else {
        $message = "Error deleting application: " . $stmt_delete->error;
    }
    $stmt_delete->close();
    header("Location: index.php?view=applications&message=" . urlencode($message));
    exit;
}

// --- Fetch Data for Display ---

// Fetch all applications
$applications_sql = "SELECT
                        a.id,
                        a.job_id,
                        j.title AS job_title,
                        a.first_name,
                        a.last_name,
                        a.email,
                        a.phone,
                        a.country,
                        a.linkedin_profile,
                        a.cv_path,
                        a.work_sample_path,
                        a.submitted_at
                    FROM applications AS a
                    JOIN jobs AS j ON a.job_id = j.id
                    ORDER BY a.submitted_at DESC";
$applications_result = $conn->query($applications_sql);

// Fetch all jobs for management
$jobs_sql = "SELECT id, title, location, employment_type, category, description, created_at FROM jobs ORDER BY created_at DESC";
$jobs_result = $conn->query($jobs_sql);

// Fetch all categories for management
$categories_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$categories_result = $conn->query($categories_sql);

// Prepare edit job data if requested
$edit_job = null;
if ($view == 'manage_jobs' && isset($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sql_edit_job = "SELECT * FROM jobs WHERE id = ?";
    $stmt_edit = $conn->prepare($sql_edit_job);
    $stmt_edit->bind_param("i", $edit_id);
    $stmt_edit->execute();
    $result_edit = $stmt_edit->get_result();
    if ($result_edit->num_rows > 0) {
        $edit_job = $result_edit->fetch_assoc();
    }
    $stmt_edit->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Morpheme Tech</title>
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

    <style>
        html { scroll-behavior: smooth; }
        * { transition: background-color .2s ease, border-color .2s ease, color .2s ease, box-shadow .2s ease, transform .2s ease; }
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        :root {
            --bg: #0b1220;
            --bg-soft: #0f172a;
            --card: #0f1b33;
            --text: #e5e7eb;
            --muted: #9aa3b2;
            --primary: #3b82f6;
            --primary-600: #2563eb;
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
            --surface: rgba(255,255,255,0.06);
            --border: rgba(255,255,255,0.12);
            --shadow: 0 10px 30px rgba(2,6,23,0.35);
            --radius: 12px;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            background: radial-gradient(1200px 600px at 10% -10%, rgba(59,130,246,0.12), transparent 40%),
                        radial-gradient(1000px 500px at 90% 10%, rgba(34,197,94,0.10), transparent 40%),
                        var(--bg);
            color: var(--text);
            padding: 0;
            min-height: 100svh;
        }
        .admin-container {
            width: auto;
            margin: 0;
            background: linear-gradient(180deg, rgba(255,255,255,0.06), rgba(255,255,255,0.03));
            backdrop-filter: blur(10px);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            border-left: none;
            border-right: none;
            border-radius: 0;
            padding: 24px clamp(16px, 3vw, 28px);
            box-shadow: none;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 8px 0 12px 0;
            border-bottom: 1px solid var(--border);
            margin-bottom: 18px;
        }
        .topbar h2 {
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 0.2px;
            margin: 0;
        }
        .logout-btn {
            text-decoration: none;
            color: #fff;
            background: linear-gradient(180deg, rgba(239,68,68,0.18), rgba(239,68,68,0.14));
            border: 1px solid rgba(239,68,68,0.35);
            padding: 10px 14px;
            border-radius: 10px;
            transition: all .2s ease;
        }
        .logout-btn:hover { background: rgba(239,68,68,0.22); transform: translateY(-1px); }
        .nav-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 12px 0 0 0;
        }
        .nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            text-decoration: none;
            color: var(--text);
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 999px;
            transition: all .2s ease;
        }
        .nav-btn:hover { border-color: rgba(59,130,246,0.45); background: rgba(59,130,246,0.10); }
        .nav-btn.active { color: #fff; background: var(--primary); border-color: var(--primary-600); }
        h3, h4 { margin-top: 14px; font-weight: 600; }
        .table-container {
            margin-top: 14px;
            background: rgba(255,255,255,0.03);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            border-left: none;
            border-right: none;
            border-radius: 0;
            overflow: hidden;
            box-shadow: none;
        }
        .table-scroll { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; min-width: 800px; }
        thead th {
            background: linear-gradient(180deg, rgba(255,255,255,0.06), rgba(255,255,255,0.02));
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            font-size: 12px;
        }
        th, td { padding: 12px 14px; border-bottom: 1px solid var(--border); text-align: left; }
        tbody tr:hover { background: rgba(255,255,255,0.04); }
        tbody tr:nth-child(even) { background: rgba(255,255,255,0.02); }
        .file-link, .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 10px;
            border-radius: 8px;
            text-decoration: none;
            color: #fff;
            font-size: 12px;
            border: 1px solid transparent;
            transition: all .2s ease;
        }
        .file-link { background: rgba(59,130,246,0.18); border-color: rgba(59,130,246,0.35); }
        .file-link:hover { background: rgba(59,130,246,0.24); }
        .edit-btn { background: rgba(245,158,11,0.18); color: #fff; border-color: rgba(245,158,11,0.35); }
        .edit-btn:hover { background: rgba(245,158,11,0.24); }
        .delete-btn { background: rgba(239,68,68,0.18); border-color: rgba(239,68,68,0.35); }
        .delete-btn:hover { background: rgba(239,68,68,0.24); }
        .add-job-btn { background: rgba(34,197,94,0.18); border-color: rgba(34,197,94,0.35); }
        .form-container { margin-top: 18px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: 12px; padding: 16px; }
        .form-container input, .form-container select {
            width: 100%;
            padding: 12px 12px;
            margin-bottom: 14px;
            background: var(--surface);
            color: var(--text);
            border: 1px solid var(--border);
            border-radius: 10px;
            outline: none;
            transition: border-color .2s ease, box-shadow .2s ease;
            box-sizing: border-box;
        }
        .form-container input:focus, .form-container select:focus { border-color: rgba(59,130,246,0.55); box-shadow: 0 0 0 3px rgba(59,130,246,0.18); }
        .form-container button {
            padding: 12px 16px;
            background: linear-gradient(180deg, var(--primary), var(--primary-600));
            color: #fff;
            border: 0;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            letter-spacing: .2px;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .form-container button:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(37,99,235,0.35); }
        .message {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            margin: 8px 0 2px 0;
            border-radius: 10px;
            color: #0b2b16;
            background: rgba(34,197,94,0.2);
            border: 1px solid rgba(34,197,94,0.35);
        }
        /* Quill editor */
        .ql-toolbar { border: 1px solid var(--border) !important; border-bottom: none !important; background: rgba(255,255,255,0.04); color: var(--text); }
        .ql-container { border: 1px solid var(--border) !important; background: var(--surface); color: var(--text); border-radius: 0 0 10px 10px; }
        .ql-editor { color: var(--text); }
        /* Responsive */
        @media (max-width: 768px) {
            .admin-container { padding: 16px; }
            .topbar { flex-direction: column; align-items: flex-start; gap: 10px; }
            .table-container { border-radius: 0; }
        }
    </style>
</head>
<body>

<div class="admin-container">
    <div class="topbar">
        <h2>Admin Dashboard</h2>
        <a href="?logout" class="logout-btn">Logout</a>
    </div>

    <div class="nav-buttons">
        <a href="?view=applications" class="nav-btn <?php echo ($view == 'applications') ? 'active' : ''; ?>">View Applications</a>
        <a href="?view=manage_jobs" class="nav-btn <?php echo ($view == 'manage_jobs') ? 'active' : ''; ?>">Manage Jobs</a>
        <a href="?view=manage_categories" class="nav-btn <?php echo ($view == 'manage_categories') ? 'active' : ''; ?>">Manage Categories</a>
    </div>

    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if ($view == 'applications'): ?>
        <h3>Job Applications</h3>
        <div class="table-container">
            <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Job Title</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Country</th>
                    <th>LinkedIn Profile</th>
                    <th>CV</th>
                    <th>Work Sample</th>
                    <th>Submitted At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($applications_result && $applications_result->num_rows > 0): ?>
                    <?php while($row = $applications_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['job_title']); ?></td>
                            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['country']); ?></td>
                            <td>
                                <?php if (!empty($row['linkedin_profile'])): ?>
                                    <a href="<?php echo htmlspecialchars($row['linkedin_profile']); ?>" target="_blank">View Profile</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($row['cv_path'])): ?>
                                    <a href="../<?php echo htmlspecialchars($row['cv_path']); ?>" class="file-link" download>Download CV</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($row['work_sample_path'])): ?>
                                    <a href="../<?php echo htmlspecialchars($row['work_sample_path']); ?>" class="file-link" download>Download Sample</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['submitted_at']); ?></td>
                            <td>
                                <a href="?view=applications&delete_id=<?php echo $row['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this application?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="11">No applications found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
            </div>
        </div>

    <?php elseif ($view == 'manage_jobs'): ?>
        <h3>Manage Job Listings</h3>

        <?php if ($edit_job): ?>
            <h4>Edit Job Listing: <?php echo htmlspecialchars($edit_job['title']); ?></h4>
            <div class="form-container">
                <form action="?view=manage_jobs" method="POST" id="jobForm">
                    <input type="hidden" name="action" value="update_job">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_job['id']); ?>">
                    <input type="text" name="title" placeholder="Job Title" value="<?php echo htmlspecialchars($edit_job['title']); ?>" required>
                    <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($edit_job['location']); ?>" required>
                    <select name="employment_type" required>
                        <option value="Full-Time" <?php echo ($edit_job['employment_type'] == 'Full-Time') ? 'selected' : ''; ?>>Full-Time</option>
                        <option value="Part-Time" <?php echo ($edit_job['employment_type'] == 'Part-Time') ? 'selected' : ''; ?>>Part-Time</option>
                        <option value="Internship" <?php echo ($edit_job['employment_type'] == 'Internship') ? 'selected' : ''; ?>>Internship</option>
                        <option value="Contract" <?php echo ($edit_job['employment_type'] == 'Contract') ? 'selected' : ''; ?>>Contract</option>
                    </select>
                    <select name="category" required>
                        <?php
                        $categories_result->data_seek(0); // Reset pointer
                        if ($categories_result->num_rows > 0): ?>
                            <?php while($cat_row = $categories_result->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($cat_row['name']); ?>" <?php echo ($edit_job['category'] == $cat_row['name']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat_row['name']); ?></option>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <option value="">No categories found</option>
                        <?php endif; ?>
                    </select>
                    <div id="description" style="height: 250px; margin-bottom: 30px;">
                        <?php echo $edit_job['description']; ?>
                    </div>
                    <input type="hidden" name="description">
                    <button type="submit">Update Job</button>
                    <a href="?view=manage_jobs" class="action-btn" style="background-color: gray; color: white;">Cancel</a>
                </form>
            </div>
        <?php else: ?>
            <h4>Add New Job</h4>
            <div class="form-container">
                <form action="?view=manage_jobs" method="POST" id="jobForm">
                    <input type="hidden" name="action" value="add_job">
                    <input type="text" name="title" placeholder="Job Title" required>
                    <input type="text" name="location" placeholder="Location" required>
                    <select name="employment_type" required>
                        <option value="Full-Time">Full-Time</option>
                        <option value="Part-Time">Part-Time</option>
                        <option value="Internship">Internship</option>
                        <option value="Contract">Contract</option>
                    </select>
                    <select name="category" required>
                        <?php
                        $categories_result->data_seek(0); // Reset pointer
                        if ($categories_result->num_rows > 0): ?>
                            <?php while($cat_row = $categories_result->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($cat_row['name']); ?>"><?php echo htmlspecialchars($cat_row['name']); ?></option>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <option value="">No categories found</option>
                        <?php endif; ?>
                    </select>
                    <div id="description" style="height: 250px; margin-bottom: 30px;">
                        Enter job description here...
                    </div>
                    <input type="hidden" name="description">
                    <button type="submit">Add Job</button>
                </form>
            </div>
        <?php endif; ?>

        <h4>Existing Jobs</h4>
        <div class="table-container">
            <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Job Title</th>
                    <th>Location</th>
                    <th>Employment Type</th>
                    <th>Category</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($jobs_result->num_rows > 0): ?>
                    <?php while($row = $jobs_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td><?php echo htmlspecialchars($row['employment_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <a href="?view=manage_jobs&edit_id=<?php echo $row['id']; ?>" class="action-btn edit-btn">Edit</a>
                                <a href="?view=manage_jobs&delete_id=<?php echo $row['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this job?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">No job listings found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
            </div>
        </div>

    <?php elseif ($view == 'manage_categories'): ?>
        <h3>Manage Job Categories</h3>

        <h4>Add New Category</h4>
        <div class="form-container">
            <form action="?view=manage_categories" method="POST">
                <input type="hidden" name="action" value="add_category">
                <input type="text" name="category_name" placeholder="Category Name (e.g., Marketing, IT)" required>
                <button type="submit">Add Category</button>
            </form>
        </div>

        <h4>Existing Categories</h4>
        <div class="table-container">
            <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($categories_result->num_rows > 0): ?>
                    <?php while($row = $categories_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td>
                                <a href="?view=manage_categories&delete_category_id=<?php echo $row['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3">No categories found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // QuillJS editor initialization logic
    var quill;
    var form = document.querySelector('#jobForm');

    function initializeQuill(content) {
        if (quill) {
            quill.off('text-change');
            quill = null;
        }
        var quillElement = document.getElementById('description');
        if (quillElement) {
            quill = new Quill('#description', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link', 'image', 'blockquote', 'code-block'],
                        ['clean']
                    ]
                }
            });
            if (content) {
                quill.root.innerHTML = content;
            }
        }
    }

    // Check if we are in edit mode to initialize Quill with existing content
    var editJobContent = `<?php echo addslashes(isset($edit_job['description']) ? $edit_job['description'] : ''); ?>`;
    if (editJobContent) {
        initializeQuill(editJobContent);
    } else {
        initializeQuill('');
    }

    // When the form is submitted, get the HTML content from Quill and set it to the hidden input
    if (form) {
        form.addEventListener('submit', function(event) {
            var descriptionInput = document.querySelector('input[name="description"]');
            if (descriptionInput) {
                 descriptionInput.value = quill.root.innerHTML;
            }
        });
    }
</script>

</body>
</html>
<?php
$conn->close();
?>