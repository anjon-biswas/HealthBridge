<?php
require 'db_connect.php';
session_start();

// Ensure the user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header('Location: index.php');
    exit();
}

// Get appointment details and update status
if (isset($_GET['appointment_id']) && isset($_GET['status'])) {
    $appointment_id = $_GET['appointment_id'];
    $status = $_GET['status'];

    // Update appointment status in the database
    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $appointment_id);
    $stmt->execute();

    // Redirect back to the doctor dashboard
    header('Location: doctor_dashboard.php');
    exit();
} else {
    echo "<p style='color:red;'>Invalid request.</p>";
    exit();
}
