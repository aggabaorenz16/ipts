<?php
session_start();
require_once('./db/database.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QCUAI</title>
    <link rel="stylesheet" href="./css/style.css">
    <script defer src="script.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    /* Our Staff Section */
.staff-section {
    background-image: url('image/staff.jpg'); /* Corrected path */
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    padding: 60px 20px;
}
.staff-section .section-content {
    text-align: center;
}

.staff-section .section-title {
    color: white;
    font-size: 20px;
    margin-bottom: 20px;
}

.staff-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px;
}

.staff-member {
    background-color: #EBE5C2;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border: 5px solid black;
}
.staff-member h3 {
    font-size: 15px;
    margin-bottom: 10px;
    color: black;
}

.staff-member p {
    font-size: 15px;
    color: black;
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

    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="section-content">
                <div class="hero-details">
                   <!--  <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br> -->
                    <h2 class="title">Quezon City University - Center for Urban Agriculture and Innovation</h2>
                    <h3 class="description">To be the hub of urban agriculture and innovation
initiatives to achieve the sustainable development goals.</h3>
                    <div class="buttons">
                        <a href="#about" class="button browse">Read more</a>
                    </div>
                </div>
                <div class="hero-logos">
                    <img src="image/qcuai logo.png" alt="QCUAI Logo" class="hero-qcuai">
                    <img src="image/qcu logo.png" alt="QCU Logo" class="hero-qcu">
                </div>
            </div>
        </section>

        <!-- Vision, Mission, and Functional Statement Section -->
        <section class="mv-section">
            <div class="swiper mv-swiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide mv-box">
                        <p class="mv-title">VISION</p>
                        <p class="mv-text">TO BE RECOGNIZED AS THE LEADER IN URBAN AGRICULTURE AND INNOVATION</p>
                    </div>
                    <div class="swiper-slide mv-box">
                        <p class="mv-title">FUNCTIONAL STATEMENT</p>
                        <p class="mv-text">TO BE THE HUB OF URBAN AGRICULTURE AND INNOVATION INITIATIVES TO ACHIEVE THE SUSTAINABLE DEVELOPMENT GOALS</p>
                    </div>
                    <div class="swiper-slide mv-box">
                        <p class="mv-title">MISSION</p>
                        <p class="mv-text">TO BE THE HUB OF URBAN AGRICULTURE INITIATIVES TO ACHIEVE THE SUSTAINABLE DEVELOPMENT GOALS</p>
                    </div>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
        </section>

        <!-- About Us Section -->
        <section class="about-section" id="about">
            <div class="section-content">
                <div class="about-container">
                    <div class="image-wrapper">
                        <img src="image/IMG_2727.jpg">
                        <img src="image/IMG_2724.jpg">
                        <img src="image/IMG_2725.jpg">
                        <img src="image/IMG_2731.jpg">
                        <img src="image/IMG_2735.jpg">
                    </div>
                </div>
                <div class="about-details">
                    <h2 class="section-title" style="color: green; font-size: 35px;">ABOUT US</h2>
                    <p class="text">In August 2021, The Quezon City University - Center for Urban Agriculture and Innovation (QCU-CUAI)...</p>
                    <p class="text">With the ongoing efforts and commitment to Quezon City Government to achieve sustainable living...</p>
                </div>
            </div>
        </section>

        <!-- Our Staff Section -->
        <section class="staff-section">
            <div class="section-content">
                <h2 class="section-title" style="color: white; font-size: 35px;">OUR STAFF</h2>
                <div class="staff-list">
                    <div class="staff-member">
                        <h3>DR. TERESITA V. ATIENZA</h3>
                        <p>University President</p>
                    </div>
                    <div class="staff-member">
                        <h3>MR. ROMEL O. SEVILLA</h3>
                        <p>Head</p>
                    </div>
                    <div class="staff-member">
                        <h3>MS. JUSTINE ANGELA MARRIELE SANCHEZ</h3>
                        <p>Administrative Staff</p>
                    </div>
                    <div class="staff-member">
                        <h3>MR. JUSTIN F. MALINDAO</h3>
                        <p>Agriculturist</p>
                    </div>
                    <div class="staff-member">
                        <h3>MR. JAYLENON R. ASILO</h3>
                        <p>Agriculturist</p>
                    </div>
                    <div class="staff-member">
                        <h3>MR. DEXTER D. PENOHERMOSO</h3>
                        <p>Agriculturist</p>
                    </div>
                    <div class="staff-member">
                        <h3>MS. ISAIAH KYLA O. OLAYON</h3>
                        <p>Laborer</p>
                    </div>
                    <div class="staff-member">
                        <h3>MR. HIPOLITO O. LOPEZ</h3>
                        <p>Laborer</p>
                    </div>
                    <div class="staff-member">
                        <h3>MR. ADRIAN C. ARELLANO</h3>
                        <p>Laborer</p>
                    </div>
                    <div class="staff-member">
                        <h3>MR. ALLAN I. GUERIÑA</h3>
                        <p>Laborer</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Crops Section -->
        <!-- <section class="crop-section">
            <div class="section-content">
                <h2 class="section-title">OUR CROPS</h2>
                <div class="crop-items">
                    <a href="crop.php" class="crop-button">
                        <figure class="container">
                            <img src="image/fruit-8160703_1280.jpg" alt="fruit" />
                            <figcaption><h3>Read more</h3></figcaption>
                        </figure>
                    </a>
                    <a href="crop.php" class="crop-button">
                        <figure class="container">
                            <img src="image/veg.jpg" alt="Veggies" />
                            <figcaption><h3>Read more</h3></figcaption>
                        </figure>
                    </a>
                    <a href="crop.php" class="crop-button">
                        <figure class="container">
                            <img src="image/wheat-6710447_1280.jpg" alt="Wheat" />
                            <figcaption><h3>Read more</h3></figcaption>
                        </figure>
                    </a>
                </div>
            </div>
        </section> -->

        <!-- Footer -->
        <section class="footer">
            <div class="footer-description">
                <h2>Quezon City University - Center for Urban Agriculture and Innovation</h2>
                <p>Welcome to the Official Page of Center for Urban Agriculture and Innovation of Quezon City University</p>
                <h2>For more information, inquiries you can contact us here :</h2>
            </div>
            <div class="footer-content">
                <div class="social-icons">
                    <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="https://youtube.com" target="_blank"><i class="fab fa-youtube"></i></a>
                </div>
                <p>&copy; 2025 QCU - Center for Urban Agriculture and Innovation. All rights reserved.</p>
            </div>
        </section>
    </main>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper('.mv-swiper', {
            loop: true,
            pagination: {
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
        });
    </script>

</body>
</html>
