<?php
require 'db_connect.php';
session_start();

// Ensure the user is logged in as a patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header('Location: index.php');
    exit();
}

// Fetch all doctors from the database
$stmt = $conn->prepare("SELECT id, name, specialization, profile_picture FROM doctors");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors List</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
    
        body {
            font-family: 'Poppins', sans-serif;
            background: url('https://images.pexels.com/photos/7470817/pexels-photo-7470817.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            color: #333;
        }
    
        .header {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 15px 30px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        }
    
        .header .btn {
            margin-left: 15px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #4CAF50, #2e7d32);
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 30px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
    
        .header .btn:hover {
            background: linear-gradient(135deg, #388e3c, #1b5e20);
            transform: translateY(-2px);
        }
    
        .container {
            width: 95%;
            max-width: 1400px;
            margin: 40px auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
        }
    
        .doctor-card {
            background: rgb(161 181 188 / 85%);
            border-radius: 16px;
            width: 280px;
            padding: 25px;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            backdrop-filter: blur(8px);
        }
    
        .doctor-card:hover {
            transform: scale(1.03);
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25);
        }
    
        .doctor-card img {
            width: 110px;
            height: 110px;
            border-radius: 40%;
            object-fit: cover;
            border: 4px solidrgb(6, 35, 41);
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
    
        .doctor-card h3 {
            margin: 10px 0 5px;
            font-size: 1.3rem;
            font-weight: 600;
            color: #222;
        }
    
        .doctor-card p {
            font-size: 0.95rem;
            color: #555;
            margin-bottom: 15px;
        }
    
        .doctor-card .btn {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 18px;
            font-weight: 500;
            text-decoration: none;
            border-radius: 25px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: inline-block;
        }
    
        .doctor-card .btn:hover {
            background-color: #2e7d32;
            transform: scale(1.05);
        }
    
        @media screen and (max-width: 600px) {
            .container {
                flex-direction: column;
                align-items: center;
            }
    
            .doctor-card {
                width: 90%;
            }
    
            .header {
                flex-direction: column;
                align-items: flex-end;
            }
    
            .header .btn {
                margin: 10px 0;
            }
        }
    </style>


</head>
<body>

<!-- Header Section -->
<div class="header">
    <a href="patient_home.php" class="btn">Back to Home</a>
    <a href="become_blood_donor.php" class="btn">Become a Blood Donor</a>
</div>

<!-- Doctors List -->
<div class="container">
    <?php
    if ($result->num_rows > 0) {
        while ($doctor = $result->fetch_assoc()) {
            $doctor_url = "view_doctor_profile.php?doctor_id=" . $doctor['id'];
            echo "<div class='doctor-card'>";
            echo "<img src='" . htmlspecialchars($doctor['profile_picture'] ?? 'default.jpg') . "' alt='Doctor Profile Picture'>";
            echo "<h3>" . htmlspecialchars($doctor['name']) . "</h3>";
            echo "<p>Specialization: " . htmlspecialchars($doctor['specialization']) . "</p>";
            echo "<a href='$doctor_url' class='btn'>View Details</a>";
            echo "</div>";
        }
    } else {
        echo "<p style='color: white;'>No doctors available at the moment.</p>";
    }
    ?>
</div>

</body>
</html>
