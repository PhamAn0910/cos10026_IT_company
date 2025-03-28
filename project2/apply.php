<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="description"  content="IT Company - Apply Form">
  <meta name="keywords"     content="HTML5, CSS">
  <meta name="author"       content="Le Ngoc Quynh Trang, Pham Truong Que An">
  <meta name="viewport"     content="width=device-width, initial-scale=1.0">
  
  <title>SonixWave | Apply Now</title>
  
  <link rel="stylesheet" href="styles/style.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;700&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
</head>

<body>
  <!-- Navigation Menu -->
  <?php include 'header.inc'; ?>

  <hr>
  
  <!-- Apply Form -->
  <main>
    <h1 id="form-title">Job Application</h1>
    <form action="process_eoi.php" method="post">
      <fieldset data-aos="fade-up" data-aos-duration="1000" data-aos-once="true">
        <legend>Personal Details</legend>
        <label for="first-name">First Name:</label>
        <input type="text" id="first-name" name="first-name" 
        maxlength="20" pattern="[A-za-z]{0,20}" required
        title="Please enter your first name with a maximum of 20 alphabetic characters."><br>

        <label for="last-name">Last Name:</label>
        <input type="text" id="last-name" name="last-name" 
        maxlength="20" pattern="[A-za-z]{0,20}" required
        title="Please enter your last name with a maximum of 20 alphabetic characters."><br>

        <label for="DOB">Date of Birth:</label>
        <input type="text" id="DOB" name="DOB" required placeholder="dd/mm/yyyy"
        pattern="^(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[0-2])/(19[7-9][5-9]|200[0-4])$" 
        title="Please enter the date in valid format: dd/mm/yyyy (eg.12/02/2002).
Our company hires candidates aged 20-50 years (ie.01/01/1975 - 31/12/2004)."><br>
      </fieldset>

      <fieldset data-aos="fade-up" data-aos-duration="1000" data-aos-once="true">
        <legend id="enhancement3-1">Gender</legend>
        <input type="radio" id="male" name="gender" value="male" required>
        <label for="male">Male</label>
        <input type="radio" id="female" name="gender" value="female">
        <label for="female">Female</label>
        <input type="radio" id="other" name="gender" value="other">
        <label for="other">Other</label>
      </fieldset>

      <fieldset data-aos="fade-up" data-aos-duration="1000" data-aos-once="true">
        <legend>Contact Details</legend>
        <label for="street-address">Street Address:</label>
        <input type="text" id="street-address" name="street-address" 
        maxlength="40" required><br>

        <label for="suburb">Suburb/Town:</label>
        <input type="text" id="suburb" name="suburb" 
        maxlength="40" required><br>

        <label for="state">State:</label>
        <select id="state" name="state" required>
          <option value="">Select State</option>
          <option value="VIC">VIC</option>
          <option value="NSW">NSW</option>
          <option value="QLD">QLD</option>
          <option value="NT">NT</option>
          <option value="WA">WA</option>
          <option value="SA">SA</option>
          <option value="TAS">TAS</option>
          <option value="ACT">ACT</option>
        </select><br>

        <label for="postcode">Postcode:</label>
        <input type="text" id="postcode" name="postcode" 
        pattern="\d{4}" required
        title="The postcode can only have digits with a maximum of 4."><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="phone-num">Phone Number:</label>
        <input type="text" id="phone-num" name="phone-num" 
        pattern="[0-9 ]{8,12}" required><br>
      </fieldset>

      <fieldset data-aos="fade-up" data-aos-duration="1000" data-aos-once="true" >
        <legend id="enhancement9">Job Application Details</legend>
        <label for="reference-num">Job Reference Number:</label>
        <input type="text" id="reference-num" name="reference-num" 
        list="jobRef" required pattern="SE123|CY987" 
        title="Select the reference number from the below pre-defined list so 
this number matches the position you are applying for. 
Manually entered values must match exactly.">           
        <datalist id="jobRef">
          <option value="SE123">
          <option value="CY987">
        </datalist>
      </fieldset>

      <fieldset data-aos="fade-up" data-aos-duration="1000" data-aos-once="true">
        <legend>Job Application Skills</legend>
        <p id="enhancement3-2">Skill list:</p>
        
        <input type="checkbox" id="python" name="skill-1" value="Python" checked>
        <label for="python">Python</label><br>

        <input type="checkbox" id="html-css" name="skill-2" value="HTML & CSS">
        <label for="html-css">HTML &amp; CSS</label><br>

        <input type="checkbox" id="javascript" name="skill-3" value="JavaScript">
        <label for="javascript">JavaScript</label><br>

        <input type="checkbox" id="other-skills" name="skill-list" value="Other">
        <label for="other-skills">Other skills...</label><br>

        <p>
          <label for="other-skills-text">Other skills (if any):</label>
        </p>
        <textarea id="other-skills-text" name="other-skills-text" 
        placeholder="Write any other relevant skills that you might have..."></textarea><br>
      </fieldset>

      <input type="submit" value="Apply" data-aos="zoom-in" data-aos-duration="800" data-aos-once="true">
    </form>
  </main>

  <!-- Footer -->
  <?php include 'footer.inc'; ?>

<script>
  AOS.init();
</script>

</body>
</html>

