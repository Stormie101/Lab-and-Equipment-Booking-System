<?php
include '../config.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'labto') {
    header("Location: ../login.php");
    exit();
}

$labId = $_GET['id'] ?? null;
if (!$labId) {
    echo "Invalid lab ID.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $capacity = intval($_POST['capacity']);
    $date = $_POST['date'];
    $start = $_POST['start'];
    $end = $_POST['end'];

    $stmt = $conn->prepare("UPDATE available_lab SET Name=?, Type=?, Capacity=?, Available_Date=?, Start_Time=?, End_Time=? WHERE Lab_ID=?");
    $stmt->bind_param("ssisssi", $name, $type, $capacity, $date, $start, $end, $labId);
    $stmt->execute();
    header("Location: labto_labRecord.php");
    exit();
}

$result = $conn->query("SELECT * FROM available_lab WHERE Lab_ID=$labId");
$lab = $result->fetch_assoc();
?>

<style>
.edit-form {
    max-width: 600px;
    margin: 60px auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    font-family: 'Segoe UI', sans-serif;
}

.edit-form h2 {
    margin-bottom: 20px;
    color: #2c3e50;
    font-size: 24px;
    text-align: center;
}

.edit-form label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #34495e;
}

.edit-form input {
    width: 100%;
    padding: 12px;
    margin-bottom: 18px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    background-color: #fff;
}

.edit-form input:focus {
    border-color: #3498db;
    outline: none;
}

.edit-form button {
    width: 100%;
    padding: 14px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.edit-form button:hover {
    background-color: #2980b9;
}
</style>

<div class="edit-form">
    <h2>Edit Lab Details</h2>
    <form method="POST">
        <label>Name</label>
        <input name="name" value="<?php echo htmlspecialchars($lab['Name']); ?>" required>

        <label>Type</label>
        <input name="type" value="<?php echo htmlspecialchars($lab['Type']); ?>" required>

        <label>Capacity</label>
        <input name="capacity" type="number" value="<?php echo $lab['Capacity']; ?>" required>

        <label>Date</label>
        <input name="date" type="date" value="<?php echo $lab['Available_Date']; ?>" required>

        <label>Start Time</label>
        <input name="start" type="time" value="<?php echo $lab['Start_Time']; ?>" required>

        <label>End Time</label>
        <input name="end" type="time" value="<?php echo $lab['End_Time']; ?>" required>

        <button type="submit">Update</button>
    </form>
</div>
