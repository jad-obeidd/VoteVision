<?php
include 'connection.php';
$id = $_GET['id'];

// Get current data
$sql = "SELECT * FROM candidates WHERE id = $id";
$result = $conn->query($sql);
$candidate = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $party = $_POST['party'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Handle image
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmp, "images/" . $image);
    } else {
        $image = $candidate['image_url'];
    }

    $update = "UPDATE candidates SET 
        name='$name', party='$party', title='$title', description='$description', image_url='$image'
        WHERE id = $id";

    $conn->query($update);
    header("Location: events_overview.php");
    exit();
}
?>

<!-- ✅ Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
    <div class="card shadow p-4">
        <h2 class="mb-4">Edit Candidate</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="<?= $candidate['name'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Party</label>
                <input type="text" name="party" class="form-control" value="<?= $candidate['party'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="<?= $candidate['title'] ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4"><?= $candidate['description'] ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Current Image</label><br>
                <img src="images/<?= $candidate['image_url'] ?>" width="120" class="img-thumbnail mb-2"><br>
                <input type="file" name="image" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Update Candidate</button>
        </form>
    </div>
</div>