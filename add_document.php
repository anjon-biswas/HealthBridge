<?php
require "db_connect.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$message = "";

// Check if the user_id exists in the patients table
$stmt_check = $conn->prepare("SELECT id FROM patients WHERE id = ?");
$stmt_check->bind_param("i", $user_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows == 0) {
    $message = "User not found in the database.";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $target_dir = "uploads/"; // Folder where files will be stored
    $file_name = basename($_FILES["document"]["name"]);
    $target_file = $target_dir . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Allowed file types
    $allowed_types = ["pdf", "jpg", "png", "jpeg"];

    if (!in_array($file_type, $allowed_types)) {
        $message = "Only PDF, JPG, JPEG, and PNG files are allowed.";
    } elseif (move_uploaded_file($_FILES["document"]["tmp_name"], $target_file)) {
        // Save file path in database
        $stmt = $conn->prepare("INSERT INTO medical_documents (id, file_name, file_path) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $file_name, $target_file);
        
        if ($stmt->execute()) {
            $message = "File uploaded successfully!";
        } else {
            $message = "Database error: Unable to save file.";
        }
    } else {
        $message = "Error uploading file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Upload Medical Document</title>
    <style>
        /* Full-page background */
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background: url('https://images.unsplash.com/photo-1603398938378-e54eab446dde?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fA%3D%3D') no-repeat center center fixed;
            background-size: cover;
            padding: 50px;
            color: white;
        }

        /* Centered form container */
        .container {
            background: rgba(0, 0, 0, 0.7); /* Semi-transparent black */
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            margin: auto;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }

        h2 {
            color: #5bc0de;
        }

        input, button {
            display: block;
            margin: 15px auto;
            padding: 12px;
            width: 85%;
            border-radius: 8px;
            border: none;
            font-size: 16px;
        }

        input {
            background: white;
            color: black;
        }

        button {
            background:rgb(186, 221, 194);
            color: white;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        button:hover {
            background:rgb(164, 212, 175);
            transform: scale(1.05);
        }

        a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            display: block;
            margin-top: 15px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Upload Medical Document</h2>
        <?php if ($message) echo "<p>$message</p>"; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="file" name="document" required>
            <button type="submit">Upload</button>
        </form>
        <a href="patient_dashboard.php">Back to Profile</a>
    </div>

</body>
</html>
