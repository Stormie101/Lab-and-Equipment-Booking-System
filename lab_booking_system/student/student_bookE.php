<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $equipment_id = intval($_POST['equipment_id']);
    $booking_date = $_POST['booking_date'];
    $requested_qty = intval($_POST['quantity']);
    $user_id = $_SESSION['user_id'];

    // Validate inputs
    if ($requested_qty < 1 || strtotime($booking_date) < strtotime(date('Y-m-d'))) {
        echo "⚠️ Invalid quantity or booking date.";
        exit();
    }

    // Fetch equipment details
    $equipQuery = $conn->prepare("
        SELECT Name, Type, Quantity
        FROM available_equipment
        WHERE Equipment_ID = ?
    ");
    $equipQuery->bind_param("i", $equipment_id);
    $equipQuery->execute();
    $result = $equipQuery->get_result();

    if ($result->num_rows === 0) {
        echo "❌ Equipment not found or already booked.";
        exit();
    }

    $equipment = $result->fetch_assoc();
    $available_qty = intval($equipment['Quantity']);

    if ($requested_qty > $available_qty) {
        echo "❌ Not enough equipment available. Only $available_qty left.";
        exit();
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert into booked_equipment
        $insert = $conn->prepare("
            INSERT INTO booked_equipment (Equipment_ID, User_ID, Name, Type, Quantity, Booking_Date, Status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $status = 'pending';
        $insert->bind_param(
            "isssiss",
            $equipment_id,
            $user_id,
            $equipment['Name'],
            $equipment['Type'],
            $requested_qty,
            $booking_date,
            $status
        );
        $insert->execute();

        if ($requested_qty === $available_qty) {
            // Remove equipment if fully booked
            $delete = $conn->prepare("DELETE FROM available_equipment WHERE Equipment_ID = ?");
            $delete->bind_param("i", $equipment_id);
            $delete->execute();
        } else {
            // Update remaining quantity
            $update = $conn->prepare("UPDATE available_equipment SET Quantity = Quantity - ? WHERE Equipment_ID = ?");
            $update->bind_param("ii", $requested_qty, $equipment_id);
            $update->execute();
        }

        $conn->commit();
        header("Location: student_bookESuc.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "❌ Booking failed. Please try again.";
    }
}
?>
