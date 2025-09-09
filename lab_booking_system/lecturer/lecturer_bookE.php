<?php
session_start();
include '../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecture') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $equipment_id = intval($_POST['equipment_id']);
    $booking_date = $_POST['booking_date'];
    $booking_time = $_POST['booking_time'];
    $requested_qty = intval($_POST['quantity']);
    $user_id = $_SESSION['user_id'];

    // Validate inputs
    if ($requested_qty < 1 || strtotime($booking_date . ' ' . $booking_time) < time()) {
        echo "⚠️ Invalid quantity or booking time.";
        exit();
    }

    // Check available quantity
    $checkQty = $conn->prepare("SELECT Quantity FROM lab_equipment WHERE Equipment_ID = ?");
    $checkQty->bind_param("i", $equipment_id);
    $checkQty->execute();
    $result = $checkQty->get_result();

    if ($result->num_rows === 0) {
        echo "❌ Equipment not found.";
        exit();
    }

    $row = $result->fetch_assoc();
    $available_qty = intval($row['Quantity']);

    if ($requested_qty > $available_qty) {
        echo "❌ Not enough equipment available. Only $available_qty left.";
        exit();
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert booking with status 'pending'
    $insert = $conn->prepare("INSERT INTO equipment_booking (User_ID, Equipment_ID, Booking_Date, Booking_Time, Quantity, Status) VALUES (?, ?, ?, ?, ?, ?)");
    $status = 'pending';
    $insert->bind_param("ssssis", $user_id, $equipment_id, $booking_date, $booking_time, $requested_qty, $status);
    $insert->execute();


        // Update equipment quantity
        $update = $conn->prepare("UPDATE lab_equipment SET Quantity = Quantity - ? WHERE Equipment_ID = ?");
        $update->bind_param("ii", $requested_qty, $equipment_id);
        $update->execute();

        $conn->commit();
        header("Location: lecturer_bookESuc.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "❌ Booking failed. Please try again.";
    }
}
?>
