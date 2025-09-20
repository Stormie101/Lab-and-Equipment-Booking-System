<?php
include '../config.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'labto') {
    header("Location: ../login.php");
    exit();
}

$labId = $_GET['id'] ?? null;
if ($labId) {
    $conn->query("DELETE FROM available_lab WHERE Lab_ID=$labId");
}
header("Location: labto_labRecord.php");
exit();
