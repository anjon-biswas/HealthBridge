<?php
require_once 'db_connect.php';

$id = $_GET['id'];
$role = $_GET['role'];

$table = ($role === 'doctor') ? 'doctors' : 'patients';

$sql = "DELETE FROM $table WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('User deleted successfully'); window.location.href='admin_dashboard.php';</script>";
} else {
    echo "Failed to delete user.";
}

$conn->close();
?>
