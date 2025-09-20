<?php
include '../config.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'labto') {
    header("Location: ../login.php");
    exit();
}

$equipId = $_GET['id'] ?? null;
if (!$equipId) {
    echo "Invalid equipment ID.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $quantity = intval($_POST['quantity']);
    $date = $_POST['date'];

    $stmt = $conn->prepare("UPDATE available_equipment SET Name=?, Type=?, Quantity=?, Available_Date=? WHERE Equipment_ID=?");
    $stmt->bind_param("ssisi", $name, $type, $quantity, $date, $equipId);
    $stmt->execute();
    header("Location: labto_equipmentRecord.php");
    exit();
}

$result = $conn->query("SELECT * FROM available_equipment WHERE Equipment_ID=$equipId");
$equip = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Equipment</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f8; margin: 0; padding: 0; }
        .form-container { max-width: 600px; margin: 60px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        h2 { text-align: center; color: #2c3e50; margin-bottom: 30px; }
        label { display: block; margin-bottom: 8px; font-weight: 500; color: #34495e; }
        input { width: 100%; padding: 12px; margin-bottom: 18px; border: 1px solid #ccc; border-radius: 8px; font-size: 15px; background-color: #fff; }
        input:focus { border-color: #3498db; outline: none; }
        button { width: 100%; padding: 14px; background-color: #3498db; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 500; cursor: pointer; transition: background-color 0.3s ease; }
        button:hover { background-color: #2980b9; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #3498db; text-decoration: none; font-weight: 500; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Equipment</h2>
        <form method="POST">
            <label>Equipment Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($equip['Name']); ?>" required>

            <label>Equipment Type</label>
            <input type="text" name="type" value="<?php echo htmlspecialchars($equip['Type']); ?>" required>

            <label>Quantity</label>
            <input type="number" name="quantity" value="<?php echo $equip['Quantity']; ?>" required>

            <label>Available Date</label>
            <input type="date" name="date" value="<?php echo $equip['Available_Date']; ?>" required>

            <button type="submit">Update Equipment</button>
        </form>
        <a href="labto_equipmentRecord.php" class="back-link">← Back to Equipment Record</a>
    </div>
</body>
</html>
