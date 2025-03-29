<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="description"  content="IT Company - Career">
  <meta name="keywords"     content="HTML5, CSS">
  <meta name="author"       content="Le Ngoc Quynh Trang, Pham Truong Que An">
  <meta name="viewport"     content="width=device-width, initial-scale=1.0">
  
  <title>SonixWave | Career</title>
  
  <link rel="stylesheet" href="styles/style.css">
  <link rel="stylesheet" href="styles/responsive-nav.css">
  <script src="scripts/nav-toggle.js"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;700&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
</head>

<body>
<?php 
  include 'header.inc'; // Navigation Menu
  require_once 'settings.php'; // Database connection

  $dbconn = mysqli_connect($host, $user, $pwd, $sql_db);
  if (!$dbconn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  // $query = "SELECT position_title, job_ref_num, salary_range, reports_to, 
  // job_description, key_responsibilities, qualifications_essential, qualifications_preferable FROM jobs";

  $query = "SELECT * FROM jobs";
  $result = mysqli_query($dbconn,$query);

  if (!$result) {
    die("Query failed: " . mysqli_error($dbconn));
  }

  echo "<hr id='career-separator-line'>";	

  echo "<div class='jobs-context'>";	
  echo "<main>";
  
  echo "<h1>Career Opportunities</h1>";
  // Check if there are any rows
  if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      echo "<div class='jd' data-aos='fade-up' data-aos-duration='1500' data-aos-once='true'>";
      echo "<section class='left-grid'>";
        echo "<h2> $row[position_title] </h2>";
        echo "<p> <strong>Job Reference Number:</strong> $row[job_ref_num] </p>";
        echo "<p> <strong>Position Title:</strong> $row[position_title] </p>";
        echo "<p> <strong>Salary Range:</strong> $row[salary_range] </p>";
        echo "<p> <strong>Reports to:</strong> $row[reports_to] </p>";
        echo "</section>";

        echo "<section class='right-grid'>";
        echo "<h3>Job Description</h3>";
        echo "<p>{$row['job_description']}</p>";

        echo "<h3>Key Responsibilities</h3>";
        echo "<ol>$row[key_responsibilities]</ol>";

        echo "<h3>Qualifications</h3>";

        echo "<h4>Essential</h4>";
        echo "<ul>$row[qualifications_essential]</ul>";

        echo "<h4>Preferable</h4>";
        echo "<ul>$row[qualifications_preferable]</ul>";
        echo "</section>";
      echo "</div>";
    }      
  } 
  
  else {
    echo "No job listings available.";
  }

  echo "</main>";

    echo "<aside>"; // Aside - Sidebar
    echo "<h2>Why <br> <span>Join Us?</span></h2>";
    echo "<section>";
    echo "<p><i>SonixWave offers a dynamic and innovative work environment, competitive 
      salaries, and opportunities for growth in the tech industry.</i></p>";

    echo "<h3>Employee Benefits</h3>";
    echo "<ul>";	
    echo "<li>Flexible working hours</li>";
    echo "<li>Remote work options</li>";
    echo "<li>Health and wellness programs</li>";
    echo "<li>Professional development and training</li>";
    echo "</ul>";
    echo "</section>";
    echo "</aside>";

  echo "</div>"; // Close jobs-context div

  mysqli_close($dbconn);

  include 'footer.inc'; // Footer
?>

<script>
  AOS.init();
</script>

</body>
</html>