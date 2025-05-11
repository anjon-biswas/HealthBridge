<?php
require "db_connect.php";
session_start();

// Ensure the user is logged in and is a patient
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "patient") {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch patient details (Fix: Ensure correct table & data retrieval)
$stmt = $conn->prepare("SELECT name, email, age, gender, blood_group FROM patients WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle case if patient details are not found
if (!$user) {
    echo "<p style='color:red;'>Error: Patient details not found.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Patient Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('https://images.unsplash.com/photo-1603398938378-e54eab446dde?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fA%3D%3D') no-repeat center center fixed;
            background-size: cover;
            text-align: center;
            padding: 20px;
            color: white;
        }

        /* Button Styling */
        .top-right-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-fixed {
            padding: 12px 18px;
            font-weight: bold;
            font-size: 16px;
            text-decoration: none;
            border-radius: 5px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            transition: background-color 0.3s, transform 0.2s;
        }

        .donor-btn {
            background-color: #e63946; /* Red */
            color: white;
        }

        .donor-btn:hover {
            background-color: #d62828; /* Darker Red */
            transform: scale(1.05);
        }

        .home-btn {
            background-color: rgb(134, 235, 140);
            color: black;
        }

        .home-btn:hover {
            background-color: rgba(3, 84, 18, 0.3);
            transform: scale(1.05);
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

    <!-- Buttons positioned at the top right -->
    <div class="top-right-buttons">
        <a href="become_blood_donor.php" class="btn-fixed donor-btn">Become a Blood Donor</a>
        <a href="patient_home.php" class="btn-fixed home-btn">Back to Dashboard</a>
    </div>

    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
        
        <h3>Your Profile Details</h3>
        <table>
            <tr><th>Name</th><td><?php echo htmlspecialchars($user['name']); ?></td></tr>
            <tr><th>Email</th><td><?php echo htmlspecialchars($user['email']); ?></td></tr>
            <tr><th>Age</th><td><?php echo htmlspecialchars($user['age']); ?></td></tr>
            <tr><th>Gender</th><td><?php echo htmlspecialchars($user['gender']); ?></td></tr>
            <tr><th>Blood Group</th><td><?php echo htmlspecialchars($user['blood_group']); ?></td></tr>
        </table>

        <h3>Actions</h3>
        <a href="update_profile.php?user_id=<?php echo $user_id; ?>" class="btn">Update Profile</a>
        <a href="change_password.php?user_id=<?php echo $user_id; ?>" class="btn">Change Password</a>
        <a href="add_document.php?user_id=<?php echo $user_id; ?>" class="btn">Add Medical Documents</a>

        <h3>Medical Documents</h3>
        <table>
            <tr>
                <th>Document Name</th>
                <th>View</th>
            </tr>
            <?php
            $stmt = $conn->prepare("SELECT file_name, file_path FROM medical_documents WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>".htmlspecialchars($row['file_name'])."</td>
                            <td><a href='".htmlspecialchars($row['file_path'])."' target='_blank'>View</a></td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No documents uploaded yet.</td></tr>";
            }
            ?>
        </table>

        <br>
        <a href="logout.php" class="btn logout-btn">Logout</a>
    </div>

</body>
</html>

