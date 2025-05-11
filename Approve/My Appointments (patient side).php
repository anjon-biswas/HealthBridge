<?php
require 'db_connect.php';
session_start();

// Ensure the user is logged in as a patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header('Location: index.php');
    exit();
}

$patient_id = $_SESSION['user_id'];

// Fetch the patient's appointments
$stmt = $conn->prepare("SELECT a.id, d.name AS doctor_name, a.problem_note, a.preferred_time, a.consultation_type, a.status 
                        FROM appointments a 
                        JOIN doctors d ON a.doctor_id = d.id 
                        WHERE a.patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - Patient</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #4CAF50;
        }
        .appointment {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>My Appointments</h2>

    <?php while ($row = $result->fetch_assoc()) { ?>
        <div class="appointment">
            <p><strong>Doctor:</strong> <?php echo htmlspecialchars($row['doctor_name']); ?></p>
            <p><strong>Problem:</strong> <?php echo htmlspecialchars($row['problem_note']); ?></p>
            <p><strong>Preferred Time:</strong> <?php echo htmlspecialchars($row['preferred_time']); ?></p>
            <p><strong>Consultation Type:</strong> <?php echo htmlspecialchars($row['consultation_type']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
        </div>
    <?php } ?>

</div>

</body>
</html>
