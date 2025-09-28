<?php
include '../config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'labto') {
    header("Location: ../login.php");
    exit();
}

$bookingId = $_GET['id'] ?? null;

if (!$bookingId || !is_numeric($bookingId)) {
    echo "<p style='color: red;'>Invalid booking ID.</p>";
    exit();
}

// Fetch booking details
$stmt = $conn->prepare("SELECT * FROM booked_equipment WHERE Booking_ID = ?");
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $booking = $result->fetch_assoc();

    $conn->begin_transaction();

    try {
        // 1. Insert into available_equipment
        $insert = $conn->prepare("
            INSERT INTO available_equipment (Name, Type, Quantity, Available_Date)
            VALUES (?, ?, ?, ?)
        ");
        $insert->bind_param(
            "ssis",
            $booking['Name'],
            $booking['Type'],
            $booking['Quantity'],
            $booking['Booking_Date']
        );
        $insert->execute();

        // 2. Delete from booked_equipment
        $delete = $conn->prepare("DELETE FROM booked_equipment WHERE Booking_ID = ?");
        $delete->bind_param("i", $bookingId);
        $delete->execute();

        $conn->commit();
        echo "<script>alert('Booking rejected and equipment returned to availability.'); window.location.href = 'labto_equipmentRecord.php';</script>";
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        echo "<p style='color: red;'>❌ Rejection failed. Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Booking not found.</p>";
}
?>
