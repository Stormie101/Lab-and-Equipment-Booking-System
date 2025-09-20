<?php
include '../config.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'labto') {
    header("Location: ../login.php");
    exit();
}

$bookingId = $_GET['id'] ?? null;
if ($bookingId) {
    $conn->query("UPDATE booked_lab SET Status='approved' WHERE Booking_ID=$bookingId");
}
header("Location: labto_labRecord.php");
exit();
