<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="description"  content="Our Webstie Enhancements">
  <meta name="keywords"     content="HTML5, CSS">
  <meta name="author"       content="Le Ngoc Quynh Trang, Pham Truong Que An">
  <meta name="viewport"     content="width=device-width, initial-scale=1.0">
  
  <title>SonixWave | Enhancements</title>

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
  <!-- Navigation Menu -->
  <?php include 'header.inc'; ?>

  <hr>
    <h1 id="enhancement-h1" style="margin-top:4rem;"> 10 HTML &amp; CSS Enhancements</h1>
    
    <div class="enhancements">
    <section>
        <h2>1. CSS Variables for Colors and Fonts</h2>
        <p>This feature introduces CSS variables, defined in the <code>:root</code> selector, which allows you to store and reuse colors and fonts throughout the website. 
            By using variables like <code>--dark-green</code>, <code>--pink</code>, and <code>--font-sans-serif</code>, you avoid repeating color codes and font-family declarations multiple times. 
            This approach ensures consistency across the site and makes it easier to make changes in one place if needed.</p>
        <a href="index.html">
            <img src="images/enhancementCode/code1.png" alt="CSS Variables for Colors and Fonts">
        </a>
    </section>
    
    <section>
        <h2>2. Navigation Link Styling</h2>
        <p>The hover effect transitions smoothly over 0.3 seconds, changing the color to pink when hovered. 
           The transition effect ensures that the color change happens in a visually appealing way, rather than being instantaneous.</p>
        <a href="index.html#enhancement2">
            <img src="images/enhancementCode/code2.png" alt="Navigation Link Styling">
        </a>
    </section>
    
    <section>
        <h2>3. Checkbox and Radio Button Styling</h2>
        <p>This enhancement applies styles directly to all radio buttons and checkboxes using the <code>input[type="radio"]</code> and <code>input[type="checkbox"]</code> selectors, rather than using classes or IDs.
            This saves time by allowing you to style all checkboxes and radio buttons in a uniform way without needing additional markup.
        </p>
        <a href="apply.html#enhancement3-1">
          <img src="images/enhancementCode/code3-1.png" alt="Radio Button Styling">
        </a>
        <a href="apply.html#enhancement3-2">
          <img src="images/enhancementCode/code3-2.png" alt="Checkbox Styling">        
        </a>
    </section>
    
    <section>
        <h2>4. Button and Icon Transformations</h2>
        <p>This enhancement focuses on adding effects to buttons and icons. 
            The buttons change color and increase in size when hovered over, using the <code>transform: scale(1.05)</code> property. 
            This gives the effect of the button "popping out," providing visual feedback. 
            Additionally, the <code>.heart</code> icon rotates slightly, giving it a dynamic and playful appearance.</p>
        <a href="index.html#enhancement4-1">
            <img src="images/enhancementCode/code4-1.png" alt="Button Transformations">
        </a>
        <a href="about.html#enhancement4-2">
            <img src="images/enhancementCode/code4-2.png" alt="Icon Transformations">
        </a>
    </section>
    
    <section>
        <h2>5. Floating Animation</h2>
        <p>This feature animates an icon using CSS keyframes. 
            The icon "floats" left and right with a smooth motion, giving the illusion of movement. 
            This effect is especially useful for drawing attention to specific elements on the page.</p>
            <a href="index.html#enhancement5">
                <img src="images/enhancementCode/code5.png" alt="Floating Animation">
            </a>
    </section>
    
    <section>
        <h2>6. Styled Underline</h2>
        <p>This enhancement modifies the default text underline with various CSS properties. 
            The color, thickness, offset, and style of the underline are customized, making the underline more prominent and visually appealing. 
            The result is an eye-catching underline that complements the website's design, rather than a plain line.</p>
        <a href="about.html#enhancement6">
            <img src="images/enhancementCode/code6.png" alt="Styled Underline">
        </a>
    </section>
    
    <section>
        <h2>7. Table Cell Styling</h2>
        <p>This enhancement applies styling to table cells <code>&lt;td&gt;&lt;/td&gt;</code> that contain content, avoiding the empty ones.</p>
        <a href="about.html#enhancement7">
            <img src="images/enhancementCode/code7.png" alt="Table Cell Styling">
        </a>
    </section>
    
    <section>
        <h2>8. FontAwesome Icons</h2>
        <p>
            <a href="https://fontawesome.com/" target="_blank" rel="noopener noreferrer" class="link">FontAwesome</a> icons are used to bring more visual interest to the site without the need for custom images. 
            These scalable vector icons are simple to implement and improve the design of the page. 
            In this example, a headphone icon is added using the <code>&lt;i&gt;&lt;/i&gt;</code> tag with appropriate classes for styling.
        </p>        
        <a href="index.html#enhancement8">
            <img src="images/enhancementCode/code8.png" alt="FontAwesome Icons">
        </a>
    </section>

    <section>
        <h2>9. Job Reference Number Validation</h2>
        <p>
            When entering the 5-alphanumeric-character job reference number in the application form, users must type the same reference number listed in the job description list for it to be valid. 
            This ensures accuracy and prevents incorrect applications.
        </p>        
        <a href="apply.html#enhancement9">
            <img src="images/enhancementCode/code9.png" alt="Job Reference Number Validation">
        </a>
    </section>

    <section>
        <h2>10. HTML Data List Element</h2>
        <p>
            The <code>&lt;datalist&gt;</code> tag provides an "autocomplete" feature for <code>&lt;input&gt;</code> elements. 
            Users will see a drop-down list of pre-defined options as they input data.
            This helps the users can easily fill in long, hard to memorize input such as the Job Reference Number.
        </p>        
        <a href="apply.html#enhancement9">
            <img src="images/enhancementCode/code10.png" alt="HTML Data List Element">
        </a>
    </section>
  </div>

  <!-- Link to PHP enhancements page -->
  <div class="enhancement-button-container">
    <a href="phpenhancements.php" class="enhancement-button">View PHP Enhancements &rarr;</a> <!-- &rarr; is a right arrow symbol -->
  </div>

  <!-- Footer -->
  <?php include 'footer.inc'; ?>

<script>
    AOS.init();
</script>

</body>
</html>

