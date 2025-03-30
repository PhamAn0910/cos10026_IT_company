<?php
/**
 * Process EOI Form Submission
 * This script handles the Expression of Interest form submission,
 * validates input data, and stores it in the database
 */

// 1. Initialize required resources
require_once("settings.php");
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. Define validation constants
define('MIN_AGE', 15);
define('MAX_AGE', 80);
define('MAX_ADDRESS_LENGTH', 40);
define('MAX_NAME_LENGTH', 20);
define('MIN_PHONE_LENGTH', 8);
define('MAX_PHONE_LENGTH', 12);

// 3. Helper functions
/**
 * Create database connection with error handling
 * @return mysqli Database connection object
 * @throws Exception if connection fails
 */
function create_database_connection() {
    global $host, $user, $pwd, $sql_db;
    
    // Attempt to connect to database
    $conn = @mysqli_connect($host, $user, $pwd, $sql_db);
    
    // Check connection
    if (!$conn) {
        error_log("Database connection failed: " . mysqli_connect_error());
        throw new Exception("Unable to connect to the database. Please try again later.");
    }
    
    return $conn;
}

/**
 * Create EOI table if it doesn't exist
 * @param mysqli $conn Database connection
 * @throws Exception if table creation fails
 */
function create_eoi_table($conn) {
    $query = "CREATE TABLE IF NOT EXISTS eoi (
        eoi_id INT AUTO_INCREMENT PRIMARY KEY,
        job_reference VARCHAR(5) NOT NULL,
        first_name VARCHAR(20) NOT NULL,
        last_name VARCHAR(20) NOT NULL,
        date_of_birth DATE NOT NULL,
        gender VARCHAR(10) NOT NULL,
        street_address VARCHAR(40) NOT NULL,
        suburb VARCHAR(40) NOT NULL,
        state VARCHAR(3) NOT NULL,
        postcode VARCHAR(4) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(12) NOT NULL,
        skills TEXT NOT NULL,
        other_skills TEXT,
        status VARCHAR(20) DEFAULT 'New' NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($query)) {
        error_log("Table creation failed: " . $conn->error);
        throw new Exception("Database setup failed. Please contact support.");
    }
}

// Basic sanitization function
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Enhanced error handling
function handle_database_error($error, $context = '') {
    error_log("Database error in $context: " . $error);
    return "A database error occurred. Please try again later.";
}

// Enhanced database operations with prepared statements
function insert_eoi($conn, $data) {
    $query = "INSERT INTO eoi (job_reference, first_name, last_name, date_of_birth, 
              gender, street_address, suburb, state, postcode, email, phone, 
              skills, other_skills, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'New')";
              
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception(handle_database_error($conn->error, 'prepare statement'));
    }
    
    $stmt->bind_param("sssssssssssss", 
        $data['job_reference'], 
        $data['first_name'],
        $data['last_name'],
        $data['dob'],
        $data['gender'],
        $data['street_address'],
        $data['suburb'],
        $data['state'],
        $data['postcode'],
        $data['email'],
        $data['phone'],
        $data['skills'],
        $data['other_skills']
    );
    
    if (!$stmt->execute()) {
        throw new Exception(handle_database_error($stmt->error, 'execute statement'));
    }
    
    return $stmt->insert_id;
}

// 4. Validation functions - grouped by type
// 4.1 Personal Information validation
function validate_name($name, $fieldName) {
    if (empty($name)) {
        return "$fieldName cannot be empty.";
    }
    if (!preg_match("/^[A-Za-z]{1," . MAX_NAME_LENGTH . "}$/", $name)) {
        return "$fieldName must contain only letters (max 20 characters).";
    }
    return "";
}

function validate_date($date) {
    if (empty($date)) {
        return "Date of birth cannot be empty.";
    }
    
    // Check format dd/mm/yyyy
    if (!preg_match("/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/(19[4-9][0-9]|20[0-1][0-9])$/", $date)) {
        return "Date must be in dd/mm/yyyy format (e.g., 25/04/2000).";
    }
    
    $dateParts = explode('/', $date);
    // Validate actual date
    if (!checkdate($dateParts[1], $dateParts[0], $dateParts[2])) {
        return "Please enter a valid date.";
    }
    
    $dob = DateTime::createFromFormat('Y-m-d', $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0]);
    $today = new DateTime();
    $age = $today->diff($dob)->y;
    
    if ($age < MIN_AGE) {
        return "You must be at least " . MIN_AGE . " years old to apply.";
    }
    if ($age > MAX_AGE) {
        return "Age cannot exceed " . MAX_AGE . " years.";
    }
    
    return "";
}

