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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input data
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
    $jobRef = sanitize_input($_POST["reference-num"]);
    
    // Process skills
    $skillsArray = array();
    if (isset($_POST["skill-1"])) $skillsArray[] = $_POST["skill-1"];
    if (isset($_POST["skill-2"])) $skillsArray[] = $_POST["skill-2"];
    if (isset($_POST["skill-3"])) $skillsArray[] = $_POST["skill-3"];
    $skills = implode(", ", $skillsArray);
    
    $otherSkills = isset($_POST["other-skills-text"]) ? sanitize_input($_POST["other-skills-text"]) : "";

    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($dob) || empty($gender) ||
        empty($streetAddress) || empty($suburb) || empty($state) || empty($postcode) ||
        empty($email) || empty($phone) || empty($jobRef)) {
        $errMsg .= "All required fields must be filled out.<br>";
    }

    // If no errors, proceed with database insertion
    if ($errMsg == "") {
        $conn = @mysqli_connect($host, $user, $pwd, $sql_db);
        
        if (!$conn) {
            echo "<h2>Error</h2>";
            echo "<p>Database connection failure</p>";
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
            
            // Insert data into the database
            $insertSQL = "INSERT INTO $sql_table (
                job_reference, first_name, last_name, date_of_birth, gender,
                street_address, suburb, state, postcode, email, phone,
                skills, other_skills
            ) VALUES (
                '$jobRef', '$firstName', '$lastName', '$mysqlDob', '$gender',
                '$streetAddress', '$suburb', '$state', '$postcode', '$email', '$phone',
                '$skills', '$otherSkills'
            )";
            
            $result = mysqli_query($conn, $insertSQL);
            
            if ($result) {
                $eoiNumber = mysqli_insert_id($conn);
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
            } else {
                echo "<h2>Error</h2>";
                echo "<p>Something went wrong with the database insertion: " . mysqli_error($conn) . "</p>";
            }
            mysqli_close($conn);
        }
    } else {
        echo "<h2>Error</h2>";
        echo "<p>$errMsg</p>";
    }
}
?>
