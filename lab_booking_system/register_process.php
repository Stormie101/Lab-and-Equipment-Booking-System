<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    $prefix = match($role) {
        'student' => 'ST',
        'lecture' => 'LE',
        'instructor' => 'IT',
        'labto' => 'AD',
        default => 'US'
    };

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

    // Insert user with email
    $stmt = $conn->prepare("INSERT INTO users (user_id, username, email, password, role, status) VALUES (?, ?, ?, ?, ?, 'active')");
    $stmt->bind_param("sssss", $user_id, $username, $email, $password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Registration failed.'); window.location.href='register.php';</script>";
    }
}
?>