// 4.2 Contact Information validation
function validate_address($address) {
    if (empty($address)) {
        return "Street address cannot be empty.";
    }
    if (!preg_match("/^[A-Za-z0-9\s\-\/,.]{1,40}$/", $address)) {
        return "Street address can only contain letters, numbers, spaces, and basic punctuation.";
    }
    if (strlen($address) > MAX_ADDRESS_LENGTH) {
        return "Street address must not exceed " . MAX_ADDRESS_LENGTH . " characters.";
    }
    return "";
}

function validate_suburb($suburb) {
    if (empty($suburb)) {
        return "Suburb/Town cannot be empty.";
    }
    if (!preg_match("/^[A-Za-z\s\-']{1,40}$/", $suburb)) {
        return "Suburb/Town can only contain letters, spaces, hyphens and apostrophes.";
    }
    if (strlen($suburb) > MAX_ADDRESS_LENGTH) {
        return "Suburb/Town must not exceed " . MAX_ADDRESS_LENGTH . " characters.";
    }
    return "";
}

function validate_state_postcode($state, $postcode) {
    $state_postcodes = [
        'VIC' => ['pattern' => '/^(3[0-9]{3}|8[0-9]{3})$/', 'ranges' => '3000-3999 or 8000-8999'],
        'NSW' => ['pattern' => '/^2[0-9]{3}$/', 'ranges' => '2000-2999'],
        'QLD' => ['pattern' => '/^4[0-9]{3}$/', 'ranges' => '4000-4999'],
        'NT'  => ['pattern' => '/^0[0-9]{3}$/', 'ranges' => '0800-0999'],
        'WA'  => ['pattern' => '/^6[0-9]{3}$/', 'ranges' => '6000-6999'],
        'SA'  => ['pattern' => '/^5[0-9]{3}$/', 'ranges' => '5000-5999'],
        'TAS' => ['pattern' => '/^7[0-9]{3}$/', 'ranges' => '7000-7999'],
        'ACT' => ['pattern' => '/^2[0-9]{3}$/', 'ranges' => '2600-2618']
    ];
    
    if (!array_key_exists($state, $state_postcodes)) {
        return "Please select a valid state.";
    }
    
    if (!preg_match("/^\d{4}$/", $postcode)) {
        return "Postcode must be exactly 4 digits.";
    }
    
    if (!preg_match($state_postcodes[$state]['pattern'], $postcode)) {
        return "Postcode must be in range " . $state_postcodes[$state]['ranges'] . " for $state.";
    }
    
    return "";
}

function validate_email($email) {
    if (empty($email)) {
        return "Email address cannot be empty.";
    }
    $pattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    if (!preg_match($pattern, $email)) {
        return "Please enter a valid email address format (e.g., name@example.com).";
    }
    return "";
}

function validate_phone($phone) {
    if (empty($phone)) {
        return "Phone number cannot be empty.";
    }
    $phoneClean = str_replace(' ', '', $phone);
    if (!preg_match("/^\d{" . MIN_PHONE_LENGTH . "," . MAX_PHONE_LENGTH . "}$/", $phoneClean)) {
        return "Phone number must contain between " . MIN_PHONE_LENGTH . " and " . MAX_PHONE_LENGTH . " digits (spaces allowed).";
    }
    return "";
}
function validate_job_reference($jobRef) {
    if (empty($jobRef)) {
        return "Job reference number cannot be empty.";
    }
    if (!preg_match("/^[A-Za-z0-9]{5}$/", $jobRef)) {
        return "Job reference must be exactly 5 alphanumeric characters (letters and numbers only).";
    }
    return "";
}

function validate_skills($skillsArray, $otherSkills, $otherSkillsChecked) {
    if (empty($skillsArray) && !$otherSkillsChecked) {
        return "Please select at least one skill.";
    }
    
    if ($otherSkillsChecked && empty($otherSkills)) {
        return "Please specify your other skills or uncheck the option.";
    }
    
    return "";
}

