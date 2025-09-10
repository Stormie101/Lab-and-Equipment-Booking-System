<?php
include '../config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$lab_id = $_POST['lab_id'];

// Get Student_ID
$res = $conn->query("SELECT Student_ID FROM student WHERE user_id='$user_id'");
$row = $res->fetch_assoc();
$student_id = $row['Student_ID'];

// Optional: Check if already booked
$check = $conn->query("SELECT * FROM lab_booking WHERE User_ID='$student_id' AND Lab_ID='$lab_id'");
if ($check->num_rows > 0) {
    echo "You have already booked this lab.";
    exit();
}

// Insert booking
$insert = $conn->query("INSERT INTO lab_booking (User_ID, Lab_ID, Booking_Date) VALUES ('$student_id', '$lab_id', NOW())");

if ($insert) {
    echo "Lab booked successfully!";
} else {
    echo "Error booking lab.";
}
?>
