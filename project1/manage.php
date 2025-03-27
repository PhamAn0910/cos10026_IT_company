<?php
require_once("settings.php");

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to display results in a table
function display_results($result) {
    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table class='results-table'>";
        echo "<tr><th>EOI Number</th><th>Job Ref</th><th>Name</th><th>DOB</th><th>Gender</th><th>Address</th><th>Email</th><th>Phone</th><th>Skills</th><th>Status</th><th>Date</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['EOInumber']) . "</td>";
            echo "<td>" . htmlspecialchars($row['job_reference']) . "</td>";
            echo "<td>" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['date_of_birth']) . "</td>";
            echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
            echo "<td>" . htmlspecialchars($row['street_address'] . ", " . $row['suburb'] . ", " . $row['state'] . " " . $row['postcode']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
            echo "<td>" . htmlspecialchars($row['skills'] . ($row['other_skills'] ? ", " . $row['other_skills'] : "")) . "</td>";
            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
            echo "<td>" . htmlspecialchars($row['application_date']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No results found.</p>";
    }
}

// The form processing code has been moved to the results-container section
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SonixWave | Manage EOIs</title>
    <link rel="stylesheet" href="styles/style.css">
    <style>
        .management-section {
            margin: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .results-table th, .results-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .results-table th {
            background-color: #f5f5f5;
        }
        .results-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #45a049;
        }
        /* New styles for messages and results */
        #results-container {
            margin-bottom: 30px;
        }
        #results-container:empty {
            display: none;
        }
        .success-message {
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
            color: #3c763d;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .error-message {
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
    </style>
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
        <h1>Manage Expressions of Interest</h1>

        <!-- Results Section -->
        <div id="results-container" class="management-section">
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $conn = @mysqli_connect($host, $user, $pwd, $sql_db);
                
                if (!$conn) {
                    echo "<div class='error-message'>Database connection failure</div>";
                } else {
                    // Handle different operations based on form submission
                    if (isset($_POST['list_all'])) {
                        $query = "SELECT * FROM eoi ORDER BY EOInumber DESC";
                        $result = mysqli_query($conn, $query);
                        display_results($result);
                    }
                    
                    elseif (isset($_POST['list_by_job'])) {
                        $jobRef = sanitize_input($_POST['job_ref']);
                        $query = "SELECT * FROM eoi WHERE job_reference = '$jobRef' ORDER BY EOInumber DESC";
                        $result = mysqli_query($conn, $query);
                        display_results($result);
                    }
                    
                    elseif (isset($_POST['list_by_name'])) {
                        $firstName = sanitize_input($_POST['first_name']);
                        $lastName = sanitize_input($_POST['last_name']);
                        $query = "SELECT * FROM eoi WHERE 1=1";
                        if (!empty($firstName)) {
                            $query .= " AND first_name LIKE '%$firstName%'";
                        }
                        if (!empty($lastName)) {
                            $query .= " AND last_name LIKE '%$lastName%'";
                        }
                        $query .= " ORDER BY EOInumber DESC";
                        $result = mysqli_query($conn, $query);
                        display_results($result);
                    }
                    
                    elseif (isset($_POST['delete_job'])) {
                        $jobRef = sanitize_input($_POST['delete_job_ref']);
                        $query = "DELETE FROM eoi WHERE job_reference = '$jobRef'";
                        if (mysqli_query($conn, $query)) {
                            echo "<div class='success-message'>Successfully deleted all EOIs for job reference: $jobRef</div>";
                        } else {
                            echo "<div class='error-message'>Error deleting EOIs: " . mysqli_error($conn) . "</div>";
                        }
                    }
                    
                    elseif (isset($_POST['update_status'])) {
                        $eoiNumber = sanitize_input($_POST['eoi_number']);
                        $newStatus = sanitize_input($_POST['new_status']);
                        $query = "UPDATE eoi SET status = '$newStatus' WHERE EOInumber = '$eoiNumber'";
                        if (mysqli_query($conn, $query)) {
                            echo "<div class='success-message'>Successfully updated status for EOI number: $eoiNumber</div>";
                        } else {
                            echo "<div class='error-message'>Error updating status: " . mysqli_error($conn) . "</div>";
                        }
                    }
                    
                    mysqli_close($conn);
                }
            }
            ?>
        </div>

        <!-- List All EOIs -->
        <div class="management-section">
            <h2>List All EOIs</h2>
            <form method="post">
                <input type="submit" name="list_all" value="List All EOIs" class="submit-btn">
            </form>
        </div>

        <!-- List EOIs by Job Reference -->
        <div class="management-section">
            <h2>List EOIs by Job Reference</h2>
            <form method="post">
                <div class="form-group">
                    <label for="job_ref">Job Reference Number:</label>
                    <input type="text" id="job_ref" name="job_ref" required>
                </div>
                <input type="submit" name="list_by_job" value="Search" class="submit-btn">
            </form>
        </div>

        <!-- List EOIs by Name -->
        <div class="management-section">
            <h2>List EOIs by Name</h2>
            <form method="post">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name">
                </div>
                <input type="submit" name="list_by_name" value="Search" class="submit-btn">
            </form>
        </div>

        <!-- Delete EOIs by Job Reference -->
        <div class="management-section">
            <h2>Delete EOIs by Job Reference</h2>
            <form method="post" onsubmit="return confirm('Are you sure you want to delete all EOIs for this job reference?');">
                <div class="form-group">
                    <label for="delete_job_ref">Job Reference Number:</label>
                    <input type="text" id="delete_job_ref" name="delete_job_ref" required>
                </div>
                <input type="submit" name="delete_job" value="Delete" class="submit-btn">
            </form>
        </div>

        <!-- Update EOI Status -->
        <div class="management-section">
            <h2>Update EOI Status</h2>
            <form method="post">
                <div class="form-group">
                    <label for="eoi_number">EOI Number:</label>
                    <input type="number" id="eoi_number" name="eoi_number" required>
                </div>
                <div class="form-group">
                    <label for="new_status">New Status:</label>
                    <select id="new_status" name="new_status" required>
                        <option value="New">New</option>
                        <option value="Current">Current</option>
                        <option value="Final">Final</option>
                    </select>
                </div>
                <input type="submit" name="update_status" value="Update Status" class="submit-btn">
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 SonixWave. All rights reserved.</p>
        <p class="footer-logo">SonixWave</p>
    </footer>
</body>
</html>
