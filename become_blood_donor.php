<?php
session_start();

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("Location: index.php");
    exit();
}

require "db_connect.php";

$user_id = $_SESSION["user_id"];
$user_role = $_SESSION["role"];

// Fetch user basic info from relevant table
$table = ($user_role === "doctor") ? "doctors" : "patients";
$stmt = $conn->prepare("SELECT name, age FROM $table WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if already registered
$check = $conn->prepare("SELECT donor_id FROM blood_donors WHERE user_id = ?");
$check->bind_param("i", $user_id);
$check->execute();
$checkResult = $check->get_result();

if ($checkResult->num_rows > 0) {
    $already_registered = true;
} else {
    $already_registered = false;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && !$already_registered) {
    $blood_group = $_POST["blood_group"];
    $gender = $_POST["gender"];
    $availability = $_POST["availability"];
    $contact_number = $_POST["contact_number"];
    $address = $_POST["address"];

    $stmt = $conn->prepare("INSERT INTO blood_donors (user_id, user_role, name, blood_group, age, gender, availability, contact_number, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssissss", $user_id, $user_role, $user['name'], $blood_group, $user['age'], $gender, $availability, $contact_number, $address);

    if ($stmt->execute()) {
        $success = "Thank you for registering as a blood donor!";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Become a Blood Donor</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #627679, #2f2f2f);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .form-container {
            background: #bed0d3;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 500px;
        }

        h2 {
            text-align: center;
            color: #0b5149;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin: 15px 0 5px;
            color: #37474f;
        }

        input[type="text"],
        input[type="number"],
        input[type="tel"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #b0bec5;
            border-radius: 6px;
            background: #f9f9f9;
            font-size: 14px;
        }

        button {
            margin-top: 20px;
            width: 100%;
            background-color: #00796b;
            color: white;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #004d40;
        }

        .success {
            background-color: #c8e6c9;
            color: #256029;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .error {
            background-color: #ffcdd2;
            color: #b71c1c;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .notice {
            background-color: #a4a39c;
            color: #2c0e03;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Become a Blood Donor</h2>

        <?php if (isset($success)): ?>
            <div class="success"><?= $success ?></div>
        <?php elseif (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php elseif ($already_registered): ?>
            <div class="notice">You are already registered as a blood donor.</div>
        <?php else: ?>
            <form method="POST">
                <label>Blood Group:</label>
                <input type="text" name="blood_group" required placeholder="e.g. A+, B-, O+">

                <label>Gender:</label>
                <select name="gender" required>
                    <option value="">Select</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>

                <label>Availability:</label>
                <select name="availability" required>
                    <option value="Available">Available</option>
                    <option value="Not Available">Not Available</option>
                </select>

                <label>Contact Number:</label>
                <input type="tel" name="contact_number" required placeholder="e.g. 0123456789">

                <label>Address:</label>
                <textarea name="address" required rows="3" placeholder="Enter full address"></textarea>

                <button type="submit">Register as Donor</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
