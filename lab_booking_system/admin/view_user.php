<?php
include '../config.php';
session_start();
include 'header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'labto') {
    header("Location: ../login.php");
    exit();
}

$userId = $_GET['id'] ?? null;

if (!$userId || strlen($userId) < 2) {
    echo "<p style='color: red;'>Invalid user ID.</p>";
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE User_ID = ?");
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<p style='color: red;'>User not found.</p>";
    exit();
}
?>
<style>
body {
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    padding: 0;
    background: #f4f6f8;
    color: #333;
}

.wrapper {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 220px;
    background-color: #2c3e50;
    color: white;
    padding: 20px;
    flex-shrink: 0;
}

.sidebar h2 {
    font-size: 20px;
    margin-bottom: 30px;
    color: #ecf0f1;
}

.sidebar a {
    display: block;
    color: #ecf0f1;
    text-decoration: none;
    margin-bottom: 15px;
    font-weight: 500;
    padding: 8px 12px;
    border-radius: 6px;
}

.sidebar a:hover {
    background-color: #34495e;
}

.main-content {
    flex-grow: 1;
    padding: 40px;
}

.container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    padding: 30px;
    max-width: 800px;
    margin: auto;
}

.top-right {
    text-align: right;
    margin-bottom: 10px;
}

.top-right a {
    color: #3498db;
    text-decoration: none;
    font-weight: bold;
}

h2 {
    color: #2c3e50;
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 14px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

th {
    background-color: #ecf0f1;
    width: 180px;
}

.badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: bold;
    text-transform: capitalize;
}

.badge.labto { background-color: #3498db; color: white; }
.badge.instructor { background-color: #9b59b6; color: white; }
.badge.lecture { background-color: #f39c12; color: white; }
.badge.student { background-color: #2ecc71; color: white; }

.badge.active { background-color: #2ecc71; color: white; }
.badge.inactive { background-color: #e74c3c; color: white; }

.action-btn {
    display: inline-block;
    padding: 8px 12px;
    margin-top: 20px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.action-edit {
    background-color: #3498db;
    color: white;
}

.action-edit:hover {
    background-color: #2980b9;
}

@media (max-width: 768px) {
    .wrapper {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
        padding: 10px;
    }

    .main-content {
        padding: 20px;
    }

    .container {
        padding: 20px;
    }

    table, th, td {
        font-size: 14px;
    }
}
</style>

<div class="wrapper">
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="labto_dashboard.php">Dashboard</a>
        <a href="labto_labRecord.php">Lab Record</a>
        <a href="labto_equipmentRecord.php">Equipment Record</a>
        <a href="../logout.php">Logout</a>
    </div>
    <div class="main-content container">
        <div class="top-right"><a href="logout.php">Logout</a></div>
        <h2>User Profile</h2>

        <?php if ($user): ?>
<table>
    <tr><th>User ID</th><td><?php echo $user['user_id']; ?></td></tr>
    <tr><th>Username</th><td><?php echo htmlspecialchars($user['username']); ?></td></tr>
    <tr><th>Role</th><td><?php echo htmlspecialchars($user['role']); ?></td></tr>
    <tr><th>Status</th><td><?php echo htmlspecialchars($user['status']); ?></td></tr>
</table>

        <?php else: ?>
            <p style="color: red;">User not found.</p>
        <?php endif; ?>

        <a href="labto_labRecord.php" class="action-btn action-edit" style="margin-top: 20px;">← Back to Lab Record</a>
    </div>
</div>