// 5. Display functions
function display_error($errors) {
    echo "<!DOCTYPE html>";
    echo "<html lang='en'>";
    echo "<head>";
    echo "  <meta charset='UTF-8'>";
    echo "  <meta name='description'  content='IT Company - Apply Form Expression of Interest'>";
    echo "  <meta name='keywords'     content='HTML5, CSS'>";
    echo "  <meta name='author'       content='Le Ngoc Quynh Trang, Pham Truong Que An'>";
    echo "  <meta name='viewport'     content='width=device-width, initial-scale=1.0'>";
    echo "  <title>SonixWave | Application Submission Error Page</title>";
    echo "  <link rel='stylesheet' href='styles/style.css'>";
    echo "  <link rel='stylesheet' href='styles/responsive-nav.css'>";
    echo "  <script src='scripts/nav-toggle.js'></script>";
    echo "  <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;700&display=swap'>";
    echo "  <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;700&display=swap'>";
    echo "  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css'>";
    echo "  <script src='https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js'></script>";
    echo "</head>";
    echo "<body>";
    require_once("header.inc");
    echo '<div class="error-container">';
    echo '<h2>Application Submission Error</h2>';
    echo '<div class="error-message">';
    echo '<p>We cannot process your application due to the following issues:</p>';
    echo '<ul class="error-list">';
    foreach ($errors as $error) {
        echo "<li>" . htmlspecialchars($error) . "</li>";
    }
    echo '</ul>';
    echo '<div class="error-actions">';
    echo '<p>Please:</p>';
    echo '<ul>';
    echo '<li>Review the errors listed above</li>';
    echo '<li>Click the Back button below to return to the form</li>';
    echo '<li>Correct the information and submit again</li>';
    echo '</ul>';
    echo '<p><a href="javascript:history.back()" class="button">← Back to Application Form</a></p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    require_once("footer.inc");
    echo '</body>';
    echo '</html>';
}

function display_success($eoiNumber, $jobRef, $firstName, $lastName) {
    echo "<!DOCTYPE html>";
    echo "<html lang='en'>";
    echo "<head>";
    echo "  <meta charset='UTF-8'>";
    echo "  <meta name='description' content='IT Company - Apply Form Expression of Interest'>";
    echo "  <meta name='keywords' content='HTML5, CSS'>";
    echo "  <meta name='author' content='Le Ngoc Quynh Trang, Pham Truong Que An'>";
    echo "  <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "  <title>SonixWave | Application Submitted</title>";
    echo "  <link rel='stylesheet' href='styles/style.css'>";
    echo "  <link rel='stylesheet' href='styles/responsive-nav.css'>";
    echo "  <script src='scripts/nav-toggle.js'></script>";
    echo "  <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;700&display=swap'>";
    echo "  <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;700&display=swap'>";
    echo "  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css'>";
    echo "  <script src='https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js'></script>";
    echo "</head>";
    echo "<body>";
    require_once("header.inc");
    echo '<div class="success-container">';
    echo '<h2>Application Submitted Successfully</h2>';
    echo '<div class="confirmation-details">';
    echo "<p>Thank you for your application, " . htmlspecialchars($firstName . " " . $lastName) . "!</p>";
    echo "<p>Your Expression of Interest has been successfully received and recorded in our system.</p>";
    echo "<div class='application-details'>";
    echo "<h3>Important Information</h3>";
    echo "<ul>";
    echo "<li>EOI Reference Number: <strong>" . htmlspecialchars($eoiNumber) . "</strong></li>";
    echo "<li>Job Reference: <strong>" . htmlspecialchars($jobRef) . "</strong></li>";
    echo "</ul>";
    echo "</div>";
    echo "<div class='next-steps'>";
    echo "<h3>Next Steps</h3>";
    echo "<ol>";
    echo "<li>Save your EOI Reference Number for all future correspondence</li>";
    echo "<li>Our HR team will review your application within 5 business days</li>";
    echo "<li>You will receive an email confirmation shortly</li>";
    echo "</ol>";
    echo "</div>";
    echo "<div class='action-links'>";
    echo '<p><a href="index.php" class="button">Return to Home Page</a></p>';
    echo '<p><a href="jobs.php" class="link">View More Job Opportunities</a></p>';
    echo "</div>";
    echo '</div>';
    echo '</div>';
    require_once("footer.inc");
    echo '</body>';
    echo '</html>';
}

