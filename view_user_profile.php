<?php
require_once 'db_connect.php';

$id = $_GET['id'];
$role = $_GET['role'];

$table = ($role === 'doctor') ? 'doctors' : 'patients';

$sql = "SELECT * FROM $table WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View User Profile</title>
    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa, #ffffff);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .profile-card {
            background-color: #ffffff;
            border-radius: 15px;
            padding: 30px 40px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .profile-card h2 {
            color: #00796b;
            margin-bottom: 20px;
        }
        .profile-info {
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
        }
        .profile-info span {
            font-weight: bold;
            color: #004d40;
        }
        .back-button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #00796b;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: #004d40;
        }
    </style>
</head>
<body>
<?php
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "<div class='profile-card'>";
    echo "<h2>" . ucfirst($role) . " Profile</h2>";
    echo "<div class='profile-info'><span>Name:</span> " . $user['name'] . "</div>";
    echo "<div class='profile-info'><span>Email:</span> " . $user['email'] . "</div>";
    echo "<div class='profile-info'><span>Age:</span> " . $user['age'] . "</div>";
    // You can add more fields like specialization, blood group etc.
    echo "<button class='back-button' onclick=\"window.history.back()\">Go Back</button>";
    echo "</div>";
} else {
    echo "<div class='profile-card'><h2>User not found.</h2><button class='back-button' onclick=\"window.history.back()\">Go Back</button></div>";
}
$conn->close();
?>
</body>
</html>
