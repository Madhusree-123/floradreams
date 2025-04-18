<?php
session_start();
include 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Get user details
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION["user_id"] = $id;
        $_SESSION["username"] = $username;
        echo "<script>alert('Login successful!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Invalid username or password!'); window.location.href='index.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