// 6. Form submission check
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["reference-num"])) {
    header("Location: apply.php");
    exit();
}

// 7. Initialize variables
$errors = [];
$firstName = $lastName = $dob = $gender = $streetAddress = $suburb = "";
$state = $postcode = $email = $phone = $jobRef = $skills = $otherSkills = "";

// 8. Process form data
try {
    // Sanitize and validate all inputs
    $jobRef = sanitize_input($_POST["reference-num"]); 
    $firstName = sanitize_input($_POST["first-name"]); 
    $lastName = sanitize_input($_POST["last-name"]); 
    $dob = sanitize_input($_POST["DOB"]);
    $gender = sanitize_input($_POST["gender"]);
    $streetAddress = sanitize_input($_POST["street-address"]); 
    $suburb = sanitize_input($_POST["suburb"]);
    $state = sanitize_input($_POST["state"]);
    $postcode = sanitize_input($_POST["postcode"]);
    $email = sanitize_input($_POST["email"]);
    $phone = sanitize_input($_POST["phone-num"]); 

    // Process skills
    $skillsArray = isset($_POST["skills"]) ? $_POST["skills"] : [];
    foreach ($skillsArray as &$skill) {
        $skill = sanitize_input($skill);
    }
    $skills = implode(", ", $skillsArray);
    
    $otherSkillsChecked = isset($_POST["other_skills"]);
    $otherSkills = $otherSkillsChecked ? sanitize_input($_POST["other_skills_text"]) : "";

    // 9. Validate all inputs
    $validationErrors = [];

    // Validate all fields with detailed error messages
    $jobRefError = validate_job_reference($jobRef);
    if ($jobRefError !== "") $validationErrors[] = $jobRefError;

    $firstNameError = validate_name($firstName, "First name");
    if ($firstNameError !== "") $validationErrors[] = $firstNameError;

    $lastNameError = validate_name($lastName, "Last name");
    if ($lastNameError !== "") $validationErrors[] = $lastNameError;

    $dobError = validate_date($dob);
    if ($dobError !== "") $validationErrors[] = $dobError;

    $addressError = validate_address($streetAddress);
    if ($addressError !== "") $validationErrors[] = $addressError;

    $suburbError = validate_suburb($suburb);
    if ($suburbError !== "") $validationErrors[] = $suburbError;

    $statePostcodeError = validate_state_postcode($state, $postcode);
    if ($statePostcodeError !== "") $validationErrors[] = $statePostcodeError;

    $emailError = validate_email($email);
    if ($emailError !== "") $validationErrors[] = $emailError;

    $phoneError = validate_phone($phone);
    if ($phoneError !== "") $validationErrors[] = $phoneError;

    $skillsError = validate_skills($skillsArray, $otherSkills, $otherSkillsChecked);
    if ($skillsError !== "") $validationErrors[] = $skillsError;

    // 10. Database operations
    if (empty($validationErrors)) {
        try {
            $conn = create_database_connection();
            
            create_eoi_table($conn);
            
            // Convert date format for MySQL
            $dateParts = explode('/', $dob);
            $mysqlDate = "{$dateParts[2]}-{$dateParts[1]}-{$dateParts[0]}";
            
            // Prepare and execute insert statement with error handling
            $data = [
                'job_reference' => $jobRef,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'dob' => $mysqlDate,
                'gender' => $gender,
                'street_address' => $streetAddress,
                'suburb' => $suburb,
                'state' => $state,
                'postcode' => $postcode,
                'email' => $email,
                'phone' => $phone,
                'skills' => $skills,
                'other_skills' => $otherSkills
            ];
            
            $eoiNumber = insert_eoi($conn, $data);
            $conn->close();
            
            display_success($eoiNumber, $jobRef, $firstName, $lastName);
            
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            throw new Exception("Unable to save your application. Please try again later.");
        }
    } else {
        display_error($validationErrors);
    }

} catch (Exception $e) {
    error_log("Application processing error: " . $e->getMessage());
    display_error(["An unexpected error occurred. Please try again later."]);
}
?>
