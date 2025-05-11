<?php
session_start();
require "db_connect.php"; // Include DB connection file

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // First: check in patients
    $stmt = $conn->prepare("SELECT id, password, 'patient' AS role FROM patients WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // If not found in patients, check doctors
    if ($result->num_rows === 0) {
        // First check if doctor exists
        $stmt = $conn->prepare("SELECT id, password, status FROM doctors WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if ($row['status'] !== 'approved') {
                $message = "Account pending approval by admin.";
            } elseif (password_verify($password, $row["password"])) {
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["role"] = "doctor";
                header("Location: doctor_dashboard.php");
                exit();
            } else {
                $message = "Invalid email or password!";
            }
        } else {
            // Check admin next if doctor not found
            $stmt = $conn->prepare("SELECT id, password, 'admin' AS role FROM admin WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
        }
    }



    // If not found in doctors, check admins
    if ($result->num_rows === 0) {
        $stmt = $conn->prepare("SELECT id, password, 'admin' AS role FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
    }

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row["password"])) {
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["role"] = $row["role"];

            // Redirect based on role
            if ($row["role"] == "admin") {
                header("Location: admin_dashboard.php");
            } elseif ($row["role"] == "doctor") {
                header("Location: doctor_dashboard.php");
            } else {
                header("Location: patient_home.php");
            }
            exit();
        } else {
            $message = "Invalid email or password!";
        }
    } else {
        $message = "Invalid email or password!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | HealthBridge</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            height: 100vh;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            overflow: hidden;
        }

        .login-container {
            backdrop-filter: blur(15px);
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba( 31, 38, 135, 0.37 );
            padding: 40px 30px;
            width: 350px;
            text-align: center;
            animation: popIn 0.8s ease-out;
        }

        @keyframes popIn {
            0% {
                transform: scale(0.5);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        h2 {
            margin-bottom: 20px;
            font-size: 26px;
            color: #00d2ff;
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0 20px 0;
            border: none;
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 16px;
            transition: 0.3s ease;
        }

        input:focus {
            outline: none;
            box-shadow: 0 0 5px #00d2ff;
            background-color: rgba(255, 255, 255, 0.15);
        }

        button {
            background: linear-gradient(to right, #00d2ff, #3a7bd5);
            border: none;
            padding: 12px 25px;
            color: white;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            transform: scale(1.05);
            background: linear-gradient(to right, #3a7bd5, #00d2ff);
        }

        .link {
            margin-top: 20px;
            display: block;
            color: #aaa;
            font-size: 14px;
            text-decoration: none;
            transition: 0.2s;
        }

        .link:hover {
            color: #00d2ff;
            text-decoration: underline;
        }

        .message {
            color: #ff4b5c;
            font-size: 14px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login to HealthBridge</h2>
    
    <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

    <form action="" method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <a class="link" href="register.php">Don't have an account? Register here</a>
</div>

</body>
</html>


