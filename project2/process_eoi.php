<?php
require_once("settings.php");

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Initialize variables to store form data
$firstName = $lastName = $dob = $gender = $streetAddress = $suburb = "";
$state = $postcode = $email = $phone = $jobRef = $skills = $otherSkills = "";
$errMsg = "";

// State postcode mapping for validation
$statePostcodeMap = [
    'VIC' => ['3', '8'],
    'NSW' => ['1', '2'],
    'QLD' => ['4', '9'],
    'NT' => ['0'],
    'WA' => ['6'],
    'SA' => ['5'],
    'TAS' => ['7'],
    'ACT' => ['0', '2']
];

// Valid states
$validStates = ['VIC', 'NSW', 'QLD', 'NT', 'WA', 'SA', 'TAS', 'ACT'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input data
    $firstName = sanitize_input($_POST["first-name"]);
    $lastName = sanitize_input($_POST["last-name"]);
    $dob = sanitize_input($_POST["DOB"]);
    $gender = isset($_POST["gender"]) ? sanitize_input($_POST["gender"]) : "";
    $streetAddress = sanitize_input($_POST["street-address"]);
    $suburb = sanitize_input($_POST["suburb"]);
    $state = sanitize_input($_POST["state"]);
    $postcode = sanitize_input($_POST["postcode"]);
    $email = sanitize_input($_POST["email"]);
    $phone = sanitize_input($_POST["phone-num"]);
    $jobRef = sanitize_input($_POST["reference-num"]);
    
    // Process skills
    $skillsArray = array();
    if (isset($_POST["skill-1"])) $skillsArray[] = sanitize_input($_POST["skill-1"]);
    if (isset($_POST["skill-2"])) $skillsArray[] = sanitize_input($_POST["skill-2"]);
    if (isset($_POST["skill-3"])) $skillsArray[] = sanitize_input($_POST["skill-3"]);
    $skills = implode(", ", $skillsArray);
    
    $otherSkillsChecked = isset($_POST["other-skills"]);
    $otherSkills = isset($_POST["other-skills-text"]) ? sanitize_input($_POST["other-skills-text"]) : "";

    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($dob) || empty($gender) ||
        empty($streetAddress) || empty($suburb) || empty($state) || empty($postcode) ||
        empty($email) || empty($phone) || empty($jobRef)) {
        $errMsg .= "All required fields must be filled out.<br>";
    }

    // Validate job reference number (exactly 5 alphanumeric characters)
    if (!preg_match("/^[a-zA-Z0-9]{5}$/", $jobRef)) {
        $errMsg .= "Job reference must be exactly 5 alphanumeric characters.<br>";
    }

    // Validate first name (max 20 alpha characters)
    if (!preg_match("/^[a-zA-Z]{1,20}$/", $firstName)) {
        $errMsg .= "First name must contain only alphabetic characters and be maximum 20 characters long.<br>";
    }

    // Validate last name (max 20 alpha characters)
    if (!preg_match("/^[a-zA-Z]{1,20}$/", $lastName)) {
        $errMsg .= "Last name must contain only alphabetic characters and be maximum 20 characters long.<br>";
    }

    // Validate date of birth (dd/mm/yyyy between 15 and 80 years old)
    if (!preg_match("/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{4}$/", $dob)) {
        $errMsg .= "Date of birth must be in format dd/mm/yyyy.<br>";
    } else {
        $dobParts = explode('/', $dob);
        $day = $dobParts[0];
        $month = $dobParts[1];
        $year = $dobParts[2];
        
        // Check if date is valid
        if (!checkdate($month, $day, $year)) {
            $errMsg .= "Date of birth is not a valid date.<br>";
        } else {
            // Check age between 15 and 80
            $birthDate = new DateTime($year . '-' . $month . '-' . $day);
            $today = new DateTime();
            $age = $today->diff($birthDate)->y;
            
            if ($age < 15 || $age > 80) {
                $errMsg .= "Age must be between 15 and 80 years.<br>";
            }
        }
    }

    // Validate gender (selected)
    if (!in_array($gender, ['male', 'female', 'other'])) {
        $errMsg .= "Please select a valid gender.<br>";
    }

    // Validate street address (max 40 characters)
    if (strlen($streetAddress) > 40) {
        $errMsg .= "Street address must be maximum 40 characters long.<br>";
    }

    // Validate suburb/town (max 40 characters)
    if (strlen($suburb) > 40) {
        $errMsg .= "Suburb/town must be maximum 40 characters long.<br>";
    }

    // Validate state (one of VIC, NSW, QLD, NT, WA, SA, TAS, ACT)
    if (!in_array($state, $validStates)) {
        $errMsg .= "State must be one of VIC, NSW, QLD, NT, WA, SA, TAS, ACT.<br>";
    }

    // Validate postcode (exactly 4 digits and matches state)
    if (!preg_match("/^\d{4}$/", $postcode)) {
        $errMsg .= "Postcode must be exactly 4 digits.<br>";
    } else {
        $validPostcode = false;
        foreach ($statePostcodeMap[$state] as $prefix) {
            if (substr($postcode, 0, 1) === $prefix) {
                $validPostcode = true;
                break;
            }
        }
        if (!$validPostcode) {
            $errMsg .= "Postcode does not match the selected state.<br>";
        }
    }

    // Validate email address
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errMsg .= "Please enter a valid email address.<br>";
    }

    // Validate phone number (8 to 12 digits or spaces)
    $phoneClean = str_replace(' ', '', $phone);
    if (!preg_match("/^\d{8,12}$/", $phoneClean)) {
        $errMsg .= "Phone number must contain 8 to 12 digits (spaces allowed).<br>";
    }

    // Validate other skills (not empty if checkbox selected)
    if (!$otherSkillsChecked || empty($otherSkills)) {
        $errMsg .= "Please specify your other skills or uncheck the 'Other Skills' option.<br>";
    }

    // If no errors, proceed with database insertion
    if ($errMsg == "") {
        $conn = @mysqli_connect($host, $user, $pwd, $sql_db);
        
        if (!$conn) {
            echo displayErrorPage("Database connection failure");
        } else {
            // Create EOI table if it doesn't exist
            $sql_table = "eoi";
            $createTableSQL = "CREATE TABLE IF NOT EXISTS $sql_table (
                EOInumber INT AUTO_INCREMENT PRIMARY KEY,
                job_reference VARCHAR(10),
                first_name VARCHAR(20),
                last_name VARCHAR(20),
                date_of_birth DATE,
                gender VARCHAR(10),
                street_address VARCHAR(40),
                suburb VARCHAR(40),
                state VARCHAR(3),
                postcode VARCHAR(4),
                email VARCHAR(50),
                phone VARCHAR(12),
                skills TEXT,
                other_skills TEXT,
                status VARCHAR(20) DEFAULT 'New',
                application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            mysqli_query($conn, $createTableSQL);
            
            // Convert date format from dd/mm/yyyy to yyyy-mm-dd for MySQL
            $dobParts = explode('/', $dob);
            $mysqlDob = $dobParts[2] . '-' . $dobParts[1] . '-' . $dobParts[0];
            
            // Prepare statement to prevent SQL injection
            $stmt = mysqli_prepare($conn, "INSERT INTO $sql_table (
                job_reference, first_name, last_name, date_of_birth, gender,
                street_address, suburb, state, postcode, email, phone,
                skills, other_skills
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            mysqli_stmt_bind_param($stmt, "sssssssssssss", 
                $jobRef, $firstName, $lastName, $mysqlDob, $gender,
                $streetAddress, $suburb, $state, $postcode, $email, $phone,
                $skills, $otherSkills
            );
            
            $result = mysqli_stmt_execute($stmt);
            
            if ($result) {
                $eoiNumber = mysqli_insert_id($conn);
                displayConfirmationPage($firstName, $lastName, $eoiNumber);
            } else {
                echo displayErrorPage("Something went wrong with the database insertion: " . mysqli_error($conn));
            }
            
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
        }
    } else {
        echo displayErrorPage($errMsg);
    }
}

