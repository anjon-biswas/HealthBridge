<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit();
}
require_once "db_connect.php";

// Handle Approve/Reject actions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $request_id = $_POST["request_id"];
    $action = $_POST["action"];

    if ($action === "approve") {
        $stmt = $conn->prepare("SELECT * FROM doctor_requests WHERE id = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $insert = $conn->prepare("INSERT INTO doctors (name, email, password, age, gender, blood_group, specialization, degrees, profile_picture, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ? , 'approved' )");
            $insert->bind_param("sssisssss", $row["name"], $row["email"], $row["password"], $row["age"], $row["gender"], $row["blood_group"], $row["specialization"], $row["degrees"], $row["profile_picture"]);
            $insert->execute();
        }
        $update = $conn->prepare("UPDATE doctor_requests SET status = 'approved' WHERE id = ?");
        $update->bind_param("i", $request_id);
        $update->execute();
    } elseif ($action === "reject") {
        $update = $conn->prepare("UPDATE doctor_requests SET status = 'rejected' WHERE id = ?");
        $update->bind_param("i", $request_id);
        $update->execute();
    }

    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HealthBridge Admin Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            background-color: #2c3e50;
            position: fixed;
            top: 0; left: 0;
            padding-top: 30px;
        }
        .sidebar h2 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar button {
            display: block;
            width: 80%;
            margin: 15px auto;
            padding: 12px;
            font-size: 16px;
            border: none;
            background-color: #1abc9c;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .sidebar button:hover {
            background-color: #16a085;
        }
        .sidebar .logout-btn {
            background-color: #e74c3c;
        }
        .main-content {
            margin-left: 250px;
            padding: 40px;
        }
        .section {
            display: none;
        }
        .section.active {
            display: block;
        }
        h1 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background: #1abc9c;
            color: white;
        }
        .action-btn {
            padding: 8px 14px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }
        .approve-btn {
            background-color: #2ecc71;
        }
        .reject-btn {
            background-color: #e74c3c;
        }
        .link-btn {
            color: #2980b9;
            text-decoration: none;
            margin-right: 10px;
        }
        .link-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Dashboard</h2>
    <button onclick="showSection('view-doctor-requests')">View Doctor Requests</button>
    <button onclick="showSection('view-users')">View Users</button>
    <form action="logout.php" method="post" style="text-align:center;">
        <button type="submit" class="logout-btn" style="margin-top: 30px;">Logout</button>
    </form>
</div>

<div class="main-content">
    <!-- Doctor Requests Section -->
    <div class="section active" id="view-doctor-requests">
        <h1>Pending Doctor Requests</h1>
        <?php
        $result = $conn->query("SELECT * FROM doctor_requests WHERE status = 'pending'");
        if ($result->num_rows > 0) {
            echo "<table>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Blood Group</th>
                        <th>Specialization</th>
                        <th>Degrees</th>
                        <th>Actions</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['age']}</td>
                        <td>{$row['gender']}</td>
                        <td>{$row['blood_group']}</td>
                        <td>{$row['specialization']}</td>
                        <td>{$row['degrees']}</td>
                        <td>
                            <form method='POST' style='display:inline-block;'>
                                <input type='hidden' name='request_id' value='{$row['id']}'>
                                <input type='hidden' name='action' value='approve'>
                                <button type='submit' class='action-btn approve-btn'>Approve</button>
                            </form>
                            <form method='POST' style='display:inline-block; margin-left:5px;'>
                                <input type='hidden' name='request_id' value='{$row['id']}'>
                                <input type='hidden' name='action' value='reject'>
                                <button type='submit' class='action-btn reject-btn'>Reject</button>
                            </form>
                        </td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No pending requests.</p>";
        }
        ?>
    </div>

    <!-- All Users Section -->
    <div class="section" id="view-users">
        <h1>All Users</h1>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php
            // Fetch doctors
            $doctor_sql = "SELECT id, name, email FROM doctors";
            $doctor_result = $conn->query($doctor_sql);
            if ($doctor_result->num_rows > 0) {
                while ($row = $doctor_result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['name']}</td>
                            <td>{$row['email']}</td>
                            <td>Doctor</td>
                            <td>
                                <a class='link-btn' href='view_user_profile.php?id={$row['id']}&role=doctor'>View</a> 
                                <a class='link-btn' href='delete_user.php?id={$row['id']}&role=doctor' onclick=\"return confirm('Are you sure you want to delete this user?')\">Delete</a>
                            </td>
                          </tr>";
                }
            }

            // Fetch patients
            $patient_sql = "SELECT id, name, email FROM patients";
            $patient_result = $conn->query($patient_sql);
            if ($patient_result->num_rows > 0) {
                while ($row = $patient_result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['name']}</td>
                            <td>{$row['email']}</td>
                            <td>Patient</td>
                            <td>
                                <a class='link-btn' href='view_user_profile.php?id={$row['id']}&role=patient'>View</a> 
                                <a class='link-btn' href='delete_user.php?id={$row['id']}&role=patient' onclick=\"return confirm('Are you sure you want to delete this user?')\">Delete</a>
                            </td>
                          </tr>";
                }
            }
            ?>
        </table>
    </div>
</div>

<script>
    function showSection(id) {
        document.querySelectorAll('.section').forEach(sec => sec.classList.remove('active'));
        document.getElementById(id).classList.add('active');
    }
</script>

</body>
</html>
