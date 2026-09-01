<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Makes the site scale properly on mobile (responsive layout) -->
  <title>VoteVision Carousel</title>
  <style>
    /* Apply global reset and font */
    * {
      margin: 0;
      /* Remove default margin from all elements */
      padding: 0;
      /* Remove default padding from all elements */
      box-sizing: border-box;
      /* Includes border and padding in width/height */
      font-family: "Poppins", sans-serif;
      /* Set default font */
    }

    body,
    html {
      height: 100%;
      /* Full height of the viewport */
      margin: 0;
      /* Remove default browser margin */
      padding: 0;
      /* Remove default browser padding */
      font-family: "Poppins", sans-serif;
      /* Use Poppins font */
      background: linear-gradient(135deg, #4e54c8, #8f94fb, #ff9a9e);
      /* Background gradient */
    }

    .banner {
      width: 100%;
      /* Full width */
      height: 100vh;
      /* Full viewport height */
      display: flex;
      /* Use Flexbox */
      justify-content: center;
      /* Center items horizontally */
      align-items: center;
      /* Center items vertically */
      perspective: 1000px;
      /* Adds 3D effect to children */
      position: relative;
      /* For absolutely positioned children */
      overflow: hidden;
      /* Hide anything outside the box */
    }

    .slider {
      position: relative;
      /* For stacking and animation */
      width: 300px;
      /* Width of the carousel */
      height: 300px;
      /* Height of the carousel */
      transform-style: preserve-3d;
      /* Keeps 3D transformations for children */
      animation: spin 20s linear infinite;
      /* Rotating animation */
      z-index: 2;
      /* Above background */
    }

    .item {
      position: absolute;
      /* To freely position in 3D space */
      width: 100px;
      /* Width of each card */
      height: 150px;
      /* Height of each card */
      left: 100px;
      /* Offset from the left inside slider */
      top: 75px;
      /* Offset from the top */
      transform: rotateY(calc((var(--position) - 1) * 36deg)) translateZ(400px);
      /* Rotate and move in 3D circle */
    }

    .item img {
      width: 100%;
      /* Full width of container */
      height: 100%;
      /* Full height of container */
      object-fit: cover;
      /* Keeps aspect ratio, fills container */
      border-radius: 10px;
      /* Rounded corners */
    }

    @keyframes spin {
      from {
        transform: rotateX(-10deg) rotateY(0deg);
        /* Starting rotation */
      }

      to {
        transform: rotateX(-100deg) rotateY(360deg);
        /* Full spin */
      }
    }

    .model {
      position: absolute;
      /* Freely positioned */
      top: 50%;
      /* Center vertically */
      left: 50%;
      /* Center horizontally */
      width: 180px;
      /* Width of center image */
      height: 75vh;
      /* 75% of screen height */
      background-image: url(images/picture11.jpeg);
      /* Background image */
      background-size: cover;
      /* Fully cover the container */
      background-position: top center;
      /* Focus top-center */
      background-repeat: no-repeat;
      /* Don’t repeat */
      transform: translate(-50%, -50%);
      /* Perfect centering */
      z-index: 1;
      /* Behind the carousel items */
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      /* Shadow around model */
      border-radius: 12px;
      /* Rounded corners */
      pointer-events: none;
      /* Prevent interaction (clicks, etc.) */
    }

    .login-btn {
      position: absolute;
      /* Absolute positioning */
      top: 25px;
      /* Distance from top */
      right: 40px;
      /* Distance from right */
      padding: 10px 25px;
      /* Inner space */
      background-color: #1d3557;
      /* Dark blue background */
      color: white;
      /* White text */
      text-decoration: none;
      /* No underline */
      border-radius: 30px;
      /* Fully rounded button */
      font-weight: 600;
      /* Bold font */
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      /* Soft shadow */
      z-index: 999;
      /* Very top layer */
      transition: background 0.3s ease;
      /* Smooth hover effect */
    }

    .login-btn:hover {
      background-color:rgb(157, 69, 69);
      /* Lighter red on hover */
    }

    .branding {
      position: absolute;
      /* Freely position */
      top: 25px;
      /* Distance from top */
      left: 40px;
      /* Distance from left */
      z-index: 5;
      /* Above many elements */
      color: #1d3557;
      /* Text color */
    }

    .branding img {
      width: 35px;
      /* Logo width */
      vertical-align: middle;
      /* Align with text */
      margin-right: 8px;
      /* Space between logo and title */
    }

    .logo-title {
      font-size: 26px;
      /* Large title */
      font-weight: 800;
      /* Extra bold */
      letter-spacing: 1px;
      /* Spaced out letters */
      margin-bottom: 4px;
      /* Small space below */
      display: inline-block;
      /* Align side by side with logo */
      vertical-align: middle;
      /* Align with logo image */
    }

    .logo-subtitle {
      font-size: 14px;
      /* Small subtitle */
      font-weight: 500;
      /* Medium weight */
      color: #555;
      /* Light gray text */
    }

    .welcome-text {
      position: absolute;
      /* Freely position */
      bottom: 50px;
      /* Distance from bottom */
      text-align: center;
      /* Center text */
      color: #1d3557;
      /* Text color */
      font-size: 1.2rem;
      /* Medium font size */
      font-weight: 500;
      /* Semi-bold */
    }
  </style>

</head>

<body>
  <div class="banner">
    <div class="slider" style="--quantity: 10">
      <div class="item" style="--position: 1"><img src="images/picture1.jpeg" alt=""></div>
      <div class="item" style="--position: 2"><img src="images/picture2.jpg" alt=""></div>
      <div class="item" style="--position: 3"><img src="images/picture3.webp" alt=""></div>
      <div class="item" style="--position: 4"><img src="images/picture4.png" alt=""></div>
      <div class="item" style="--position: 5"><img src="images/picture5.jpeg" alt=""></div>
      <div class="item" style="--position: 6"><img src="images/picture6.png" alt=""></div>
      <div class="item" style="--position: 7"><img src="images/picture7.avif" alt=""></div>
      <div class="item" style="--position: 8"><img src="images/picture8.jpg" alt=""></div>
      <div class="item" style="--position: 9"><img src="images/picture9.jpg" alt=""></div>
      <div class="item" style="--position: 10"><img src="images/picture10.png" alt=""></div>
    </div>

    <div class="model"></div>

    <a href="login1.php" class="login-btn">Login</a>

    <!-- ✅ Branding with Logo -->
    <div class="branding">
      <img src="images/img.png" alt="VoteVision Logo" />
      <p class="logo-title">VoteVision</p>
      <p class="logo-subtitle">Online Voting System</p>
    </div>

    <div class="welcome-text">
      <p>Make your voice count - vote now with with vision polls!</p>
    </div>
  </div>
</body>

</html>