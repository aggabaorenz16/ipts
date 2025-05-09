<?php
require_once('./db/database.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$searchTerm = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchTerm = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT name, description, image FROM crops WHERE name LIKE CONCAT('%', ?, '%')");
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT name, description, image FROM crops";
    $result = $conn->query($sql);
}

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QCUAI - Crops</title>
    <link rel="stylesheet" href="./css/crop.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Simple modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0; top: 0;
            width: 100%; height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            width: 60%;
            max-width: 700px;
            position: relative;
        }

        .modal-close {
            color: #aaa;
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 10px;
        }

        .modal-close:hover {
            color: red;
        }
    </style>
</head>
<body>
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
                <li class="nav-item"><a href="login.php" class="nav-link">Log in</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="crop-section">
            <div class="section-content">
                <h2 class="section-title">OUR CROPS</h2>
<div class="search-container">
    <form id="searchForm" method="GET" action="">
        <div class="search-input-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" name="search" placeholder="Search crops..." value="<?php echo htmlspecialchars($searchTerm); ?>">
        </div>
    </form>
</div>

                <div class="crop-items">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $cropName = $row['name'];
                            $cropDescription = $row['description'];
                            $imagePath = $row['image'];
                            ?>
                            <div class="crop-box">
                                <figure class="container">
                                    <img src="<?php echo $imagePath; ?>" alt="<?php echo $cropName; ?>" />
                                    <figcaption>
                                        <button class="read-more-btn" data-name="<?php echo htmlspecialchars($cropName); ?>" data-description="<?php echo htmlspecialchars($cropDescription); ?>">Read More</button>
                                    </figcaption>
                                </figure>
                                <p class="crop-name"><?php echo $cropName; ?></p>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p style='text-align:center; font-size:18px;'>No crops found.</p>";
                    }
                    ?>
                </div>
            </div>
        </section>

        <!-- Modal Structure -->
        <div id="cropModal" class="modal">
            <div class="modal-content">
                <span class="modal-close">&times;</span>
                <h2 id="modalCropName"></h2>
                <p id="modalCropDescription"></p>
            </div>
        </div>

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

    <script>
        // JavaScript for modal handling
        const modal = document.getElementById("cropModal");
        const modalName = document.getElementById("modalCropName");
        const modalDescription = document.getElementById("modalCropDescription");
        const closeModal = document.querySelector(".modal-close");

        document.querySelectorAll('.read-more-btn').forEach(button => {
            button.addEventListener('click', function () {
                const name = this.getAttribute('data-name');
                const description = this.getAttribute('data-description');
                modalName.textContent = name;
                modalDescription.textContent = description;
                modal.style.display = "block";
            });
        });

        closeModal.onclick = function () {
            modal.style.display = "none";
        }

        window.onclick = function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
