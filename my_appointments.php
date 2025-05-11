<?php
require "db_connect.php";
session_start();

// Ensure the user is logged in as a patient
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "patient") {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION["user_id"];

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
    <title>My Appointments</title>
    <style>
        body { font-family: Arial, sans-serif; background: url('https://images.pexels.com/photos/3882516/pexels-photo-3882516.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2') no-repeat center center fixed;; padding: 20px; text-align: center; }
        .container { 
            max-width: 800px; 
            margin: auto; 
            background:rgb(179, 196, 180); 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); 
        }
        h2 { color:rgb(27, 29, 27); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background-color:rgb(55, 56, 55); color: white; }
        .status-pending { color: orange; }
        .status-approved { color: green; }
        .status-rejected { color: red; }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            color: white;
            background-color:rgb(144, 179, 145);
            border-radius: 15px;
            transition: background 0.3s, transform 0.2s;
        }
        .btn:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="container">
    <h2>My Appointments</h2>

    <table>
        <tr>
            <th>Doctor</th>
            <th>Problem</th>
            <th>Preferred Time</th>
            <th>Type</th>
            <th>Status</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row["doctor_name"]); ?></td>
                <td><?php echo htmlspecialchars($row["problem_note"]); ?></td>
                <td><?php echo htmlspecialchars($row["preferred_time"]); ?></td>
                <td><?php echo ucfirst($row["consultation_type"]); ?></td>
                <td class="status-<?php echo strtolower($row["status"]); ?>">
                    <?php echo ucfirst($row["status"]); ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a href="patient_home.php" class="btn">Back to Dashboard</a>
</div>

</body>
</html>