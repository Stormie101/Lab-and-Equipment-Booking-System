<?php
include '../config.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'labto') {
    header("Location: ../login.php");
    exit();
}

$bookingId = $_GET['id'] ?? null;
if ($bookingId) {
    $stmt = $conn->prepare("UPDATE booked_equipment SET Status='rejected' WHERE Booking_ID=?");
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
}
header("Location: labto_equipmentRecord.php");
exit();
?>
