<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="description"  content="IT Company - Home Page">
  <meta name="keywords"     content="HTML5, CSS">
  <meta name="author"       content="Le Ngoc Quynh Trang, Pham Truong Que An">
  <meta name="viewport"     content="width=device-width, initial-scale=1.0">
  
  <title>SonixWave | Home Page</title>

  <link rel="stylesheet" href="styles/style.css">
  <link rel="stylesheet" href="styles/responsive-nav.css">
  <script src="scripts/nav-toggle.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;700&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

</head>

<body class="homepage">
  <!-- Navigation Menu -->
  <?php include 'header.inc'; ?>

  <!-- Company Description & Graphic -->
  <section>
    <figure>
      <i class="ileft-shadow fa-solid fa-headphones"></i>
      <i class="i-left fa-solid fa-headphones" id="enhancement8"></i>
      <i class="iright-shadow fa-solid fa-guitar"></i>
      <i class="i-right fa-solid fa-guitar" id="enhancement5"></i>

      <figcaption>
        <h1>Harmonizing Technology & Creativity</h1>
        <h2>Where Innovation <br> <span data-aos="fade-up" data-aos-duration="1900" data-aos-once="true">Meets Rhythm</span></h2>
        <p>
          SonixWave blends the power of IT with the soul of music, creating innovative digital solutions that strike the perfect chord. 
          Whether you're a business seeking cutting-edge tech or an artist looking for digital transformation, we orchestrate success through creativity and technology.
        </p>
        <div class="hero-buttons">
          <a href="jobs.php" class="btn primary-btn" id="enhancement4-1">Explore Jobs</a>
          <a href="apply.php" class="btn secondary-btn">Join Us</a>
        </div>
      </figcaption>
    </figure>
  </section>

<script>
  AOS.init();
</script>
</body>
</html>

