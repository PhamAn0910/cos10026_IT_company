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

// Get the sorting field and order
function get_sort_clause() {
    $valid_fields = [
        'EOInumber', 'job_reference', 'first_name', 'last_name', 
        'date_of_birth', 'gender', 'email', 'status', 'application_date'
    ];
    
    $sort_field = isset($_POST['sort_field']) ? sanitize_input($_POST['sort_field']) : 'EOInumber';
    $sort_order = isset($_POST['sort_order']) ? sanitize_input($_POST['sort_order']) : 'DESC';
    
    // Validate the sort field
    if (!in_array($sort_field, $valid_fields)) {
        $sort_field = 'EOInumber';
    }
    
    // Validate the sort order
    if ($sort_order != 'ASC' && $sort_order != 'DESC') {
        $sort_order = 'DESC';
    }
    
    // Special handling for status field to sort in logical order (New, Current, Final)
    if ($sort_field == 'status') {
        if ($sort_order == 'ASC') {
            return " ORDER BY CASE status 
                      WHEN 'New' THEN 1 
                      WHEN 'Current' THEN 2 
                      WHEN 'Final' THEN 3 
                      ELSE 4 END";
        } else { // DESC
            return " ORDER BY CASE status 
                      WHEN 'Final' THEN 1 
                      WHEN 'Current' THEN 2 
                      WHEN 'New' THEN 3 
                      ELSE 4 END";
        }
    }
    
    return " ORDER BY $sort_field $sort_order";
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
    <link rel="stylesheet" href="styles/responsive-nav.css">
    <script src="scripts/nav-toggle.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
</head>
<body>
    <main>
        <?php include 'header.inc'; ?>
        <hr>

        <h1 style="margin-top:4rem;">Manage Expressions of Interest</h1>

        <!-- Results Section -->
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            echo '<div id="results-container" class="management-section">';
            $conn = @mysqli_connect($host, $user, $pwd, $sql_db);
            
            if (!$conn) {
                echo "<div class='error-message'>Database connection failure</div>";
            } else {
                // Get sort clause for queries
                $sort_clause = get_sort_clause();

                // Handle different operations based on form submission
                if (isset($_POST['list_all'])) {
                    $query = "SELECT * FROM eoi" . $sort_clause;
                    $result = mysqli_query($conn, $query);
                    display_results($result);
                }
                
                elseif (isset($_POST['list_by_job'])) {
                    $jobRef = sanitize_input($_POST['job_ref']);
                    $query = "SELECT * FROM eoi WHERE job_reference = '$jobRef'" . $sort_clause;
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
                    $query .= $sort_clause;
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
                    
                    // First, check if the EOI number exists
                    $checkQuery = "SELECT EOInumber FROM eoi WHERE EOInumber = '$eoiNumber'";
                    $checkResult = mysqli_query($conn, $checkQuery);
                    
                    if (mysqli_num_rows($checkResult) > 0) {
                        // EOI exists, proceed with update
                        $query = "UPDATE eoi SET status = '$newStatus' WHERE EOInumber = '$eoiNumber'";
                        if (mysqli_query($conn, $query)) {
                            echo "<div class='success-message'>Successfully updated status for EOI number: $eoiNumber</div>";
                        } else {
                            echo "<div class='error-message'>Error updating status: " . mysqli_error($conn) . "</div>";
                        }
                    } else {
                        // EOI does not exist
                        echo "<div class='error-message'>Error: EOI number $eoiNumber does not exist in the database</div>";
                    }
                }
                
                mysqli_close($conn);
            }
            echo '</div>'; // Close the results container
        }
        ?>

        <!-- List All EOIs - Full Width -->
        <div class="management-section">
            <h2 class="h2-manage">List All EOIs</h2>
            <form method="post" class="form-manage">
                <div class="sort-options">
                    <label for="sort_field">Sort by:</label>
                    <select id="sort_field" name="sort_field" class="select-manage">
                        <option value="EOInumber" <?php echo (!isset($_POST['sort_field']) || $_POST['sort_field'] == 'EOInumber') ? 'selected' : ''; ?>>EOI Number</option>
                        <option value="job_reference" <?php echo (isset($_POST['sort_field']) && $_POST['sort_field'] == 'job_reference') ? 'selected' : ''; ?>>Job Reference</option>
                        <option value="first_name" <?php echo (isset($_POST['sort_field']) && $_POST['sort_field'] == 'first_name') ? 'selected' : ''; ?>>First Name</option>
                        <option value="last_name" <?php echo (isset($_POST['sort_field']) && $_POST['sort_field'] == 'last_name') ? 'selected' : ''; ?>>Last Name</option>
                        <option value="date_of_birth" <?php echo (isset($_POST['sort_field']) && $_POST['sort_field'] == 'date_of_birth') ? 'selected' : ''; ?>>Date of Birth</option>
                        <option value="status" <?php echo (isset($_POST['sort_field']) && $_POST['sort_field'] == 'status') ? 'selected' : ''; ?>>Status</option>
                        <option value="application_date" <?php echo (isset($_POST['sort_field']) && $_POST['sort_field'] == 'application_date') ? 'selected' : ''; ?>>Application Date</option>
                    </select>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="sort_order" value="ASC" <?php echo (isset($_POST['sort_order']) && $_POST['sort_order'] == 'ASC') ? 'checked' : ''; ?>> 
                            Ascending
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="sort_order" value="DESC" <?php echo (!isset($_POST['sort_order']) || $_POST['sort_order'] == 'DESC') ? 'checked' : ''; ?>> 
                            Descending
                        </label>
                    </div>
                </div>
                <input type="submit" name="list_all" value="List All EOIs">
            </form>
        </div>

        <!-- Two-column grid for the other forms -->
        <div class="forms-grid">
            <!-- List EOIs by Job Reference -->
            <div class="management-section">
                <h2 class="h2-manage">List EOIs by Job Reference</h2>
                <form method="post" class="form-manage">
                    <div class="form-group">
                        <label for="job_ref">Job Reference Number:</label>
                        <input type="text" id="job_ref" name="job_ref" required>
                    </div>
                    <div class="sort-options">
                        <label for="sort_field_job">Sort by:</label>
                        <select id="sort_field_job" name="sort_field" class="select-manage">
                            <option value="EOInumber" <?php echo (!isset($_POST['sort_field']) || $_POST['sort_field'] == 'EOInumber') ? 'selected' : ''; ?>>EOI Number</option>
                            <option value="first_name" <?php echo (isset($_POST['sort_field']) && $_POST['sort_field'] == 'first_name') ? 'selected' : ''; ?>>First Name</option>
                            <option value="last_name" <?php echo (isset($_POST['sort_field']) && $_POST['sort_field'] == 'last_name') ? 'selected' : ''; ?>>Last Name</option>
                            <option value="date_of_birth" <?php echo (isset($_POST['sort_field']) && $_POST['sort_field'] == 'date_of_birth') ? 'selected' : ''; ?>>Date of Birth</option>
                            <option value="status" <?php echo (isset($_POST['sort_field']) && $_POST['sort_field'] == 'status') ? 'selected' : ''; ?>>Status</option>
                            <option value="application_date" <?php echo (isset($_POST['sort_field']) && $_POST['sort_field'] == 'application_date') ? 'selected' : ''; ?>>Application Date</option>
                        </select>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="sort_order" value="ASC"> 
                                Ascending
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="sort_order" value="DESC" checked> 
                                Descending
                            </label>
                        </div>
                    </div>
                    <input type="submit" name="list_by_job" value="Search">
                </form>
            </div>

            <!-- List EOIs by Name -->
            <div class="management-section">
                <h2 class="h2-manage">List EOIs by Name</h2>
                <form method="post" class="form-manage">
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name">
                    </div>
                    <div class="sort-options">
                        <label for="sort_field_name">Sort by:</label>
                        <select id="sort_field_name" name="sort_field" class="select-manage">
                            <option value="EOInumber" <?php echo (!isset($_POST['sort_field']) || $_POST['sort_field'] == 'EOInumber') ? 'selected' : ''; ?>>EOI Number</option>
                            <option value="job_reference" <?php echo (isset($_POST['sort_field']) && $_POST['sort_field'] == 'job_reference') ? 'selected' : ''; ?>>Job Reference</option>
                            <option value="date_of_birth" <?php echo (isset($_POST['sort_field']) && $_POST['sort_field'] == 'date_of_birth') ? 'selected' : ''; ?>>Date of Birth</option>
                            <option value="status" <?php echo (isset($_POST['sort_field']) && $_POST['sort_field'] == 'status') ? 'selected' : ''; ?>>Status</option>
                            <option value="application_date" <?php echo (isset($_POST['sort_field']) && $_POST['sort_field'] == 'application_date') ? 'selected' : ''; ?>>Application Date</option>
                        </select>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="sort_order" value="ASC"> 
                                Ascending
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="sort_order" value="DESC" checked> 
                                Descending
                            </label>
                        </div>
                    </div>
                    <input type="submit" name="list_by_name" value="Search">
                </form>
            </div>

            <!-- Delete EOIs by Job Reference -->
            <div class="management-section">
                <h2 class="h2-manage">Delete EOIs by Job Reference</h2>
                <form method="post" class="form-manage" onsubmit="return confirm('Are you sure you want to delete all EOIs for this job reference?');">
                    <div class="form-group">
                        <label for="delete_job_ref">Job Reference Number:</label>
                        <input type="text" id="delete_job_ref" name="delete_job_ref" required>
                    </div>
                    <input type="submit" name="delete_job" value="Delete">
                </form>
            </div>

            <!-- Update EOI Status -->
            <div class="management-section">
                <h2 class="h2-manage">Update EOI Status</h2>
                <form method="post" class="form-manage">
                    <div class="form-group">
                        <label for="eoi_number">EOI Number:</label>
                        <input type="number" id="eoi_number" name="eoi_number" required>
                    </div>
                    <div class="form-group">
                        <label for="new_status">New Status:</label>
                        <select id="new_status" name="new_status" required class="select-manage">
                            <option value="New">New</option>
                            <option value="Current">Current</option>
                            <option value="Final">Final</option>
                        </select>
                    </div>
                    <input type="submit" name="update_status" value="Update Status">
                </form>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 SonixWave. All rights reserved.</p>
        <p class="footer-logo">SonixWave</p>
    </footer>
</body>
</html> 