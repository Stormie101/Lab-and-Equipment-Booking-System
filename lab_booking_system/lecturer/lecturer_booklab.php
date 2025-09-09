<?php
session_start();
include '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schedule_id = $_POST['schedule_id']; // This is Lab_ID
    $user_id = $_POST['user_id'];

    $booking_date = $_POST['booking_date'];
    $status = 'pending'; // You said no status input, so we set it internally

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert booking into lab_booking
$insert = $conn->prepare("
    INSERT INTO lab_booking (Booking_Date, Status, User_ID, Schedule_ID)
    VALUES (?, ?, ?, ?)
");

        $insert->bind_param("ssss", $booking_date, $status, $user_id, $schedule_id);
        $insert->execute();

        // Remove the booked lab from lab table
        $delete = $conn->prepare("DELETE FROM lab WHERE Lab_ID = ?");
        $delete->bind_param("s", $schedule_id);
        $delete->execute();

        $conn->commit();
        header("Location: lecturer_bookESuc.php");
        exit();

    } catch (Exception $e) {
    $conn->rollback();
    echo "❌ Booking failed. Error: " . $e->getMessage();
}
}
?>
