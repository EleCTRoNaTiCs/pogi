<?php
session_start();
include '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $last_attempt = $_POST['last_attempt'];

    // Prepare delete query to prevent SQL injection
    $query = "DELETE FROM user_progress WHERE user_id = ? AND last_attempt = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $user_id, $last_attempt);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Record deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting record!";
    }

    $stmt->close();
    $conn->close();

    header("Location: index.php");
    exit();
}
?>
