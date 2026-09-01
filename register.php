<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom Styling -->
<style>
    body {
        background: linear-gradient(to right, #0d1b2a, #1b263b, #415a77);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Poppins', sans-serif;
    }

    .signup-card {
        background: #f8f9fa;
        padding: 45px 30px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        width: 100%;
        max-width: 450px;
        text-align: center;
        animation: fadeIn 1s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .signup-card h3 {
        color: #1b263b;
        font-weight: bold;
        margin-bottom: 30px;
    }

    .form-control:focus {
        border-color: #1b263b;
        box-shadow: 0 0 10px #1b263b;
    }

    .btn-election {
        background-color: #1b263b;
        color: #fff;
        border: none;
        transition: 0.3s;
        font-weight: bold;
        border-radius: 8px;
    }

    .btn-election:hover {
        background-color: #0d1b2a;
        box-shadow: 0 0 10px #778da9;
        transform: translateY(-2px);
    }

    .btn-reset {
        border: 2px solid #1b263b;
        color: #1b263b;
        font-weight: bold;
        transition: 0.3s;
        border-radius: 8px;
    }

    .btn-reset:hover {
        background-color: #1b263b;
        color: white;
    }

    .btn-home {
        margin-top: 10px;
        background-color: #6c757d;
        color: #fff;
        font-weight: bold;
        border: none;
        border-radius: 8px;
    }

    .btn-home:hover {
        background-color: #495057;
        box-shadow: 0 0 8px #ccc;
    }

    .small-link {
        font-size: 14px;
        color: #415a77;
        text-decoration: none;
        font-weight: bold;
    }

    .small-link:hover {
        color: #1b263b;
        text-decoration: underline;
    }
</style>

<!-- Sign Up Form -->
<div class="signup-card">
    <h3>Join VoteVision</h3>
    <form action="register2.php" method="POST" enctype="multipart/form-data">
        <div class="form-floating mb-3">
            <input type="text" name="name" class="form-control" id="nameInput" placeholder="Full Name" required>
            <label for="nameInput">Full Name</label>
        </div>

        <div class="form-floating mb-3">
            <input type="email" name="email" class="form-control" id="emailInput" placeholder="Email" required>
            <label for="emailInput">Email Address</label>
        </div>

        <div class="form-floating mb-4">
            <input type="password" name="pass" class="form-control" id="passwordInput" placeholder="Password"
                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}"
                title="Password must be at least 8 characters long, include uppercase, lowercase, number, and special character."
                required>
            <label for="passwordInput">Password</label>
        </div>

        <!-- Profile Picture Upload -->
        <div class="mb-4 text-start">
            <label for="profilepic" class="form-label fw-bold">Identity Document </label>
            <input type="file" name="profilepic" id="profilepic" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-election w-100 mb-2">Create Account</button>
        <button type="reset" class="btn btn-reset w-100">Reset</button>

        <p class="mt-4">
            <a href="login1.php" class="small-link">Already have an account? Login</a>
        </p>

        <!-- Back to Home Button -->
        <a href="index.php" class="btn btn-home w-100">← Back to Home</a>
    </form>
</div>