// Function to display user-friendly error page
function displayErrorPage($errorMessage) {
    $output = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Application Error</title>
        <link rel="stylesheet" href="styles/style.css">
    </head>
    <body>
        <header>    
            <nav>
                <span class="logo"><a href="https://youtu.be/KAIC4oCGNNc" target="_blank">SonixWave</a></span>       
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="jobs.html">Career</a></li>
                    <li><a href="apply.html">Apply Now</a></li>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="enhancements.html">Enhancements</a></li>
                    <li><a href="mailto:105028463@student.swin.edu.au,105192148@student.swin.edu.au">Contact</a></li>
                </ul>
            </nav>
        </header>
        <main>
            <h1>Application Error</h1>
            <div class="error-message">
                <p>We encountered the following issues with your application:</p>
                <p>' . $errorMessage . '</p>
                <p><a href="javascript:history.back()">Go Back</a> to correct your information.</p>
            </div>
        </main>
        <footer>
            <p>&copy; 2025 SonixWave. All rights reserved.</p>
            <p class="footer-logo">SonixWave</p>
        </footer>
    </body>
    </html>';
    
    return $output;
}

// Function to display confirmation page
function displayConfirmationPage($firstName, $lastName, $eoiNumber) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Application Confirmation</title>
        <link rel="stylesheet" href="styles/style.css">
    </head>
    <body>
        <header>    
            <nav>
                <span class="logo"><a href="https://youtu.be/KAIC4oCGNNc" target="_blank">SonixWave</a></span>       
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="jobs.html">Career</a></li>
                    <li><a href="apply.html">Apply Now</a></li>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="enhancements.html">Enhancements</a></li>
                    <li><a href="mailto:105028463@student.swin.edu.au,105192148@student.swin.edu.au">Contact</a></li>
                </ul>
            </nav>
        </header>
        <main>
            <h1>Application Submitted Successfully!</h1>
            <div class="confirmation-message">
                <p>Thank you for your application, <?php echo htmlspecialchars($firstName . " " . $lastName); ?>!</p>
                <p>Your Expression of Interest (EOI) number is: <strong><?php echo $eoiNumber; ?></strong></p>
                <p>Please keep this number for future reference.</p>
                <p>We will review your application and contact you soon.</p>
                <p><a href="index.html">Return to Home Page</a></p>
            </div>
        </main>
        <footer>
            <p>&copy; 2025 SonixWave. All rights reserved.</p>
            <p class="footer-logo">SonixWave</p>
        </footer>
    </body>
    </html>
    <?php
}
?>