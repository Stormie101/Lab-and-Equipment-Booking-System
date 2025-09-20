<?php
include '../config.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'labto') {
    header("Location: ../login.php");
    exit();
}

$equipId = $_GET['id'] ?? null;
if ($equipId) {
    $stmt = $conn->prepare("DELETE FROM available_equipment WHERE Equipment_ID=?");
    $stmt->bind_param("i", $equipId);
    $stmt->execute();
}
header("Location: labto_equipmentRecord.php");
exit();
?>
