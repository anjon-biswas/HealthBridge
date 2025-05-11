<?php
require "db_connect.php";
session_start();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // secure hash
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $blood_group = $_POST['blood_group'];
    $specialization = $_POST['specialization'];
    $degrees = $_POST['degrees'];

    // Handle profile picture upload
    $profile_picture = $_FILES['profile_picture'];
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir); // Create if not exists
    }
    $file_name = uniqid() . "_" . basename($profile_picture["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($profile_picture["tmp_name"], $target_file)) {
        // Insert into database
        $sql = "INSERT INTO doctors (name, email, password, age, gender, blood_group, specialization, degrees, profile_picture)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisssss", $name, $email, $password, $age, $gender, $blood_group, $specialization, $degrees, $file_name);

        if ($stmt->execute()) {
            echo "<script>alert('Doctor added successfully!'); window.location.href = 'admin_dashboard.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Error uploading image.";
    }

    $conn->close();
} else {
    echo "Invalid request.";
}
?>
