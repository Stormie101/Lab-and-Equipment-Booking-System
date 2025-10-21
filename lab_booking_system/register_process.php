<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; // plain text as requested
    $role = $_POST['role'];

    // Role-based prefix
    $prefix = match($role) {
        'student' => 'ST',
        'lecture' => 'LE',
        'instructor' => 'IT',
        'labto' => 'AD',
        default => 'US'
    };

    // Count users by role to generate unique ID
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

    // Role-specific fields
    $student_id = $_POST['student_id'] ?? null;
    $student_name = $_POST['student_name'] ?? null;
    $semester = $_POST['semester'] ?? null;
    $lecture_name = $_POST['lecture_name'] ?? null;
    $department = $_POST['department'] ?? null;

    // Optional: Validate required fields by role
    if ($role === 'student' && (empty($student_id) || empty($student_name) || empty($semester))) {
        echo "<script>alert('Please fill all student fields.'); window.location.href='register.php';</script>";
        exit();
    }

    if ($role === 'lecture' && (empty($lecture_name) || empty($department))) {
        echo "<script>alert('Please fill all lecturer fields.'); window.location.href='register.php';</script>";
        exit();
    }

    // Insert full user record
    $stmt = $conn->prepare("INSERT INTO users (
        user_id, username, email, password, role, status,
        student_id, student_name, semester, lecture_name, department
    ) VALUES (?, ?, ?, ?, ?, 'active', ?, ?, ?, ?, ?)");

$stmt->bind_param("ssssssssss", 
    $user_id, $username, $email, $password, $role,
    $student_id, $student_name, $semester, $lecture_name, $department
);


    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Registration failed.'); window.location.href='register.php';</script>";
    }
}
?>
