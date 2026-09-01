<?php
require_once 'connection.php';

if (
    isset($_POST["name"]) && !empty(trim($_POST["name"])) &&
    isset($_POST["email"]) && !empty($_POST["email"]) &&
    isset($_POST["pass"]) && !empty($_POST["pass"])
) {
    $name  = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $pass  = password_hash($_POST["pass"], PASSWORD_DEFAULT);

    // ✅ Validate and upload profile picture
    if (!isset($_FILES["profilepic"]) || $_FILES["profilepic"]["error"] !== 0) {
        header("Location: register.php?error=missing_profile_pic");
        exit();
    }

    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $extension      = pathinfo($_FILES["profilepic"]["name"], PATHINFO_EXTENSION);
    $profilePicName = time() . "_" . uniqid() . "." . $extension;
    $uploadPath     = $targetDir . $profilePicName;

    move_uploaded_file($_FILES["profilepic"]["tmp_name"], $uploadPath);

    // 🔍 OCR processing (Arabic + English)
    $tesseractPath = 'C:\Program Files\Tesseract-OCR\tesseract.exe'; // update if needed
    $outputFile    = tempnam(sys_get_temp_dir(), 'ocr_output');

    $command = "\"$tesseractPath\" \"$uploadPath\" \"$outputFile\" -l eng+ara 2>&1";
    exec($command, $ocrOutput, $statusCode);

    $ocrText = "";
    if ($statusCode === 0 && file_exists($outputFile . ".txt")) {
        $ocrText = file_get_contents($outputFile . ".txt");
        unlink($outputFile . ".txt");
    }

    // 🔢 Extract only digits from OCR result
    if (preg_match('/\d+/', $ocrText, $matches)) {
        $ocrDigits = $matches[0];      // first full number sequence
    } else {
        // or, to concat all digits into one string:
        // $ocrDigits = preg_replace('/\D/', '', $ocrText);
        $ocrDigits = '';
    }

    // ❌ Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        header("Location: register.php?error=email_exists");
        exit();
    }
    $stmt->close();

    // ❌ Check if same OCR digits already used
    $escapedOCR = addslashes($ocrDigits);
    $stmt = $conn->prepare("SELECT id FROM user WHERE ocr_result = ?");
    $stmt->bind_param("s", $escapedOCR);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "<script>
                alert('⚠️ This ID number has already been used. Please use a different one.');
                window.location.href='register.php';
              </script>";
        unlink($uploadPath);
        exit();
    }
    $stmt->close();

    // ✅ Save new user
    $stmt = $conn->prepare("
        INSERT INTO user (name, email, password, profile_pic, ocr_result, role_id)
        VALUES (?, ?, ?, ?, ?, 1)
    ");
    $stmt->bind_param("sssss", $name, $email, $pass, $profilePicName, $escapedOCR);
    $stmt->execute();
    $stmt->close();

    header("Location: login1.php?success=registered");
    exit();
} else {
    header("Location: register.php?error=missing_fields");
    exit();
}
