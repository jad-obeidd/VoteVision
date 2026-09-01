<!DOCTYPE html>
<html lang="en"> <!-- Sets the language to English -->

<head>
    <meta charset="UTF-8" /> <!-- Character encoding: allows special characters -->
    <meta name="viewport" content="width=device-width, initial-scale=1" /> <!-- Responsive scaling on mobile -->
    <title>VoteVision Login</title> <!-- Page title shown in browser tab -->

    <!-- 🔗 Bootstrap CSS for layout and components -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- 🎨 Internal CSS Styling -->
    <style>
        body {
            background: linear-gradient(to right, #0d1b2a, #1b263b, #415a77);
            /* Gradient background */
            min-height: 100vh;
            /* Minimum height of the viewport */
            display: flex;
            /* Flex to center the content */
            align-items: center;
            /* Vertically center */
            justify-content: center;
            /* Horizontally center */
            font-family: "Poppins", sans-serif;
            /* Font family */
            padding: 20px;
            /* Inner space around body */
        }

        .login-card {
            background: #f8f9fa;
            /* Light background color */
            border-radius: 15px;
            /* Rounded corners */
            padding: 50px 30px;
            /* Space inside the card */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            /* Drop shadow */
            width: 100%;
            max-width: 450px;
            /* Max width of the card */
            text-align: center;
            /* Center the text */
            animation: fadeIn 1s ease-in-out;
            /* Fade in effect */
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                /* Start invisible */
                transform: translateY(20px);
                /* Move up while fading */
            }

            to {
                opacity: 1;
                transform: translateY(0);
                /* Final position */
            }
        }

        .login-logo {
            width: 100px;
            /* Logo size */
            margin-bottom: 20px;
            animation: logoBounce 1.2s ease;
            /* Logo entrance animation */
        }

        @keyframes logoBounce {
            0% {
                transform: scale(0.5);
                opacity: 0;
            }

            60% {
                transform: scale(1.1);
                opacity: 1;
            }

            100% {
                transform: scale(1);
                /* Final size */
            }
        }

        .login-card h3 {
            color: #1b263b;
            font-weight: 700;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .form-floating>.form-control:focus~label {
            color: #1b263b;
            /* Label color on input focus */
        }

        .form-control:focus {
            border-color: #1b263b;
            /* Border color on focus */
            box-shadow: 0 0 10px #1b263b;
            /* Shadow on input focus */
        }

        .btn-election {
            background-color: #1b263b;
            /* Button color */
            color: #fff;
            border: none;
            transition: all 0.3s;
            /* Smooth animation */
            font-weight: bold;
            border-radius: 8px;
        }

        .btn-election:hover {
            background-color:rgb(37, 140, 5);
            box-shadow: 0 0 10px #778da9;
            /* Glowing effect */
            transform: translateY(-2px);
            /* Button lift */
        }

        .signup-link {
            margin-top: 20px;
            color: #415a77;
            font-weight: bold;
            text-decoration: none;
            display: block;
        }

        .signup-link:hover {
            color: #1b263b;
            text-decoration: underline;
        }

        .small-text {
            font-size: 13px;
            color: #6c757d;
        }
    </style>
</head>

<body>

    <!-- 🧾 Login Form Card -->
    <div class="login-card">

        <!-- ✅ App Logo -->
        <img src="images/img.png" alt="VoteVision Logo" class="login-logo" />

        <!-- ✅ Title -->
        <h3>VoteVision Portal</h3>

        <!-- ✅ PHP Error Message if Login Fails -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <!-- ✅ Login Form -->
        <form action="login.php" method="POST">
            <!-- Email Input -->
            <div class="form-floating mb-3">
                <input type="email" name="email" class="form-control" id="emailInput" placeholder="name@example.com" required />
                <label for="emailInput">Email Address</label>
            </div>

            <!-- Password Input -->
            <div class="form-floating mb-4">
                <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Password" required />
                <label for="passwordInput">Password</label>
            </div>

            <!-- ✅ Login Button -->
            <button type="submit" class="btn btn-election w-100 py-2 mb-3">Login</button>

            <!-- ✅ Small description -->
            <div class="small-text">Secure access to elections and polls</div>

            <!-- ✅ Link to Register -->
            <a href="register.php" class="signup-link">Don't have an account? Register</a>

            <!-- ✅ Back to Home Button -->
            <a href="index.php" class="btn btn-secondary w-100 mt-3">← Back to Home</a>
        </form>
    </div>

</body>

</html>