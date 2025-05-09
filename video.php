<?php
session_start();
require_once('./db/database.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>QCUAI</title>
  <link rel="stylesheet" href="./css/style.css" />
  <script defer src="script.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    .video-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  padding: 0 20px;
}

.video-item iframe {
  width: 100%;
  border-radius: 10px;
  border: 5px solid white;
}


</style>
<body>
  <!-- Header / Navbar -->
  <header>
    <nav class="navbar">
      <div class="nav-logo">
        <img src="image/qcuai logo.png" alt="logo">
        <h2 class="logo-text">
          Quezon City University - <br> Center for Urban Agriculture and Innovation </br>
        </h2>
      </div>
      <ul class="nav-menu">
        <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
        <li class="nav-item"><a href="crop.php" class="nav-link">Crops</a></li>
        <li class="nav-item"><a href="shop.php" class="nav-link">Shop</a></li>
        <li class="nav-item"><a href="video.php" class="nav-link">Videos</a></li>
        <?php if (isset($_SESSION['email'])): ?>
          <li class="nav-item"><a href="logout.php" class="nav-link">Log out</a></li>
        <?php else: ?>
          <li class="nav-item"><a href="login.php" class="nav-link">Log in</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>
<br>
<br>
<br>
<br>
<br>
<br>
  <main>
    <!-- Hero Section -->
   <section class="video">
  <div class="section-content">
    <h2 style="text-align: center; margin-bottom: 30px; color: white;">Featured Videos from MakaAgri</h2>
    <div class="video-grid">
      <div class="video-item">
        <iframe width="100%" height="315" src="https://www.youtube.com/embed/lYoaZ7W14pE" 
          title="How to Make a Potting Mix" frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen></iframe>
      </div>
      <div class="video-item">
        <iframe width="100%" height="315" src="https://www.youtube.com/embed/28o_aBD63NU?si=BjQjM21ar1R0MI23 
          title="Urban Gardening Tips" frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen></iframe>
      </div>
      <div class="video-item">
        <iframe width="100%" height="315" src="https://www.youtube.com/embed/8mHDVGPHIFw?si=efU5aE5eVJ5QmTtK" 
          title="Planting 101" frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen></iframe>
      </div>
    </div>
  </div>
</section>

  </main>
</body>
</html>
