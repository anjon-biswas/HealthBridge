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

// Decide which table to use
$table = ($role === 'doctor') ? 'doctors' : 'patients';

// Fetch user info
$stmt = $conn->prepare("SELECT name, password FROM $table WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if user exists
if (!$user) {
    $message = "❌ User not found!";
} else {
    $current_hashed_password = $user["password"];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $current_password = $_POST["current_password"];
        $new_password = $_POST["new_password"];
        $confirm_password = $_POST["confirm_password"];

        // Verify current password
        if (!password_verify($current_password, $current_hashed_password)) {
            $message = "❌ Current password is incorrect!";
        } elseif ($new_password !== $confirm_password) {
            $message = "❌ New password and Confirm password do not match!";
        } else {
            // Hash new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password in database
            $update_stmt = $conn->prepare("UPDATE $table SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $hashed_password, $user_id);
            
            if ($update_stmt->execute()) {
                $message = "✅ Password updated successfully!";
            } else {
                $message = "❌ Password update failed!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Change Password</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: url('https://images.unsplash.com/photo-1603398938378-e54eab446dde?q=80&w=2070&auto=format&fit=crop') no-repeat center center fixed; 
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
        input {
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
        .message { color: red; margin-bottom: 10px; }
        .success { color: green; }
        .disabled {
            background-color: #eee;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Change Password</h2>
        <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>
        <form action="" method="POST">
            <input type="text" value="<?= isset($user['name']) ? $user['name'] : ''; ?>" disabled class="disabled">
            <input type="password" name="current_password" placeholder="Current Password" required>
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            <button type="submit">Update Password</button>
        </form>
        <br>
        <a href="<?= ($role === 'doctor') ? 'doctor_dashboard.php' : 'patient_dashboard.php'; ?>">Back to Dashboard</a>
    </div>
</body>
</html>
