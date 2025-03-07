<?php
session_start(); // Start the session
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="author" content="Untree.co" />

  <meta name="description" content="" />
  <meta name="keywords" content="bootstrap, bootstrap4" />

  <!-- Bootstrap CSS -->
  <!-- <link href="css/bootstrap.min.css" rel="stylesheet" /> -->
  <link href="../../public/furni-ed/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    rel="stylesheet" />
  <!-- <link href="css/tiny-slider.css" rel="stylesheet" /> -->
  <link href="../../public/furni-ed/css/tiny-slider.css" rel="stylesheet" />

  <!-- <link href="css/style.css" rel="stylesheet" /> -->
  <link rel="stylesheet" href="../../public/furni-ed/css/style.css" />
  <link rel="shortcut icon" href="store.png" />
  <title>Craftify</title>
</head>

<body>
  <nav
    class="custom-navbar navbar navbar navbar-expand-md navbar-dark bg-dark"
    arial-label="Craftify navigation bar">
    <div class="container">
      <a class="navbar-brand" href="index.html">Craftify<span>.</span></a>

      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarsCraftify"
        aria-controls="navbarsCraftify"
        aria-expanded="false"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsCraftify">
        <ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">
          <li class="nav-item active">
            <a class="nav-link" href="index.html">Home</a>
          </li>
          <li><a class="nav-link" href="shop.html">Shop</a></li>
          <li><a class="nav-link" href="about.html">About us</a></li>
          <li><a class="nav-link" href="services.html">Services</a></li>
          <li><a class="nav-link" href="Singin.html">Sing in</a></li>
          <li><a class="nav-link" href="contact.html">Contact us</a></li>
        </ul>

        <ul style="align-items: center;" class="custom-navbar-cta navbar-nav mb-2 mb-md-0 ms-5">
          <?php
          if (isset($_SESSION['user_id'])) { ?>
            <li>
              <a class="nav-link" href="../profile/profile.php">
                <img src="../../public/furni-ed/images/user.svg" />
              </a>
            </li>
            <li>
              <a class="nav-link" href="cart.html">
                <img src="../../public/furni-ed/images/cart.svg" />
              </a>
            </li>
            <li>
              <a href="logout.php">
                <i class="fa-solid fa-arrow-right-from-bracket x"></i>
              </a>
            </li>
          <?php } else { ?>
            <li>
              <a class="btn btn-secondary me-2" href="../../public/login/index.php">Login</a>
            </li>
          <?php } ?>

        </ul>
      </div>
    </div>
  </nav>

  <?php
  echo isset($_SESSION['user_id']);
  ?>
</body>

</html>