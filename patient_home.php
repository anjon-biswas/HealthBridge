<?php
require "db_connect.php";
session_start();

// Ensure the user is logged in and is a patient
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "patient") {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION["user_id"];

// Fetch patient details (if needed)
$stmt = $conn->prepare("SELECT name FROM patients WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if (!$patient) {
    echo "<p style='color:red;'>Error: Patient details not found.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>HealthBridge - Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('https://images.pexels.com/photos/7470817/pexels-photo-7470817.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2') no-repeat center center fixed;
            background-size: cover;
            color: white;
            margin: 0;
            padding: 0;
        }

        .header {
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color:rgb(171, 235, 255);
        }

        .container {
            text-align: center;
            padding: 30px;
            max-width: 800px;
            margin: auto;
        }

        h2 {
            color:rgb(198, 227, 235);
        }

        .btn {
            display: inline-block;
            margin: 10px;
            padding: 12px 20px;
            background: rgb(126, 189, 141);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            transition: 0.3s;
        }

        .btn:hover {
            background: rgb(150, 180, 160);
            transform: scale(1.05);
        }

        .logout-btn {
            background: red;
        }

        .logout-btn:hover {
            background: darkred;
        }

        .footer {
            background: rgba(0, 0, 0, 0.8);
            text-align: center;
            padding: 15px;
            position: fixed;
            bottom: 0;
            width: 100%;
            color: #ccc;
        }
    </style>
</head>
<body>

    <div class="header">
        HealthBridge - Home Page
    </div>

    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($patient['name']); ?>!</h2>
        <p>Navigate through the system to book appointments, check doctor profiles, and manage your health records.</p>

        <a href="list0f_doctors.php" class="btn">View Doctors</a>
        <a href="my_appointments.php" class="btn">My Appointments</a>
        <a href="patient_dashboard.php" class="btn">My Profile</a>
        <a href="logout.php" class="btn logout-btn">Logout</a>
    </div>

    <div class="footer">
        &copy; 2025 Ovi Aria Towheeda | Connecting Patients & Doctors & Donors
    </div>

</body>
</html>
