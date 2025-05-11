<?php
session_start();
require "db_connect.php";

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];
$message = "";

// Determine table name based on role
$table = ($role === 'doctor') ? 'doctors' : 'patients';

// Fetch user details from correct table
$stmt = $conn->prepare("SELECT name, email, age, gender, blood_group FROM $table WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $age = $_POST["age"];

    // Update database in the correct table
    $update_stmt = $conn->prepare("UPDATE $table SET email = ?, age = ? WHERE id = ?");
    $update_stmt->bind_param("sii", $email, $age, $user_id);

    if ($update_stmt->execute()) {
        $_SESSION["email"] = $email;
        $message = "Profile updated successfully!";
        $user["email"] = $email;
        $user["age"] = $age;
    } else {
        $message = "Update failed!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('https://images.unsplash.com/photo-1603398938378-e54eab446dde?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fA%3D%3D') no-repeat center center fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            width: 400px;
            text-align: center;
        }
        h2 { color: #333; }
        form { display: flex; flex-direction: column; }
        input, select {
            margin: 10px 0;
            padding: 10px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background: #007bff;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover { background: #0056b3; }
        .message { color: green; margin-bottom: 10px; }
        .disabled {
            background-color: #eee;
            cursor: not-allowed;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update Profile</h2>
        <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>
        <form method="POST">
            <input type="text" value="<?= htmlspecialchars($user['name']); ?>" disabled class="disabled">
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
            <input type="number" name="age" value="<?= htmlspecialchars($user['age']); ?>" required>
            <input type="text" value="<?= ucfirst(htmlspecialchars($user['gender'])); ?>" disabled class="disabled">
            <input type="text" value="<?= strtoupper(htmlspecialchars($user['blood_group'])); ?>" disabled class="disabled">
            <input type="text" value="<?= ucfirst($role); ?>" disabled class="disabled">
            <button type="submit">Update Profile</button>
        </form>
        <br>
        <a href="<?= $role === 'doctor' ? 'doctor_dashboard.php' : 'patient_dashboard.php'; ?>">Back to Dashboard</a>
    </div>
</body>
</html>
