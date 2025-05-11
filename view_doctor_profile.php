<?php
require 'db_connect.php';
session_start();

// Ensure the user is logged in and is a patient
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
$stmt = $conn->prepare("SELECT id, name, email, age, gender, blood_group, specialization, degrees, profile_picture FROM doctors WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// Check if doctor exists
if (!$doctor) {
    echo "<p style='color:red;'>Doctor not found.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('https://images.pexels.com/photos/7470817/pexels-photo-7470817.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2') no-repeat center center fixed;
            padding: 20px;
        }
        .container {
            width: 60%;
            margin: 0 auto;
            background-color:rgba(212, 223, 225, 0.92);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 4px solid #0c4139;
        }
        h2 {
            color:rgb(31, 31, 30);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: middle;
            border-bottom: 1px solid #5e535385;
        }
        th {
            background-color: #093336;
            color: white;
        }
        .btn-container {
            margin-top: 30px;
            display: flex;
            justify-content: space-around;
        }
        .btn {
            background-color:rgb(79, 139, 81);
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
            display: inline-block;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <img src="<?php echo htmlspecialchars($doctor['profile_picture'] ?? 'default.jpg'); ?>" alt="Doctor Profile Picture" class="profile-img">
    
    <h2>Dr. <?php echo htmlspecialchars($doctor['name']); ?>'s Profile</h2>

    <table>
        <tr><th>Name</th><td><?php echo htmlspecialchars($doctor['name']); ?></td></tr>
        <tr><th>Email</th><td><?php echo htmlspecialchars($doctor['email']); ?></td></tr>
        <tr><th>Age</th><td><?php echo htmlspecialchars($doctor['age']); ?></td></tr>
        <tr><th>Gender</th><td><?php echo htmlspecialchars($doctor['gender']); ?></td></tr>
        <tr><th>Blood Group</th><td><?php echo htmlspecialchars($doctor['blood_group']); ?></td></tr>
        <tr><th>Specialization</th><td><?php echo htmlspecialchars($doctor['specialization']); ?></td></tr>
        <tr><th>Degrees</th><td><?php echo htmlspecialchars($doctor['degrees']); ?></td></tr>
    </table>

    <div class="btn-container">
        <a href="list0f_doctors.php" class="btn">Back to Doctors List</a>
        <a href="appointment_form.php?doctor_id=<?php echo $doctor['id']; ?>" class="btn">Request Appointment</a>

    </div>
</div>

</body>
</html>
