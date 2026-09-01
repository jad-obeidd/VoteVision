<?php
// ✅ Start session and connect to the database
session_start();
include 'connection.php';

// ✅ Get the current page name (used to highlight the active link in navbar)
$currentPage = basename($_SERVER['PHP_SELF']);

// ✅ Fetch dashboard data from the database
$totalCandidates = $conn->query("SELECT COUNT(*) AS total FROM candidates")->fetch_assoc()['total'];
$activePolls = $conn->query("SELECT COUNT(*) AS total FROM polls WHERE end_date >= CURDATE()")->fetch_assoc()['total'];
$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM user")->fetch_assoc()['total'];

// ✅ Get the latest news content
$news = $conn->query("SELECT content FROM news LIMIT 1")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>VoteVision Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- ✅ Include Bootstrap, Icons, and Animation CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- ✅ Custom CSS Styles -->
  <style>
    body {
      background: url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1600&q=80') no-repeat center center fixed;
      background-size: cover;
      backdrop-filter: blur(5px);
      padding-top: 80px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .navbar {
      background: linear-gradient(135deg, #1d3557, #457b9d);
    }

    .navbar-brand,
    .nav-link {
      color: #f1f1f1 !important;
    }

    .navbar-nav .nav-link:hover {
      background-color: rgba(255, 255, 255, 0.15);
      color: #ffd166 !important;
    }

    .navbar-nav .active {
      color: #ffd166 !important;
    }

    .live-clock {
      color: #fff;
      font-size: 0.95rem;
      margin-left: 15px;
    }

    .hero {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 15px;
      padding: 50px 30px;
      margin-bottom: 40px;
    }

    .stat-box {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    #scrollTopBtn {
      position: fixed;
      bottom: 30px;
      right: 20px;
      display: none;
      z-index: 1000;
    }
  </style>
</head>

<body>
  <!-- ✅ Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm py-3">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php">
        <img src="images/img.png" width="30" class="me-2" /> VoteVision
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- ✅ Nav Links with active highlighting -->
      <div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
        <ul class="navbar-nav mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link px-3 <?= $currentPage === 'home.php' ? 'active' : '' ?>" href="home.php">Home</a></li>
          <li class="nav-item"><a class="nav-link px-3 <?= $currentPage === 'events_overview.php' ? 'active' : '' ?>" href="events_overview.php">Candidates</a></li>
          <li class="nav-item"><a class="nav-link px-3 <?= $currentPage === 'poll.php' ? 'active' : '' ?>" href="poll.php">Polls</a></li>
          <li class="nav-item"><a class="nav-link px-3 <?= $currentPage === 'profile.php' ? 'active' : '' ?>" href="profile.php">Profile</a></li>

          <!-- ✅ Show extra links if user is logged in -->
          <?php if (isset($_SESSION['isloggedin'])): ?>
            <li class="nav-item"><a class="nav-link px-3 <?= $currentPage === 'my_votes.php' ? 'active' : '' ?>" href="my_votes.php">History</a></li>
            <li class="nav-item"><a class="nav-link px-3" href="logout.php">Logout</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link px-3" href="login.php">Login</a></li>
          <?php endif; ?>
        </ul>
        <span id="clock" class="live-clock"></span>
      </div>
    </div>
  </nav>

  <!-- ✅ Hero Section -->
  <div class="container">
    <div class="hero text-center animate__animated animate__fadeInDown">
      <h1>Welcome to VoteVision 🗳️</h1>
      <?php if (isset($_SESSION['name'])): ?>
        <p class="lead">Hi <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>, ready to vote and lead change?</p>
      <?php else: ?>
        <p class="lead">Login or register to participate in national polls and view election results live.</p>
      <?php endif; ?>
      <a href="poll.php" class="btn btn-primary mt-3 px-4 py-2">Vote Now</a>
    </div>
  </div>

  <!-- ✅ Dashboard Stats -->
  <div class="container mb-5">
    <div class="row text-center g-4">
      <div class="col-md-4">
        <div class="stat-box animate__animated animate__fadeInUp">
          <h5>👤 Total Candidates</h5>
          <h2><?= $totalCandidates ?></h2>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-box animate__animated animate__fadeInUp animate__delay-1s">
          <h5>📊 Active Polls</h5>
          <h2><?= $activePolls ?></h2>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-box animate__animated animate__fadeInUp animate__delay-2s">
          <h5>👥 Registered Users</h5>
          <h2><?= $totalUsers ?></h2>
        </div>
      </div>
    </div>
  </div>

  <!-- ✅ News & Top Users -->
  <div class="container mb-5">
    <div class="d-flex flex-column flex-md-row gap-4">
      <!-- News Section -->
      <div class="flex-fill bg-white p-4 rounded-4 shadow-sm d-flex flex-column justify-content-between">
        <div>
          <h5 class="fw-bold mb-3"><i class="bi bi-newspaper me-2"></i> Latest News & Events</h5>
          <?php if ($news && $news['content']): ?>
            <?= nl2br($news['content']) ?>
          <?php else: ?>
            <p class="text-muted">No news available yet.</p>
          <?php endif; ?>
        </div>
        <div class="mt-4 text-end">
          <a href="update_news.php" class="btn btn-outline-primary">✏️ Update News</a>
        </div>
      </div>

      <!-- Top Users Section -->
      <div class="flex-fill bg-white p-4 rounded-4 shadow-sm">
        <h5 class="fw-bold mb-3"><i class="bi bi-trophy-fill text-warning me-2"></i> Top 5 Engaged Users</h5>
        <ul class="list-unstyled mb-0" id="top-users">
          <li class="text-muted">Loading top users...</li>
        </ul>
      </div>
    </div>
  </div>

  <!-- ✅ Footer -->
  <footer class="text-white pt-5 pb-3" style="background: linear-gradient(135deg, #1d3557, #457b9d);">
    <div class="container">
      <div class="row">
        <!-- About -->
        <div class="col-md-4 mb-4">
          <h4 class="fw-bold"><img src="images/img.png" width="30" class="me-2" />VoteVision</h4>
          <p>Your voice. Your choice. Vote with confidence and shape the future.</p>
        </div>

        <!-- Quick Links -->
        <div class="col-md-4 mb-4">
          <h5 class="mb-3">Quick Links</h5>
          <ul class="list-unstyled">
            <li><a href="home.php" class="text-white text-decoration-none">🏠 Home</a></li>
            <li><a href="events_overview.php" class="text-white text-decoration-none">👥 Candidates</a></li>
            <li><a href="poll.php" class="text-white text-decoration-none">🗳️ Polls</a></li>
            <li><a href="profile.php" class="text-white text-decoration-none">👤 Profile</a></li>
            <li><a href="index.php" class="text-white text-decoration-none">🔐 Logout</a></li>
          </ul>
        </div>

        <!-- Contact Info -->
        <div class="col-md-4 mb-4">
          <h5 class="mb-3">Contact</h5>
          <p><i class="bi bi-envelope-fill me-2"></i> support@votevision.com</p>
          <p><i class="bi bi-geo-alt-fill me-2"></i> Beirut, Lebanon</p>
          <div class="mt-3">
            <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
            <a href="#" class="text-white me-3"><i class="bi bi-twitter"></i></a>
            <a href="#" class="text-white me-3"><i class="bi bi-instagram"></i></a>
          </div>
        </div>
      </div>
      <hr class="border-light" />
      <div class="text-center">&copy; <?= date('Y') ?> VoteVision. All rights reserved.</div>
    </div>
  </footer>

  <!-- ✅ Scroll To Top Button -->
  <button onclick="scrollToTop()" id="scrollTopBtn" class="btn btn-dark rounded-circle">↑</button>

  <!-- ✅ JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // ✅ Load top users from server every 30 seconds
    function loadTopUsers() {
      fetch('get_top_users.php')
        .then(res => res.text())
        .then(html => {
          document.getElementById('top-users').innerHTML = html;
        });
    }
    loadTopUsers();
    setInterval(loadTopUsers, 30000);

    // ✅ Live Clock
    function updateClock() {
      const now = new Date();
      const time = now.toLocaleTimeString();
      const date = now.toLocaleDateString();
      document.getElementById('clock').textContent = `${date} ${time}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // ✅ Scroll To Top Button Show/Hide
    const scrollTopBtn = document.getElementById('scrollTopBtn');
    window.onscroll = () => {
      scrollTopBtn.style.display = window.scrollY > 300 ? 'block' : 'none';
    };

    function scrollToTop() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    }
  </script>
</body>

</html>