<?php
require "db_connect.php";
session_start();

// Ensure the user is logged in and is a doctor
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "doctor") {
    header("Location: index.php");
    exit();
}

$doctor_id = $_SESSION["user_id"];

// Fetch doctor details
$stmt = $conn->prepare("SELECT name, email, age, gender, blood_group FROM doctors WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// Handle case if doctor details are not found
if (!$doctor) {
    echo "<p style='color:red;'>Error: Doctor details not found.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Sticky Become a Blood Donor Button -->
    <a href="become_blood_donor.php" class="donor-btn">Become a Blood Donor</a>
    
    <style>
        .donor-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #e63946; /* Red color */
            color: white;
            font-weight: bold;
            font-size: 16px;
            text-decoration: none;
            border-radius: 5px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            transition: background-color 0.3s;
        }
    
        .donor-btn:hover {
            background-color: #d62828; /* Darker red on hover */
        }
    </style>

    <title>Doctor Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('https://images.unsplash.com/photo-1603398938378-e54eab446dde?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fA%3D%3D') no-repeat center center fixed;
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

        .btn {
            display: block;
            margin: 10px auto;
            padding: 12px;
            width: 85%;
            text-align: center;
            background: rgb(174, 204, 181);
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
    </style>
</head>
<body>

    <div class="container">
        <h2>Welcome, Dr. <?php echo htmlspecialchars($doctor['name']); ?>!</h2>
        
        <h3>Your Profile Details</h3>
        <table>
            <tr><th>Name</th><td><?php echo htmlspecialchars($doctor['name']); ?></td></tr>
            <tr><th>Email</th><td><?php echo htmlspecialchars($doctor['email']); ?></td></tr>
            <tr><th>Age</th><td><?php echo htmlspecialchars($doctor['age']); ?></td></tr>
            <tr><th>Gender</th><td><?php echo htmlspecialchars($doctor['gender']); ?></td></tr>
            <tr><th>Blood Group</th><td><?php echo htmlspecialchars($doctor['blood_group']); ?></td></tr>
        </table>

        <h3>Actions</h3>
        <!-- Fix: Pass session ID to forms -->
        <a href="update_profile.php?doctor_id=<?php echo $doctor_id; ?>" class="btn">Update Profile</a>
        <a href="change_password.php?doctor_id=<?php echo $doctor_id; ?>" class="btn">Change Password</a>

        <h3>Your Appointments</h3>
        <a href="view_appointments.php?doctor_id=<?php echo $doctor_id; ?>" class="btn">View Appointments</a>

        <br>
        <a href="index.php" class="btn logout-btn">Logout</a>
    </div>

</body>
</html>
