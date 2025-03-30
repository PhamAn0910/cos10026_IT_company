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
</head>
<body>
    <?php include 'header.inc'; ?>
    
    <main class="enhancement-documentation">
        <h1>PHP Enhancement: Advanced EOI Record Sorting</h1>
        
        <section class="enhancement-description">
            <h2>Description</h2>
            <p>Implemented an advanced sorting system for EOI records that allows managers to:</p>
            <ul>
                <li>Sort by multiple fields (EOI Number, Job Reference, Name, Date, Status)</li>
                <li>Choose sort direction (Ascending/Descending)</li>
                <li>Handle special sorting for Status field (New → Current → Final)</li>
            </ul>
        </section>

        <section class="implementation-details">
            <h2>Implementation Details</h2>
            <h3>Code Snippet:</h3>
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
    
    // Special handling for status field
    if ($sort_field == 'status') {
        return " ORDER BY CASE status 
            WHEN 'New' THEN 1 
            WHEN 'Current' THEN 2 
            WHEN 'Final' THEN 3 
            ELSE 4 END " . $sort_order;
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

    <?php include 'footer.inc'; ?>
</body>
</html>