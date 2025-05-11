<?php
require 'db_connect.php';
session_start();

// Ensure the doctor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header('Location: index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['status']; // "approved" or "rejected"

    // Update the appointment status
    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $appointment_id);

    if ($stmt->execute()) {
        header("Location: view_appointments.php?success=Status updated");
    } else {
        header("Location: view_appointments.php?error=Failed to update");
    }
    exit();
}
?>
