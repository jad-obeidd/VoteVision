<?php
session_start(); // Start session to track user (in case needed)
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Add Event - VoteVision</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- ✅ Include Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- ✅ Custom Styles -->
  <style>
    /* 🔷 General body styling */
    body {
      background: #f0f2f5;
      /* light gray background */
      padding: 40px 0;
      font-family: 'Poppins', sans-serif;
    }

    /* 🔷 Styling for the white event card */
    .add-card {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
      /* subtle shadow */
      max-width: 1000px;
      margin: auto;
    }

    /* 🔷 Styling for each list section */
    .list-group {
      border: 2px dashed #ccc;
      /* dashed border */
      padding: 20px;
      margin-bottom: 30px;
      border-radius: 10px;
    }
  </style>
</head>

<body>
  <!-- ✅ Main Container -->
  <div class="container">
    <div class="add-card">
      <h3 class="mb-4 text-center">🗳️ Create New Event</h3>

      <!-- ✅ Form starts -->
      <form action="save_event.php" method="POST" enctype="multipart/form-data" id="mainForm">
        <!-- 🔹 Event Title -->
        <div class="mb-3">
          <label class="form-label">Event Title:</label>
          <input
            type="text"
            name="event_title"
            class="form-control"
            placeholder="Election Event Name"
            required>
        </div>

        <!-- 🔹 Maximum allowed selections -->
        <div class="mb-4">
          <label class="form-label">Max Total Selections Allowed:</label>
          <input
            type="number"
            name="max_votes"
            class="form-control"
            min="1"
            required>
        </div>

        <!-- 🔹 Event Timing -->
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label class="form-label">Opens At:</label>
            <input
              type="datetime-local"
              name="start_time"
              class="form-control"
              required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Closes At:</label>
            <input
              type="datetime-local"
              name="end_time"
              class="form-control"
              required>
          </div>
        </div>

        <!-- 🔹 Container for dynamically added lists -->
        <div id="lists-container"></div>

        <!-- 🔹 Button to add a new candidate list -->
        <button
          type="button"
          class="btn btn-outline-primary mb-3"
          onclick="addList()">+ Add Candidate List</button>

        <!-- 🔹 Submit the entire form -->
        <div class="d-grid">
          <button type="submit" class="btn btn-success">✅ Save All</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ✅ JavaScript for dynamic form handling -->
  <script>
    let listCount = 0; // Counter for lists

    // Function to add a new candidate list
    function addList() {
      listCount++;
      const listId = 'list-' + listCount;

      // HTML structure for the list
      const html = `
        <div class="list-group mb-4" id="${listId}">
          <div class="mb-3">
            <label class="form-label">List Name:</label>
            <input
              type="text"
              name="lists[${listCount}][name]"
              class="form-control"
              required>
          </div>

          <!-- Container for this list's candidates -->
          <div class="candidates-container" id="candidates-${listCount}"></div>

          <!-- Button to add new candidate to this list -->
          <button
            type="button"
            class="btn btn-sm btn-secondary mb-3"
            onclick="addCandidate(${listCount})"
          >+ Add Candidate</button>
          <hr>
        </div>`;

      // Add to the page
      document.getElementById('lists-container').insertAdjacentHTML('beforeend', html);

      // Automatically add first candidate
      addCandidate(listCount);
    }

    // Function to add a candidate input row inside a list
    function addCandidate(listIndex) {
      const container = document.getElementById('candidates-' + listIndex);
      const index = container.children.length;
      const candidateId = `candidate-${listIndex}-${index}`;

      // HTML for one candidate row
      const html = `
        <div class="row mb-3 align-items-end" id="${candidateId}">
          <!-- Candidate Name -->
          <div class="col-md-3">
            <input
              type="text"
              name="lists[${listIndex}][candidates][${index}][name]"
              class="form-control"
              placeholder="Full Name"
              required>
          </div>

          <!-- Candidate Party -->
          <div class="col-md-2">
            <input
              type="text"
              name="lists[${listIndex}][candidates][${index}][party]"
              class="form-control"
              placeholder="Party"
              required>
          </div>

          <!-- Candidate Title -->
          <div class="col-md-2">
            <input
              type="text"
              name="lists[${listIndex}][candidates][${index}][title]"
              class="form-control"
              placeholder="Title"
              required>
          </div>

          <!-- Description -->
          <div class="col-md-3">
            <input
              type="text"
              name="lists[${listIndex}][candidates][${index}][description]"
              class="form-control"
              placeholder="Short Description">
          </div>

          <!-- Image Upload -->
          <div class="col-md-1">
            <input
              type="file"
              name="lists[${listIndex}][candidates][${index}][image]"
              class="form-control"
              required>
          </div>

          <!-- Delete button -->
          <div class="col-md-1 text-end">
            <button
              type="button"
              class="btn btn-danger btn-sm"
              onclick="removeCandidate('${candidateId}')"
            >🗑️</button>
          </div>
        </div>`;

      // Add to DOM
      container.insertAdjacentHTML('beforeend', html);
    }

    // Remove a candidate row
    function removeCandidate(id) {
      const el = document.getElementById(id);
      if (el) el.remove();
    }

    // Initialize the form with one list and one candidate
    addList();
  </script>
</body>

</html>