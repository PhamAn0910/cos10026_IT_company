<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="PHP Enhancements Documentation">
    <meta name="keywords" content="PHP, Database, Enhancement">
    <meta name="author" content="Le Ngoc Quynh Trang, Pham Truong Que An">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>SonixWave | PHP Enhancement</title>
    
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/responsive-nav.css">
    <script src="scripts/nav-toggle.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
</head>

<body>
    <?php include 'header.inc'; ?>
    <hr>
    <main class="enhancement-documentation">
        <h1>PHP Enhancement: EOI Record Sorting</h1>
        
        <section class="enhancement-description">
            <h2>Description</h2>
            <p>Implemented an advanced sorting system for EOI records that allows managers to:</p>
            <ul>
                <li>Sort by multiple fields (EOI Number, Job Reference, Name, Date, Status)</li>
                <li>Choose sort direction (Ascending/Descending)</li>
                <li>Handle special sorting for Status field (New &rarr; Current &rarr; Final)</li>
            </ul>
        </section>

        <section class="implementation-details">
            <h2>Implementation Details</h2>
            <h3>Code Snippet</h3>
            <pre><code>
function get_sort_clause() {
  $valid_fields = [
    'EOInumber', 'job_reference', 'first_name', 
    'last_name', 'date_of_birth', 'status'
  ];
    
$sort_field = isset($_POST['sort_field']) ? 
  sanitize_input($_POST['sort_field']) : 'EOInumber';
$sort_order = isset($_POST['sort_order']) ? 
  sanitize_input($_POST['sort_order']) : 'DESC';
  
  <span>// Special handling for status field to sort 
    in logical order (New, Current, Final)</span>
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
            </code></pre>
        </section>

        <section class="enhancement-features">
            <h2>Key Features</h2>
            <ul>
                <li>Input validation for sort fields and orders</li>
                <li>Custom sort logic for status progression</li>
                <li>Flexible sorting options in the user interface</li>
                <li>SQL injection prevention through input sanitization</li>
            </ul>
        </section>

        <section class="technical-challenges">
            <h2>Technical Challenges Overcome</h2>
            <ul>
                <li>Implementing logical ordering for status values</li>
                <li>Ensuring secure handling of user input</li>
                <li>Maintaining consistent sorting across all query types</li>
            </ul>
        </section>
    </main>

    <!-- Link to PHP enhancements page -->
    <div class="enhancement-button-container">
        <a href="enhancements.php" class="enhancement-button">&larr; View HTML &amp; CSS Enhancements</a> <!-- &larr; is a left arrow symbol -->
    </div>

    <?php include 'footer.inc'; ?>
</body>
</html>