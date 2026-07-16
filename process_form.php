<?php

// PHP error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- IMPORTANT ---
// Adjust the paths below based on where your PHPMailer folder is located relative to this file.
// Since process_form.php is in public_html and PHPMailer is also in public_html,
// the paths should be direct.
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
// --- IMPORTANT ---

header('Content-Type: application/json');

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get and sanitize form data
    $firstName = sanitize_input($_POST['firstName']);
    $lastName = sanitize_input($_POST['lastName']);
    $email = sanitize_input($_POST['email']);
    $phone = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
    $subject = sanitize_input($_POST['subject']);
    $message = sanitize_input($_POST['message']);

    // Validate the input fields
    if (empty($firstName) || empty($lastName) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'First name, last name, email, subject, and message are required.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }

    // PHPMailer configuration
    $mail = new PHPMailer(true);

    try {
        // Server settings - Use your Hostinger SMTP details
        $mail->SMTPDebug = 0; // Set to 2 for debugging, 0 for production
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com'; // Hostinger SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@morphemetech.co.uk'; // Your webmail email
        $mail->Password   = 'info@MorphemeTech110'; // Your webmail password (Use the same as submit_application.php)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom('info@morphemetech.co.uk', 'Morpheme Tech Contact Form'); // Sender email and name
        $mail->addAddress('info@morphemetech.co.uk', 'Morpheme Tech Admin'); // Recipient email and name
        $mail->addReplyTo($email, $firstName . ' ' . $lastName); // Reply-to address

        // Email content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = "New Contact Form Submission: " . htmlspecialchars($subject);

        $email_body = '
        <div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
            <div style="text-align: center; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                <img src="https://morphemetech.co.uk/assets/images/headerlogo1.png" alt="Morpheme Tech Logo" style="max-width: 150px; filter: brightness(0) saturate(100%);">
            </div>
            
            <h2 style="color: #0056b3; text-align: center;">New Contact Form Submission</h2>
            <p style="text-align: center; color: #666;">A new message has been submitted through the website contact form.</p>
            
            <div style="background-color: #f9f9f9; padding: 15px; border-radius: 8px; margin-top: 20px;">
                <h3 style="margin-top: 0; color: #007bff;">Inquiry Details</h3>
                <p><strong>Name:</strong> ' . htmlspecialchars($firstName . ' ' . $lastName) . '</p>
                <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
                <p><strong>Phone:</strong> ' . htmlspecialchars($phone) . '</p>
                <p><strong>Subject:</strong> ' . htmlspecialchars($subject) . '</p>
            </div>
            
            <div style="margin-top: 20px;">
                <h3 style="color: #007bff;">Message:</h3>
                <p style="white-space: pre-wrap;">' . nl2br(htmlspecialchars($message)) . '</p>
            </div>
            
            <p style="text-align: center; margin-top: 30px; font-size: 12px; color: #999;">
                This email was sent automatically from the Morpheme Tech website.
            </p>
        </div>';

        $mail->Body = $email_body;

        // Send the email
        if ($mail->send()) {
            echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);
        } else {
            // Log the error for debugging purposes
            error_log("PHPMailer Error in process_form.php: {$mail->ErrorInfo}");
            echo json_encode(['success' => false, 'message' => 'Failed to send message. Please try again later.']);
        }

    } catch (Exception $e) {
        error_log("PHPMailer Exception in process_form.php: {$mail->ErrorInfo}");
        echo json_encode(['success' => false, 'message' => 'An error occurred while sending your message. Please try again later.']);
    }

} else {
    // If it's not a POST request, return an error
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

?>