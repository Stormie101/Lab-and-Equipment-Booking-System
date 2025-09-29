<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lab_id = $_POST['lab_id'];
    $user_id = $_POST['user_id'];
    $status = 'pending';

    // Fetch lab details from available_lab
    $labQuery = $conn->prepare("
        SELECT Name, Type, Capacity, Available_Date, Start_Time, End_Time
        FROM available_lab
        WHERE Lab_ID = ?
    ");
    $labQuery->bind_param("i", $lab_id);
    $labQuery->execute();
    $labResult = $labQuery->get_result();

    if ($labResult && $labResult->num_rows === 1) {
        $lab = $labResult->fetch_assoc();

        // Begin transaction
        $conn->begin_transaction();

        try {
            // Insert into booked_lab
            $insert = $conn->prepare("
                INSERT INTO booked_lab (Lab_ID, User_ID, Name, Type, Capacity, Booking_Date, Start_Time, End_Time, Status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $insert->bind_param(
                "isssissss",
                $lab_id,
                $user_id,
                $lab['Name'],
                $lab['Type'],
                $lab['Capacity'],
                $lab['Available_Date'],
                $lab['Start_Time'],
                $lab['End_Time'],
                $status
            );
            $insert->execute();

            // Remove from available_lab
            $delete = $conn->prepare("DELETE FROM available_lab WHERE Lab_ID = ?");
            $delete->bind_param("i", $lab_id);
            $delete->execute();

        $conn->commit();
        echo "success";
        exit();


        } catch (Exception $e) {
            $conn->rollback();
            echo "❌ Booking failed. Error: " . $e->getMessage();
        }
    } else {
        echo "❌ Lab not found or already booked.";
    }
}
?>
