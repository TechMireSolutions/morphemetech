<?php
// Database connection file include karein
include '../db_connection.php';

// URL se job ID ko surakshit tarike se lein
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
$job_title = "";
$employment_type = "";
$location = "";

if ($job_id > 0) {
    // Database se job title, employment type aur location fetch karein
    $sql = "SELECT title, employment_type, location FROM jobs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $job_data = $result->fetch_assoc();
            $job_title = $job_data['title'];
            $employment_type = $job_data['employment_type'];
            $location = $job_data['location'];
        } else {
            // Job not found, handle this case
            $job_title = "Job Not Found";
        }
        $stmt->close();
    } else {
        // Handle preparation error
        error_log("Error preparing job title query in apply.php: " . $conn->error);
        $job_title = "Error Loading Job";
    }
} else {
    // Agar job_id nahi hai, toh careers page par redirect karein
    header("Location: ./");
    exit();
}

// Country list for dropdown
$countries = [
    "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria",
    "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan",
    "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodia",
    "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo (Brazzaville)", "Congo (Kinshasa)",
    "Costa Rica", "Côte d'Ivoire", "Croatia", "Cuba", "Cyprus", "Czechia", "Denmark", "Djibouti", "Dominica", "Dominican Republic",
    "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini", "Ethiopia", "Fiji", "Finland",
    "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea",
    "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq",
    "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kuwait",
    "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg",
    "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico",
    "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru",
    "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "North Macedonia", "Norway", "Oman",
    "Pakistan", "Palau", "Palestine State", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal",
    "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe",
    "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia",
    "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname", "Sweden", "Switzerland", "Syria",
    "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey",
    "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "Uruguay", "Uzbekistan", "Vanuatu",
    "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for <?php echo htmlspecialchars($job_title); ?> - Morpheme Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/careers.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
    <style>
        /* Additional styles for the apply page */
        .job-preview {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #eee;
        }
        .job-preview h2 { margin-bottom: 5px; }
        .job-preview p { margin: 5px 0; }
        .job-preview span { font-weight: bold; }
        
        /* Form row and column styles */
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 15px;
        }
        .form-row .form-group {
            flex: 1;
            min-width: 250px;
        }
        
        /* FIX: Correcting Intl-Tel-Input styles for full width */
        .intl-tel-input {
            width: 100% !important;
            display: block !important; /* This is the key fix */
        }
        .intl-tel-input .form-control {
            width: 100% !important;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px 12px 10px 50px;
            height: 42px;
            font-size: 14px;
        }
        .intl-tel-input.iti--allow-dropdown .iti__flag-container {
            width: auto !important;
        }
        .form-group input[type="url"], .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group select {
            height: 42px;
        }

        /* Styles for file upload box */
        .document-upload .upload-box {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s ease;
            background-color: #fdfdfd;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .document-upload {
            display: flex;
            gap: 20px;
        }
        .upload-box:hover, .upload-box.drag-over {
            border-color: #007bff;
        }
        .upload-box input[type="file"] {
            display: none;
        }
        .upload-box span {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        .upload-box p {
            color: #777;
            font-size: 0.9em;
        }
        .upload-box.uploaded p {
            color: #28a745;
            font-weight: bold;
        }

        /* File upload error styling */
        #file-upload-error {
            color: #dc3545;
            font-size: 0.9em;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<header></header>

<main class="application-form-content">
    <a href="./job-details.php?id=<?php echo $job_id; ?>" class="back-link">&larr; Back to job details</a>

    <h1>Apply for <?php echo htmlspecialchars($job_title); ?></h1>
    <p>We are looking forward to hearing from you! Please fill out the following short form.</p>

    <?php if ($job_id > 0 && $job_title !== "Job Not Found" && $job_title !== "Error Loading Job"): ?>
    <div class="job-preview">
        <h2>Job Preview:</h2>
        <p><strong>Title:</strong> <?php echo htmlspecialchars($job_title); ?></p>
        <p><strong>Type:</strong> <?php echo htmlspecialchars($employment_type); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($location); ?></p>
    </div>
    <?php endif; ?>

    <form action="submit_application.php" method="POST" enctype="multipart/form-data" id="applicationForm">
        <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">

        <div class="form-row">
            <div class="form-group">
                <label for="first_name">First Name *</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="country">Country of Residence *</label>
                <select id="country" name="country" required>
                    <option value="">Select a country</option>
                    <?php foreach ($countries as $country): ?>
                        <option value="<?php echo htmlspecialchars($country); ?>"><?php echo htmlspecialchars($country); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="linkedin_profile">LinkedIn Profile</label>
                <input type="url" id="linkedin_profile" name="linkedin_profile" placeholder="e.g. https://linkedin.com/in/username">
            </div>
        </div>

        <div class="form-group">
            <label>Documents</label>
            <p>Please upload your CV (PDF, DOC, DOCX) and optionally a work sample (PDF, ZIP, JPG, PNG). Max. 20 MB each.</p>
            <div class="document-upload">
                <label for="cv_upload" class="upload-box">
                    <span>CV *</span>
                    <input type="file" id="cv_upload" name="cv" accept=".pdf,.doc,.docx" required>
                    <p>Click or drag-and-drop file</p>
                </label>
                <label for="work_sample_upload" class="upload-box">
                    <span>Work Sample</span>
                    <input type="file" id="work_sample_upload" name="work_sample" accept=".pdf,.zip,.jpg,.jpeg,.png,.gif">
                    <p>Click or drag-and-drop file (Optional)</p>
                </label>
            </div>
            <div id="file-upload-error" style="color: red; margin-top: 10px;"></div>
        </div>

        <div class="form-actions">
            <button type="submit" id="submitBtn">Send application</button>
            <button type="button" onclick="window.history.back()">Cancel</button>
        </div>
    </form>
</main>

<footer></footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('applicationForm');
        const submitBtn = document.getElementById('submitBtn');
        const fileErrorDiv = document.getElementById('file-upload-error');
        const maxFileSize = 20 * 1024 * 1024; // 20 MB in bytes

        // Initialize intl-tel-input
        const phoneInput = document.getElementById("phone");
        const iti = window.intlTelInput(phoneInput, {
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            initialCountry: "auto",
            geoIpLookup: function(success, failure) {
                fetch("https://ipapi.co/json/")
                    .then(response => response.json())
                    .then(data => success(data.country_code))
                    .catch(() => success("us"));
            }
        });

        const cvInput = document.getElementById('cv_upload');
        const workSampleInput = document.getElementById('work_sample_upload');

        const uploadBoxes = document.querySelectorAll('.upload-box');

        uploadBoxes.forEach(box => {
            const fileInput = box.querySelector('input[type="file"]');
            const fileStatus = box.querySelector('p');
            const originalText = fileStatus.textContent;

            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    if (file.size > maxFileSize) {
                        fileErrorDiv.textContent = `File "${file.name}" exceeds the 20MB limit.`;
                        fileInput.value = '';
                        box.classList.remove('uploaded');
                        fileStatus.textContent = originalText;
                    } else {
                        fileErrorDiv.textContent = '';
                        fileStatus.textContent = file.name;
                        box.classList.add('uploaded');
                    }
                } else {
                    fileStatus.textContent = originalText;
                    box.classList.remove('uploaded');
                }
            });

            box.addEventListener('dragover', (e) => {
                e.preventDefault();
                box.classList.add('drag-over');
            });

            box.addEventListener('dragleave', () => {
                box.classList.remove('drag-over');
            });

            box.addEventListener('drop', (e) => {
                e.preventDefault();
                box.classList.remove('drag-over');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const file = files[0];
                    if (file.size > maxFileSize) {
                        fileErrorDiv.textContent = `File "${file.name}" exceeds the 20MB limit.`;
                        box.classList.remove('uploaded');
                        fileStatus.textContent = originalText;
                    } else {
                        fileErrorDiv.textContent = '';
                        fileInput.files = files;
                        fileStatus.textContent = file.name;
                        box.classList.add('uploaded');
                    }
                }
            });
        });

        form.addEventListener('submit', function(event) {
            fileErrorDiv.textContent = '';

            if (cvInput.files.length === 0) {
                 fileErrorDiv.textContent = "CV is required.";
                 event.preventDefault();
                 return;
            }
            if (cvInput.files.length > 0 && cvInput.files[0].size > maxFileSize) {
                fileErrorDiv.textContent = `CV file "${cvInput.files[0].name}" exceeds the 20MB limit.`;
                event.preventDefault();
                return;
            }
            if (workSampleInput.files.length > 0 && workSampleInput.files[0].size > maxFileSize) {
                fileErrorDiv.textContent = `Work Sample file "${workSampleInput.files[0].name}" exceeds the 20MB limit.`;
                event.preventDefault();
                return;
            }
            if (!iti.isValidNumber()) {
                fileErrorDiv.textContent = "Invalid phone number.";
                event.preventDefault();
                return;
            }
            
            // Disable submit button to prevent double submissions
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
        });
    });
</script>
</body>
</html>
<?php
// Connection close karein
$conn->close();
?>