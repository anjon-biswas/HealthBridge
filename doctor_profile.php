<?php
require "db_connect.php"; // Ensure DB connection
session_start();

// Check if doctor ID is provided
if (!isset($_GET['id'])) {
    echo "<p style='color:red;'>Doctor not found.</p>";
    exit();
}

$doctor_id = $_GET['id'];

// Fetch doctor details
$stmt = $conn->prepare("SELECT name, email, specialization, degrees FROM doctors WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

if (!$doctor) {
    echo "<p style='color:red;'>Doctor not found.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Doctor Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('https://images.pexels.com/photos/3882516/pexels-photo-3882516.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2') no-repeat center center fixed;
            background-size: cover;
            text-align: center;
            padding: 20px;
            color: white;
        }
        .container {
            background: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            margin: auto;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }
        h2 {
            color: #5bc0de;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background: rgb(164, 187, 212);
            color: white;
        }
        td {
            color: black;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Doctor Profile</h2>
        <table>
            <tr><th>Name</th><td><?php echo htmlspecialchars($doctor['name']); ?></td></tr>
            <tr><th>Email</th><td><?php echo htmlspecialchars($doctor['email']); ?></td></tr>
            <tr><th>Specialization</th><td><?php echo htmlspecialchars($doctor['specialization']); ?></td></tr>
            <tr><th>Degrees</th><td><?php echo htmlspecialchars($doctor['degrees']); ?></td></tr>
        </table>
        <br>
        <a href="patient_home.php" class="btn">Back to Home</a>
    </div>

</body>
</html>