<?php
// ✅ Connect to the database
include 'connection.php';

// ✅ Start session (useful if user login is needed)
session_start();

// ✅ Handle the form when submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ✅ Get input values from the form
    $title = $_POST['title'];
    $description = $_POST['description'];
    $end_date = $_POST['end_date'];
    $options = $_POST['options'];
    $max_selections = (int)$_POST['max_selections'];

    // ❌ Reject if end date is in the past
    if ($end_date < date('Y-m-d')) {
        die("❌ Error: End date cannot be in the past.");
    }

    // ✅ Remove empty options
    $valid_options = array_filter($options, fn($opt) => trim($opt) !== '');
    $total_options = count($valid_options);

    // ❌ At least 2 options are required
    if ($total_options < 2) {
        die("❌ Error: Please enter at least 2 options.");
    }

    // ❌ Max selection must be 1 to total options
    if ($max_selections < 1 || $max_selections > $total_options) {
        die("❌ Error: Max selection must be between 1 and the number of options.");
    }

    // ✅ Handle optional image upload
    $image_name = '';
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . basename($_FILES['image']['name']); // give image a unique name
        move_uploaded_file($_FILES['image']['tmp_name'], 'images/' . $image_name);
    }

    // ✅ Create a unique random token (can be used for sharing)
    $token = bin2hex(random_bytes(8));

    // ✅ Insert the poll into the `polls` table
    $sql = "INSERT INTO polls (title, description, end_date, max_selections, token, image_url)
            VALUES ('$title', '$description', '$end_date', $max_selections, '$token', '$image_name')";

    // ✅ If poll was inserted successfully
    if ($conn->query($sql)) {
        $poll_id = $conn->insert_id; // get the ID of the inserted poll

        // ✅ Insert each poll option
        foreach ($valid_options as $opt) {
            $opt = trim($opt);
            $conn->query("INSERT INTO poll_options (poll_id, option_text) VALUES ($poll_id, '$opt')");
        }

        // ✅ Redirect back to poll list
        header("Location: poll.php");
        exit();
    } else {
        // ❌ Show SQL error if something goes wrong
        echo "Error: " . $conn->error;
    }
}
?>
>

<!-- ✅ HTML FORM PAGE TO CREATE A POLL -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create New Poll - VoteVision</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ✅ Bootstrap & SweetAlert -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ✅ Custom Styles -->
    <style>
        body {
            background: linear-gradient(to right, #74ebd5, #acb6e5);
            min-height: 100vh;
            display: flex;
            align-items: start;
            justify-content: center;
            padding: 40px 20px;
            font-family: 'Segoe UI', sans-serif;
        }

        .form-box {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            animation: fadeIn 0.8s ease;
            position: relative;
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

        h3 {
            font-weight: bold;
            color: #1d3557;
            text-align: center;
            margin-bottom: 30px;
        }

        label {
            font-weight: 500;
            color: #333;
        }

        .btn-primary {
            background: #1d3557;
            font-weight: bold;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background: #457b9d;
        }

        .btn-back {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #1d3557;
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #457b9d;
        }
    </style>
</head>

<body>
    <div class="form-box">

        <!-- 🔙 Back to poll list -->
        <a href="poll.php" class="btn-back">← Back to Polls</a>

        <!-- 📝 Form Title -->
        <h3>🗳️ Create a New Poll</h3>

        <!-- ✅ Poll Creation Form -->
        <form method="POST" enctype="multipart/form-data" onsubmit="return validateMaxSelection()">
            <div class="mb-3">
                <label>Poll Title:</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Description (optional):</label>
                <textarea name="description" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label>End Date:</label>
                <input type="date" name="end_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Poll Image (optional):</label>
                <input type="file" name="image" class="form-control">
            </div>

            <!-- ✅ Dynamic options -->
            <div class="mb-3">
                <label>Poll Options:</label>
                <div id="options-container">
                    <input type="text" name="options[]" class="form-control mb-2 option-input" placeholder="Option 1" required>
                    <input type="text" name="options[]" class="form-control mb-2 option-input" placeholder="Option 2" required>
                </div>
                <button type="button" onclick="addOption()" class="btn btn-outline-secondary btn-sm mt-2">+ Add Another Option</button>
            </div>

            <!-- ✅ Max Selections Field -->
            <div class="mb-3">
                <label>Max Selections Allowed:</label>
                <input type="number" name="max_selections" id="max_selections" class="form-control" min="1" value="1" required>
                <small class="text-muted" id="maxHint">You can choose up to 1 option.</small>
            </div>

            <button type="submit" class="btn btn-primary w-100">✅ Create Poll</button>
        </form>
    </div>

    <!-- ✅ JavaScript for adding/removing options and validation -->
    <script>
        let optionCount = 3;

        // ➕ Add a new option field dynamically
        function addOption() {
            const container = document.getElementById('options-container');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'options[]';
            input.placeholder = 'Option ' + optionCount++;
            input.className = 'form-control mb-2 option-input';
            container.appendChild(input);
            updateMaxSelection();
        }

        // 🔁 Update the max selection based on options count
        function updateMaxSelection() {
            const options = document.querySelectorAll('input[name="options[]"]');
            const validCount = Array.from(options).filter(opt => opt.value.trim() !== '').length;
            const maxInput = document.getElementById('max_selections');
            const hint = document.getElementById('maxHint');

            const max = Math.max(1, validCount);
            maxInput.max = max;
            hint.innerText = `You can choose up to ${max} option${max > 1 ? 's' : ''}.`;

            if (parseInt(maxInput.value) > max) {
                maxInput.value = max;
            }
        }

        // ✅ Final form validation before submit
        function validateMaxSelection() {
            const options = document.querySelectorAll('input[name="options[]"]');
            const validOptions = Array.from(options).filter(opt => opt.value.trim() !== '');
            const max = parseInt(document.getElementById('max_selections').value);

            if (validOptions.length < 2) {
                Swal.fire('⚠️ Warning', 'Please enter at least 2 options.', 'warning');
                return false;
            }

            if (max < 1 || max > validOptions.length) {
                Swal.fire('⚠️ Warning', `Max selection must be between 1 and ${validOptions.length}.`, 'warning');
                return false;
            }

            return true;
        }

        // 🔄 Live update on typing
        document.addEventListener('input', e => {
            if (e.target.classList.contains('option-input') || e.target.id === 'max_selections') {
                updateMaxSelection();
            }
        });
    </script>
</body>

</html>