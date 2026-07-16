<?php
// PHP error reporting on karein taaki debugging aasan ho
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// PHP Mailer classes ko include karein
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Public_html mein PHPMailer directory ka path set karein
// Ensure these paths are correct relative to your public_html
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

// Check karein ki form POST method se submit hua hai
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Database connection file include karein
    include '../db_connection.php';
    
    // Yahan apni email ID daalein jahan applications receive karni hain
    // Hostinger Webmail
    $receiving_email = "info@morphemetech.co.uk";
    
    // Form data ko surakshit (sanitize) karein
    $job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : '';
    $country = isset($_POST['country']) ? $conn->real_escape_string($_POST['country']) : '';
    $linkedin_profile = isset($_POST['linkedin_profile']) ? filter_input(INPUT_POST, 'linkedin_profile', FILTER_SANITIZE_URL) : '';

    // Job title database se fetch karein
    $stmt_job = $conn->prepare("SELECT title FROM jobs WHERE id = ?");
    $stmt_job->bind_param("i", $job_id);
    $stmt_job->execute();
    $result_job = $stmt_job->get_result();
    $job_title = ($result_job->num_rows > 0) ? $result_job->fetch_assoc()['title'] : 'Unknown Job';
    $stmt_job->close();

    // Files ko handle karein aur 'documents/applications' folder mein upload karein
    $upload_dir = '../documents/applications/';
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0775, true)) {
            error_log("Failed to create upload directory: " . $upload_dir);
            echo "Server error: Could not create upload directory. Please try again later.";
            exit();
        }
    }

    $cv_path = '';
    $work_sample_path = '';
    
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK && $_FILES['cv']['size'] > 0) {
        $file_extension = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);
        $cv_path_full = $upload_dir . uniqid('cv_') . '.' . $file_extension;
        $cv_path = str_replace('../', '', $cv_path_full); // Relative path for DB
        
        if (!move_uploaded_file($_FILES['cv']['tmp_name'], $cv_path_full)) {
            error_log("Failed to move uploaded CV file: " . $_FILES['cv']['name']);
            echo "Error uploading CV file. Please try again.";
            exit();
        }
    } else {
        echo "CV is required. Please upload your CV.";
        exit();
    }
    
    if (isset($_FILES['work_sample']) && $_FILES['work_sample']['error'] === UPLOAD_ERR_OK && $_FILES['work_sample']['size'] > 0) {
        $file_extension = pathinfo($_FILES['work_sample']['name'], PATHINFO_EXTENSION);
        $work_sample_path_full = $upload_dir . uniqid('work_sample_') . '.' . $file_extension;
        $work_sample_path = str_replace('../', '', $work_sample_path_full); // Relative path for DB
        
        if (!move_uploaded_file($_FILES['work_sample']['tmp_name'], $work_sample_path_full)) {
            error_log("Failed to move uploaded work sample file: " . $_FILES['work_sample']['name']);
        }
    }

    // Database mein data insert karein
    $sql_insert = "INSERT INTO applications (job_id, first_name, last_name, email, phone, country, linkedin_profile, cv_path, work_sample_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);

    if ($stmt_insert) {
        $bind_types = "issssssss";
        $stmt_insert->bind_param($bind_types, $job_id, $first_name, $last_name, $email, $phone, $country, $linkedin_profile, $cv_path, $work_sample_path);
        
        if ($stmt_insert->execute()) {
            $application_id = $conn->insert_id;
            $admin_dashboard_url = "https://morphemetech.co.uk/admin/index.php?view=applications";

            // PHPMailer ka instance banayein
            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->SMTPDebug = 0; // Set to 2 for debugging
                $mail->isSMTP();
                $mail->Host       = 'smtp.hostinger.com'; // Hostinger SMTP server
                $mail->SMTPAuth   = true;
                $mail->Username   = 'info@morphemetech.co.uk'; // Apka webmail email
                $mail->Password   = 'info@MorphemeTech110'; // Apka webmail password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                // Recipients
                $mail->setFrom('info@morphemetech.co.uk', 'Morpheme Tech Careers');
                $mail->addAddress($receiving_email, 'Morpheme Tech Admin');
                $mail->addReplyTo($email, $first_name . ' ' . $last_name);

                // Attachments
                if (!empty($cv_path)) {
                    $mail->addAttachment($upload_dir . basename($cv_path));
                }
                if (!empty($work_sample_path)) {
                    $mail->addAttachment($upload_dir . basename($work_sample_path));
                }

                // Email content with HTML formatting
                $mail->isHTML(true);
                $mail->Subject = 'New Job Application for: ' . htmlspecialchars($job_title);
                
                $email_body = '
                <div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                    <div style="text-align: center; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                        <img src="https://morphemetech.co.uk/assets/images/headerlogo1.png" alt="Morpheme Tech Logo" style="max-width: 150px; filter: brightness(0) saturate(100%);">
                    </div>
                    
                    <h2 style="color: #0056b3; text-align: center;">New Job Application Received</h2>
                    <p style="text-align: center; color: #666;">A new application has been submitted through the careers portal.</p>
                    
                    <div style="background-color: #f9f9f9; padding: 15px; border-radius: 8px; margin-top: 20px;">
                        <h3 style="margin-top: 0; color: #007bff;">Applicant Information</h3>
                        <p><strong>Job Applied For:</strong> ' . htmlspecialchars($job_title) . '</p>
                        <p><strong>Name:</strong> ' . htmlspecialchars($first_name . ' ' . $last_name) . '</p>
                        <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
                        <p><strong>Phone:</strong> ' . htmlspecialchars($phone) . '</p>
                        <p><strong>Country:</strong> ' . htmlspecialchars($country) . '</p>
                        <p><strong>LinkedIn Profile:</strong> <a href="' . htmlspecialchars($linkedin_profile) . '" style="color: #007bff; text-decoration: none;">' . htmlspecialchars($linkedin_profile) . '</a></p>
                    </div>
                    
                    <div style="text-align: center; margin-top: 30px;">
                        <p>To view all applications and documents, please visit your admin dashboard:</p>
                        <a href="' . htmlspecialchars($admin_dashboard_url) . '" style="background-color: #007bff; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                            View Applications
                        </a>
                    </div>
                    
                    <p style="text-align: center; margin-top: 20px; font-size: 12px; color: #999;">
                        This email was sent automatically from the Morpheme Tech careers portal.
                    </p>
                </div>';

                $mail->Body = $email_body;

                $mail->send();
                header("Location: thank-you.php");
                exit();

            } catch (Exception $e) {
                error_log("PHPMailer Error for application ID: " . $application_id . ". Mailer Error: {$mail->ErrorInfo}");
                // Redirect even on email failure, since the application is saved in DB
                header("Location: thank-you.php?email_error=true");
                exit();
            }
        } else {
            error_log("Error executing insert query for application: " . $stmt_insert->error);
            echo "Error submitting application. Please try again. If the problem persists, contact support.";
        }
        $stmt_insert->close();
    } else {
        error_log("Error preparing insert statement in submit_application.php: " . $conn->error);
        echo "Server error. Please try again later.";
    }

    $conn->close();
} else {
    if (isset($_POST['job_id'])) {
        header("Location: ./job-details.php?id=" . $job_id);
    } else {
        header("Location: ./");
    }
    exit();
}
?>