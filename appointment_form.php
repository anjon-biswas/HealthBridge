<?php
require 'db_connect.php';
session_start();

// Ensure the user is logged in as a patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header('Location: index.php');
    exit();
}

// Check if doctor_id is provided in the URL
if (!isset($_GET['doctor_id']) || !is_numeric($_GET['doctor_id'])) {
    echo "<p style='color:red;'>Error: Invalid doctor ID.</p>";
    exit();
}

$doctor_id = $_GET['doctor_id'];

// Fetch doctor details from the database
$stmt = $conn->prepare("SELECT id, name FROM doctors WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// Check if doctor exists
if (!$doctor) {
    echo "<p style='color:red;'>Doctor not found.</p>";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $problem_note = $_POST['problem_note'];
    $preferred_time = $_POST['preferred_time'];
    $consultation_type = $_POST['consultation_type'];
    $patient_id = $_SESSION['user_id']; // The logged-in patient's ID

    // Insert appointment request into the database
    $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, problem_note, preferred_time, consultation_type, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("iisss", $patient_id, $doctor_id, $problem_note, $preferred_time, $consultation_type);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>Appointment request submitted successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error submitting appointment request.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Appointment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('https://images.pexels.com/photos/3882516/pexels-photo-3882516.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2') no-repeat center center fixed;
            padding: 20px;
        }
        .container {
            width: 60%;
            margin: 0 auto;
            background-color:rgb(149, 152, 149);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color:rgb(35, 36, 35);
        }
        form {
            margin-top: 20px;
        }
        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .btn {
            background-color:rgb(74, 135, 75);
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 10px;
            transition: 0.3s;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Request Appointment with Dr. <?php echo htmlspecialchars($doctor['name']); ?></h2>

    <form action="appointment_form.php?doctor_id=<?php echo $doctor_id; ?>" method="POST">
        <label for="problem_note">Problem Description</label>
        <textarea id="problem_note" name="problem_note" required></textarea>

        <label for="preferred_time">Preferred Time</label>
        <input type="datetime-local" id="preferred_time" name="preferred_time" required>

        <label for="consultation_type">Consultation Type</label>
        <select id="consultation_type" name="consultation_type" required>
            <option value="online">Online</option>
            <option value="offline">Offline</option>
        </select>

        <button type="submit" class="btn">Submit Appointment Request</button>
    </form>

    <br>
    <a href="view_doctor_profile.php?doctor_id=<?php echo $doctor_id; ?>" class="btn">Back to Doctor Profile</a>
</div>

</body>
</html>
