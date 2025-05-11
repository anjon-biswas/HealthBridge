<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    exit("Unauthorized");
}

require_once "db_connection.php"; // make sure this connects to your DB

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"];
    $doctor_id = intval($_POST["id"]);

    // Fetch doctor request first
    $stmt = $conn->prepare("SELECT * FROM doctor_requests WHERE id = ?");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $doctor = $result->fetch_assoc();

        if ($action === "approve") {
            // Copy into doctors table
            $insert = $conn->prepare("INSERT INTO doctors (name, email, password, age, gender, blood_group, specialization, degrees, profile_picture, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved')");
            $insert->bind_param(
                "sssisssss",
                $doctor["name"],
                $doctor["email"],
                $doctor["password"],
                $doctor["age"],
                $doctor["gender"],
                $doctor["blood_group"],
                $doctor["specialization"],
                $doctor["degrees"],
                $doctor["profile_picture"]
            );
            $insert->execute();

            // Update status in doctor_requests
            $update = $conn->prepare("UPDATE doctor_requests SET status = 'approved' WHERE id = ?");
            $update->bind_param("i", $doctor_id);
            $update->execute();

            echo "approved";
        } elseif ($action === "reject") {
            $update = $conn->prepare("UPDATE doctor_requests SET status = 'rejected' WHERE id = ?");
            $update->bind_param("i", $doctor_id);
            $update->execute();

            echo "rejected";
        }
    } else {
        echo "not_found";
    }
}
?>
