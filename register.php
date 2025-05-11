<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = $_POST["role"];
    $age = $_POST["age"];
    $gender = $_POST["gender"];
    $blood_group = $_POST["blood_group"];
    $profile_picture = "";
    
    // Check if email already exists
    $checkPatient = $conn->prepare("SELECT email FROM patients WHERE email = ?");
    $checkPatient->bind_param("s", $email);
    $checkPatient->execute();
    $checkPatient->store_result();

    $checkDoctor = $conn->prepare("SELECT email FROM doctors WHERE email = ?");
    $checkDoctor->bind_param("s", $email);
    $checkDoctor->execute();
    $checkDoctor->store_result();

    if ($checkPatient->num_rows > 0 || $checkDoctor->num_rows > 0) {
        $message = "Email already registered!";
    } else {
        if ($role === 'doctor') {
            // Handle doctor-specific fields
            $specialization = $_POST["specialization"];
            $degrees = $_POST["degrees"];

            // Handle file upload
            if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
                $profile_picture = $_FILES["profile_picture"]["name"];
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($profile_picture);
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
                if ($check === false) {
                    $message = "File is not an image.";
                    $uploadOk = 0;
                }

                if ($_FILES["profile_picture"]["size"] > 5000000) {
                    $message = "File too large.";
                    $uploadOk = 0;
                }

                if (!in_array($imageFileType, ["jpg", "jpeg", "png"])) {
                    $message = "Only JPG, JPEG, PNG files allowed.";
                    $uploadOk = 0;
                }

                if ($uploadOk && move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                    // Insert into doctor_requests
                    $stmt = $conn->prepare("INSERT INTO doctor_requests (name, email, password, age, gender, blood_group, specialization, degrees, profile_picture, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
                    $stmt->bind_param("sssssssss", $name, $email, $password, $age, $gender, $blood_group, $specialization, $degrees, $target_file);
                    if ($stmt->execute()) {
                        $message = "Your registration request has been sent to the admin.";
                    } else {
                        $message = "Failed to register doctor.";
                    }
                } else {
                    $message = "Profile picture upload failed.";
                }
            } else {
                $message = "Profile picture is required for doctors.";
            }
        } else {
            // Insert into patients table (no profile picture)
            $stmt = $conn->prepare("INSERT INTO patients (name, email, password, age, gender, blood_group, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $name, $email, $password, $age, $gender, $blood_group, $role);
            if ($stmt->execute()) {
                header("Location: index.php?success=1");
                exit();
            } else {
                $message = "Failed to register patient.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register | HealthBridge</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('https://images.pexels.com/photos/3882516/pexels-photo-3882516.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2') no-repeat center center fixed;
            background-size: cover;
            padding: 50px;
            text-align: center;
        }
        form {
            background: #697475;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            width: 350px;
            margin: auto;
        }
        input, select {
            display: block;
            margin: 10px auto;
            padding: 10px;
            width: 90%;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        a {
            display: block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }
        h2 {
            color: aliceblue;
        }
    </style>
</head>
<body>
    <h2>Register</h2>
    <?php if (isset($message)) echo "<p style='color:red;'>$message</p>"; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="number" name="age" placeholder="Age" required>

        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <select name="blood_group" required>
            <option value="">Select Blood Group</option>
            <option value="A+">A+</option>
            <option value="A-">A-</option>
            <option value="B+">B+</option>
            <option value="B-">B-</option>
            <option value="O+">O+</option>
            <option value="O-">O-</option>
            <option value="AB+">AB+</option>
            <option value="AB-">AB-</option>
        </select>

        <select name="role" id="role" required>
            <option value="patient">Patient</option>
            <option value="doctor">Doctor</option>
        </select>

        <div id="doctorFields" style="display: none;">
            <input type="text" name="specialization" placeholder="Specialization">
            <input type="text" name="degrees" placeholder="Degrees">
            <input type="file" name="profile_picture">
        </div>

        <button type="submit">Register</button>
    </form>

    <a href="index.php">Already have an account? Login</a>

    <script>
        document.getElementById("role").addEventListener("change", function () {
            var doctorFields = document.getElementById("doctorFields");
            if (this.value === "doctor") {
                doctorFields.style.display = "block";
                doctorFields.querySelectorAll("input").forEach(input => input.required = true);
            } else {
                doctorFields.style.display = "none";
                doctorFields.querySelectorAll("input").forEach(input => input.required = false);
            }
        });
    </script>
</body>
</html>
