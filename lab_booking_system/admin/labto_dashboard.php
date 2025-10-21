<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        margin: 0;
        padding: 0;
        background: #f4f6f8;
        color: #333;
    }

    img{
        max-width: 100%;
        height: 250px;
    }

    .container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h2, h3 {
        color: #2c3e50;
        margin-bottom: 20px;
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

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 12px;
        border-bottom: 1px solid #ddd;
        text-align: left;
    }

    th {
        background-color: #ecf0f1;
    }

    .status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: bold;
        display: inline-block;
    }

    .status.approved {
        background-color: #2ecc71;
        color: white;
    }

    @media (max-width: 600px) {
        .container {
            padding: 10px;
        }

        table, th, td {
            font-size: 13px;
        }
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
}

.dashboard-boxes {
    display: flex;
    gap: 20px;
    margin-top: 30px;
    flex-wrap: wrap;
}

.box {
    flex: 1;
    min-width: 280px;
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    padding: 25px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    transition: transform 0.2s ease;
}

.box:hover {
    transform: translateY(-4px);
}

.box h4 {
    font-size: 18px;
    color: #2c3e50;
    margin-bottom: 10px;
}

.box .count {
    font-size: 36px;
    font-weight: bold;
    color: #3498db;
}

.box .icon {
    font-size: 24px;
    color: #bdc3c7;
    margin-bottom: 10px;
}

</style>


<?php
include '../config.php';
session_start();
// Ensure only admin can access = labto == admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'labto') {
    header("Location: ../login.php");
    exit();
}

include 'header.php';
echo '<div class="wrapper">';
echo '<div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="labto_dashboard.php">Dashboard</a>
        <a href="labto_labRecord.php">Lab Record</a>
        <a href="labto_equipmentRecord.php">Equipment Record</a>
        <a href="labto_profile.php">Profile</a>
        <a href="../logout.php">Logout</a>
      </div>';
echo '<div class="main-content container">';
echo '<div class="top-right"><a href="logout.php">Logout</a></div>';
echo "<h2>Admin Dashboard</h2>";

// Count pending equipment bookings
$equipCountQuery = $conn->query("SELECT COUNT(*) AS total FROM booked_equipment WHERE Status = 'pending'");
$equipCount = $equipCountQuery->fetch_assoc()['total'];

// Count pending lab bookings
$labCountQuery = $conn->query("SELECT COUNT(*) AS total FROM booked_lab WHERE Status = 'pending'");
$labCount = $labCountQuery->fetch_assoc()['total'];

// Count available labs
$labAvailableQuery = $conn->query("SELECT COUNT(*) AS total FROM available_lab");
$labAvailable = $labAvailableQuery->fetch_assoc()['total'];

// Count available equipment
$equipAvailableQuery = $conn->query("SELECT COUNT(*) AS total FROM available_equipment");
$equipAvailable = $equipAvailableQuery->fetch_assoc()['total'];

?>

<div class="dashboard-boxes">
    <div class="box">
        <h4>Pending Equipment Requests</h4>
        <div class="count"><?php echo $equipCount; ?></div>
    </div>
    <div class="box">
        <h4>Pending Lab Bookings</h4>
        <div class="count"><?php echo $labCount; ?></div>
    </div>
</div>
<div class="dashboard-boxes">
        <div class="box">
        <h4>Available Equipment</h4>
        <div class="count"><?php echo $equipAvailable; ?></div>
    </div>
    <div class="box">
        <h4>Available Labs</h4>
        <div class="count"><?php echo $labAvailable; ?></div>
    </div>
<?php
echo "</div>";
echo "</body></html>";
?>
