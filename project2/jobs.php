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
  require_once 'settings.php'; // for database connection

  /**
   * Create database connection with error handling
   * @return mysqli Database connection object
   * @throws Exception if connection fails
   */
  function create_database_connection() {
    global $host, $user, $pwd, $sql_db;
    
    // Attempt to connect to database
    $dbconn = @mysqli_connect($host, $user, $pwd, $sql_db);
    
    // Check connection
    if (!$dbconn) {
        error_log("Database connection failed: " . mysqli_connect_error());
        throw new Exception("Unable to connect to the database. Please try again later.");
    }    
    return $dbconn;
  }

  /**
   * Create jobs table if it doesn't exist
   * @param mysqli $conn Database connection
   * @throws Exception if table creation fails
   */
  function create_jobs_table($dbconn) {
    $query = "CREATE TABLE IF NOT EXISTS jobs (
      job_id INT AUTO_INCREMENT PRIMARY KEY,
      job_ref_num VARCHAR(5) NOT NULL,
      position_title VARCHAR(20) NOT NULL,
      salary_range VARCHAR(20) NOT NULL,
      reports_to VARCHAR(20) NOT NULL,
      job_description TEXT NOT NULL,
      key_responsibilities TEXT NOT NULL,
      qualifications_essential TEXT NOT NULL,
      qualification_preferable TEXT NOT NULL
    )";
  
    if (!$dbconn->query($query)) {
      error_log("Table creation failed: " . $dbconn->error);
      throw new Exception("Database setup failed. Please contact support.");
    }
  }

  /**
   * Insert into jobs table jobs listings if it doesn't exist
   * @param mysqli $conn Database connection
   * @throws Exception if table creation fails
   */
  function insert_jobs($dbconn, $data) {
    $query = "INSERT INTO jobs 
    (job_ref_num, 
    position_title, 
    salary_range, 
    reports_to, 
    job_description, 
    key_responsibilities, 
    qualification_essential, 
    qualification_preferable) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $dbconn->prepare($query);
    if (!$stmt) {
        throw new Exception(handle_database_error($dbconn->error, 'prepare statement'));
    }
    
    $stmt->bind_param("ssssssss", 
        $data['job_ref_num'], $data['position_title'], $data['salary_range'], $data['reports_to'], 
        $data['job_description'], $data['key_responsibilities'], 
        $data['qualification_essential'], $data['qualification_preferable']
    );
    
    if (!$stmt->execute()) {
        throw new Exception(handle_database_error($stmt->error, 'execute statement'));
    }
    
    $jobs_id = $stmt->insert_id;
    $stmt->close();
    return $jobs_id;          
  }

  /**
   * Function to display all jobs in SonixWave company
   * This function select all the jobs entries in the database
   * then display in the webpage by echo with CSS styling
   */
  function display_jobs($dbconn) {
    $query = "SELECT * FROM jobs";
    $result = mysqli_query($dbconn,$query);

    if (!$result) {
      die("Query failed: " . mysqli_error($dbconn));
    }

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
    } else {
      echo "No job listings available.";
    }

    // Free result set
    mysqli_free_result($result);
  }

  /**
   * Main Function
   * This function runs all the subroutines 
   * including: create_database_connection(), 
   * create jobs table and insert into jobs listing into it if it doesn't exist,
   * and finally display_job()
   */
  function main() {
    try {
      $dbconn = create_database_connection();
        
      // First check if table exists and has data
      $check_query = "SELECT * FROM jobs LIMIT 1";
      $result = mysqli_query($dbconn, $check_query);
      
      if (!$result || mysqli_num_rows($result) == 0) {
        create_jobs_table($dbconn);
        // Insert sample job data
        $data = 
          array(
            'job_ref_num' => 'SE123',
            'position_title' => 'Data Analyst',
            'salary_range' => '$80,000 - $110,000 per annum',
            'reports_to'=> 'Head of Data Science',
            'job_description' => 'SonixWave is seeking a skilled Data Analyst to collect, analyze, and interpret user 
                                  listening patterns to enhance personalized music recommendations. This role plays a 
                                  crucial part in increasing user engagement by optimizing song and genre suggestions.',
            'key_responsibilities' => '<li>Analyze large datasets of user listening behaviors to identify trends and patterns.</li>
                                      <li>Develop and refine algorithms for personalized music recommendations.</li>
                                      <li>Collaborate with software engineers and product managers to integrate data-driven insights into the platform.</li>
                                      <li>Conduct A/B testing to measure the effectiveness of recommendation models.</li>
                                      <li>Prepare and present data-driven reports to stakeholders.</li>
                                      <li>Ensure data integrity and compliance with privacy regulations.</li>',
            'qualifications_essential' => '<li>Bachelor&#39;s degree in Data Science, Statistics, Computer Science, or a related field.</li>
                                          <li>Proficiency in SQL and Python/R for data analysis.</li>
                                          <li>Strong experience with data visualization tools (e.g., Tableau, Power BI, Matplotlib).</li>
                                          <li>Minimum 3 years of experience in data analytics, preferably in a music or media tech company.</li>
                                          <li>Understanding of machine learning techniques related to recommendation systems.</li>
                                          <li>Strong analytical and problem-solving skills.</li>',
            'qualifications_preferable' => '<li>Master&#39;s degree in a related field.</li>
                                            <li>Experience with big data technologies such as Hadoop, Spark, or Google BigQuery.</li>
                                            <li>Familiarity with AI-driven personalization techniques.</li>
                                            <li>Passion for music and knowledge of different genres.</li>'
        );
          array (
            'job_ref_num'=> 'CY987',
            'position_title'=> 'Software Engineer &lpar;Backend&rpar;', 
            'reports_to' =>'Engineering Manager', 
            'job_description' => 'SonixWave is looking for a talented Backend Software Engineer 
                                  to develop and maintain the core infrastructure that powers our
                                  music recommendation and streaming platform. This role focuses 
                                  on ensuring seamless data processing and API integrations to
                                  provide a smooth user experience.',

            'key_responsibilities' => '<li>Design, develop, and maintain scalable backend systems to support high-volume music streaming.</li>
                                      <li>Build and optimize APIs for real-time music recommendations and user interactions.</li>
                                      <li>Work closely with data scientists to deploy machine learning models in production.</li>
                                      <li>Ensure database efficiency, security, and scalability.</li>
                                      <li>Collaborate with frontend developers to enhance platform responsiveness.</li>
                                      <li>Troubleshoot and debug backend issues to maintain high system reliability.</li>',

            'qualifications_essential' => '<li>Bachelor&#39;s degree in Computer Science, Software Engineering, or a related field.</li>
                                            <li>Proficiency in backend programming languages such as Python, Java, or Node.js.</li>
                                            <li>Experience with cloud platforms (AWS, GCP, or Azure).</li>
                                            <li>Minimum 3 years of experience in backend development, preferably in a media streaming company.</li>
                                            <li>Strong knowledge of RESTful API design and database management (SQL & NoSQL).</li>
                                            <li>Familiarity with microservices architecture and distributed systems.</li>',

            'qualifications_preferable' => '<li>Master&#39;s degree in a related field.</li>
                                            <li>Experience with containerization tools like Docker and Kubernetes.</li>
                                            <li>Understanding of music streaming protocols and audio processing technologies.</li>
                                            <li>Passion for music technology and innovation.</li>'
          );
        insert_jobs($dbconn, $data);
      }
        
      display_jobs($dbconn);
        
    } catch (Exception $e) {
      error_log("Error: " . $e->getMessage());
      echo "<p>An error occurred. Please try again later.</p>";
    }
  }

  // Display Career Page
  try {
    include 'header.inc'; // Navigation Menu

    echo "<hr id='career-separator-line'>";	

    echo "<div class='jobs-context'>";	
      echo "<main>";
    
      echo "<h1>Career Opportunities</h1>";
    
      main();

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

    include 'footer.inc'; // Footer
  } catch (Exception $e) {
    error_log("Error in jobs.php: " . $e->getMessage());
    echo "<p>An error occurred loading the page. Please try again later.</p>";
  }
?>

<script>
  AOS.init();
</script>

</body>
</html>