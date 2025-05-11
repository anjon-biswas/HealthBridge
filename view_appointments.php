<?php
require 'db_connect.php';
session_start();

// Ensure the user is logged in as a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header('Location: index.php');
    exit();
}

$doctor_id = $_SESSION['user_id'];

// Fetch pending appointments for the doctor
$stmt = $conn->prepare("SELECT a.id, p.name AS patient_name, a.problem_note, a.preferred_time, a.consultation_type, a.status
                        FROM appointments a
                        JOIN patients p ON a.patient_id = p.id
                        WHERE a.doctor_id = ?
                        ORDER BY a.preferred_time DESC");

$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Appointments</title>
    <style>
        body { font-family: Arial, sans-serif; background: url('https://images.pexels.com/photos/3882516/pexels-photo-3882516.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2') no-repeat center center fixed; padding: 20px; }
        .container { width: 70%; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #333; color: white; }
        .btn { padding: 5px 10px; border: none; cursor: pointer; }
        .btn-approve { background: green; color: white; }
        .btn-reject { background: red; color: white; }
    </style>
</head>
<body>

<div class="container">
    <h2>View Appointments</h2>

    <table>
        <tr>
            <th>Patient</th>
            <th>Problem</th>
            <th>Preferred Time</th>
            <th>Type</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                <td><?php echo htmlspecialchars($row['problem_note']); ?></td>
                <td><?php echo htmlspecialchars($row['preferred_time']); ?></td>
                <td><?php echo ucfirst($row['consultation_type']); ?></td>
                <td><?php echo ucfirst($row['status']); ?></td>
                <td>
                    <?php if ($row['status'] == 'pending') { ?>
                        <form action="update_appointment_status.php" method="POST" style="display:inline;">
                            <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="status" value="approved" class="btn btn-approve">Approve</button>
                            <button type="submit" name="status" value="rejected" class="btn btn-reject">Reject</button>
                        </form>
                    <?php } else { echo "Updated"; } ?>
                </td>
            </tr>
        <?php } ?>
    </table>

    <br><a href="doctor_dashboard.php">Back to Dashboard</a>
</div>

</body>
</html>
