<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password']; // stored as plain text
    $role = $_POST['role'];

    // Generate user_id prefix
    $prefix = match($role) {
        'student' => 'ST',
        'lecture' => 'LE',
        'instructor' => 'IT',
        'labto' => 'AD',
        default => 'US'
    };

    // Count existing users with this role
    $countQuery = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE role = ?");
    $countQuery->bind_param("s", $role);
    $countQuery->execute();
    $result = $countQuery->get_result()->fetch_assoc();
    $number = $result['total'] + 1;

    $user_id = $prefix . $number;

    // Check for duplicate username
    $check = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo "<script>alert('Username already exists.'); window.location.href='register.php';</script>";
        exit();
    }

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (user_id, username, password, role, status) VALUES (?, ?, ?, ?, 'active')");
    $stmt->bind_param("ssss", $user_id, $username, $password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Registration failed.'); window.location.href='register.php';</script>";
    }
}
?>
