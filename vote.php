<?php
include 'connection.php';
session_start();

$poll_id = $_GET['id'] ?? 0;
$poll_sql = "SELECT * FROM polls WHERE id = $poll_id";
$poll = $conn->query($poll_sql)->fetch_assoc();

if (!$poll) {
    die("Poll not found.");
}

if (strtotime($poll['end_date']) < time()) {
    $_SESSION['error'] = '❌ This poll has expired.';
    header("Location: poll.php");
    exit();
}

$options_sql = "SELECT * FROM poll_options WHERE poll_id = $poll_id";
$options = $conn->query($options_sql);
$max = (int)$poll['max_selections'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($poll['title']) ?> - Vote</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background: linear-gradient(135deg, #74ebd5 0%, #acb6e5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', sans-serif;
        }

        .vote-box {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            animation: fadeIn 0.8s ease;
            position: relative;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            font-weight: bold;
            color: #1d3557;
            text-align: center;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
        }

        .btn-back {
            background: #1d3557;
            color: white;
            font-weight: bold;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 14px;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn-back:hover {
            background: #457b9d;
        }

        .form-check-label {
            font-size: 18px;
            margin-left: 8px;
            color: #333;
        }

        .btn-primary {
            background: #1d3557;
            border: none;
            font-weight: bold;
            padding: 10px 20px;
            font-size: 18px;
            transition: 0.3s;
            border-radius: 8px;
            display: block;
            width: 100%;
        }

        .btn-primary:hover {
            background: #457b9d;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .form-check {
            display: flex;
            align-items: center;
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
            transition: background-color 0.3s ease;
        }

        .form-check:hover {
            background-color: #e9ecef;
        }

        .form-check-input {
            margin-right: 15px;
            transform: scale(1.4);
            cursor: pointer;
        }

        .form-check-label {
            font-size: 18px;
            margin-bottom: 0;
            cursor: pointer;
        }

        .vote-details {
            font-size: 14px;
            color: #555;
            text-align: center;
            margin-bottom: 25px;
        }
    </style>
</head>

<body>
    <div class="vote-box">
        <div class="back-button">
            <a href="poll.php" class="btn-back">&larr; Back to Polls</a>
        </div>

        <?php if (isset($_SESSION['vote_error'])): ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Vote Failed',
                    text: "<?= $_SESSION['vote_error']; ?>",
                    confirmButtonColor: '#1d3557'
                }).then(() => {
                    window.location.href = 'poll.php';
                });
            </script>
            <?php unset($_SESSION['vote_error']); ?>
        <?php elseif (isset($_SESSION['vote_success'])): ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Vote Submitted!',
                    text: "<?= $_SESSION['vote_success']; ?>",
                    confirmButtonColor: '#1d3557'
                }).then(() => {
                    window.location.href = 'poll.php';
                });
            </script>
            <?php unset($_SESSION['vote_success']); ?>
        <?php endif; ?>

        <h2><?= htmlspecialchars($poll['title']) ?></h2>
        <p class="text-muted text-center"><?= nl2br(htmlspecialchars($poll['description'])) ?></p>

        <div class="vote-details">
            <p><strong>🕐 Ends:</strong> <?= $poll['end_date'] ?></p>
            <p><strong>✅ Max Selections Allowed:</strong> <?= $max ?></p>
        </div>

        <form method="POST" action="submit_vote.php" onsubmit="return validateSelection()">
            <input type="hidden" name="poll_id" value="<?= $poll_id ?>">

            <?php while ($opt = $options->fetch_assoc()): ?>
                <div class="form-check">
                    <input class="form-check-input vote-option"
                        type="<?= $max === 1 ? 'radio' : 'checkbox' ?>"
                        name="<?= $max === 1 ? 'options' : 'options[]' ?>"
                        value="<?= $opt['id'] ?>"
                        id="opt<?= $opt['id'] ?>">
                    <label class="form-check-label" for="opt<?= $opt['id'] ?>">
                        <?= htmlspecialchars($opt['option_text']) ?>
                    </label>
                </div>
            <?php endwhile; ?>

            <button type="submit" class="btn btn-primary mt-4">🗳️ Submit Vote</button>
        </form>
    </div>

    <script>
        function validateSelection() {
            const selected = document.querySelectorAll('.vote-option:checked').length;
            const max = <?= $max ?>;

            if (selected === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Option Selected',
                    text: '⚠️ Please select at least one option.',
                    confirmButtonColor: '#1d3557'
                });
                return false;
            }

            if (selected > max) {
                Swal.fire({
                    icon: 'error',
                    title: 'Too Many Selections',
                    text: '⚠️ You can only select up to ' + max + ' option(s).',
                    confirmButtonColor: '#1d3557'
                });
                return false;
            }

            return true;
        }
    </script>
</body>

</html>